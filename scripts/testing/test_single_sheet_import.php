<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING SINGLE SHEET IMPORT ===\n\n";

use App\Services\OneDriveExcelImportService;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;

// Clear existing students
User::where('role', 'student')->delete();
echo "Cleared existing students\n";

// Create OneDrive service instance
$onedriveService = new OneDriveExcelImportService();

// Override URL with working OneDrive URL
$reflection = new ReflectionClass($onedriveService);
$urlProperty = $reflection->getProperty('onedriveUrl');
$urlProperty->setAccessible(true);
$urlProperty->setValue($onedriveService, 'https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw&download=1');

// Download the file
echo "Downloading file...\n";
$downloadMethod = $reflection->getMethod('downloadExcelFile');
$downloadMethod->setAccessible(true);
$downloadResult = $downloadMethod->invoke($onedriveService);

if (!$downloadResult['success']) {
    echo "❌ Failed to download file: " . $downloadResult['error'] . "\n";
    exit;
}

$tempFilePath = storage_path('app/temp_enrollment.xlsx');
echo "✅ File downloaded: {$tempFilePath}\n";

// Test with just IUC LMS sheet
$sheetName = 'IUC LMS';
echo "=== TESTING SHEET: {$sheetName} ===\n";

try {
    $import = new StudentsImport();
    $import->setCurrentSheet($sheetName);
    
    echo "Starting import...\n";
    
    // Enable detailed logging
    \Illuminate\Support\Facades\Log::info('=== STARTING IMPORT TEST ===');
    
    Excel::import($import, $tempFilePath);
    
    $stats = $import->getStats();
    echo "\nImport Results:\n";
    echo "Created: " . $stats['created'] . "\n";
    echo "Updated: " . $stats['updated'] . "\n";
    echo "Errors: " . $stats['errors'] . "\n";
    
    $errorDetails = $import->getErrorDetails();
    if (!empty($errorDetails)) {
        echo "\nError Details:\n";
        foreach ($errorDetails as $index => $error) {
            echo "Error " . ($index + 1) . ": " . $error['message'] . "\n";
            if (!empty($error['data'])) {
                echo "  Data: " . json_encode($error['data']) . "\n";
            }
        }
    }
    
    // Check final student count
    $finalCount = User::where('role', 'student')->count();
    echo "\nFinal student count: {$finalCount}\n";
    
    if ($finalCount > 0) {
        echo "✅ SUCCESS: Students were imported!\n";
        $sampleStudents = User::where('role', 'student')->take(3)->get();
        foreach ($sampleStudents as $student) {
            echo "  - {$student->name} ({$student->ic}) - {$student->source_sheet}\n";
        }
    } else {
        echo "❌ No students were imported\n";
    }
    
} catch (Exception $e) {
    echo "❌ Import error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} finally {
    // Clean up
    if (file_exists($tempFilePath)) {
        unlink($tempFilePath);
        echo "\n✅ Temp file cleaned up\n";
    }
}

echo "\n=== TEST COMPLETED ===\n";
