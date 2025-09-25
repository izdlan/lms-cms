<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DETAILED IMPORT DEBUG ===\n\n";

use App\Services\OneDriveExcelImportService;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

// Create OneDrive service instance
$onedriveService = new OneDriveExcelImportService();

// Override URL with working OneDrive URL
$reflection = new ReflectionClass($onedriveService);
$urlProperty = $reflection->getProperty('onedriveUrl');
$urlProperty->setAccessible(true);
$urlProperty->setValue($onedriveService, 'https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw&download=1');

// Download the file
$downloadMethod = $reflection->getMethod('downloadExcelFile');
$downloadMethod->setAccessible(true);
$downloadResult = $downloadMethod->invoke($onedriveService);

if (!$downloadResult['success']) {
    echo "❌ Failed to download file: " . $downloadResult['error'] . "\n";
    exit;
}

$tempFilePath = storage_path('app/temp_enrollment.xlsx');
echo "✅ File downloaded: {$tempFilePath}\n";
echo "File size: " . number_format(filesize($tempFilePath)) . " bytes\n\n";

// Test with IUC LMS sheet
$sheetName = 'IUC LMS';
echo "=== TESTING SHEET: {$sheetName} ===\n";

try {
    $import = new StudentsImport();
    $import->setCurrentSheet($sheetName);
    
    echo "Starting import...\n";
    Excel::import($import, $tempFilePath, $sheetName);
    
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

echo "\n=== DEBUG COMPLETED ===\n";
