<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING MANUAL IMPORT WITH LOCAL FILE ===\n\n";

use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;

// Clear existing students
User::where('role', 'student')->delete();
echo "Cleared existing students\n";

$localFilePath = 'Enrollment OEM.xlsx';

if (!file_exists($localFilePath)) {
    echo "❌ Local file '{$localFilePath}' not found\n";
    exit;
}

echo "✅ Local file found: {$localFilePath}\n";
echo "File size: " . number_format(filesize($localFilePath)) . " bytes\n\n";

// Test with IUC LMS sheet
$sheetName = 'IUC LMS';
echo "=== TESTING SHEET: {$sheetName} ===\n";

try {
    $import = new StudentsImport();
    $import->setCurrentSheet($sheetName);
    
    echo "Starting manual import...\n";
    
    // Use the correct method to import a specific sheet
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    $reader->setLoadSheetsOnly([$sheetName]);
    $spreadsheet = $reader->load($localFilePath);
    $worksheet = $spreadsheet->getSheetByName($sheetName);
    
    if ($worksheet) {
        $import->collection(collect($worksheet->toArray()));
    } else {
        throw new \Exception("Sheet '{$sheetName}' not found");
    }
    
    $stats = $import->getStats();
    echo "\nManual Import Results:\n";
    echo "Created: " . $stats['created'] . "\n";
    echo "Updated: " . $stats['updated'] . "\n";
    echo "Errors: " . $stats['errors'] . "\n";
    
    $errorDetails = $import->getErrorDetails();
    if (!empty($errorDetails)) {
        echo "\nError Details:\n";
        foreach (array_slice($errorDetails, 0, 5) as $index => $error) {
            echo "Error " . ($index + 1) . ": " . $error['message'] . "\n";
        }
        if (count($errorDetails) > 5) {
            echo "... and " . (count($errorDetails) - 5) . " more errors\n";
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
}

echo "\n=== TEST COMPLETED ===\n";
