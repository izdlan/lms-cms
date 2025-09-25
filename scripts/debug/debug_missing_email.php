<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING MISSING EMAIL ISSUE ===\n\n";

use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;

$localFilePath = 'Enrollment OEM.xlsx';

// Test DHU LMS sheet specifically
$sheetName = 'DHU LMS';
echo "=== TESTING SHEET: {$sheetName} ===\n";

try {
    $import = new StudentsImport();
    $import->setCurrentSheet($sheetName);
    
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
    echo "Created: " . $stats['created'] . "\n";
    echo "Updated: " . $stats['updated'] . "\n";
    echo "Errors: " . $stats['errors'] . "\n";
    
    $errorDetails = $import->getErrorDetails();
    if (!empty($errorDetails)) {
        echo "\nError Details:\n";
        foreach (array_slice($errorDetails, 0, 5) as $index => $error) {
            echo "Error " . ($index + 1) . ": " . $error['message'] . "\n";
            if (!empty($error['data'])) {
                echo "  Data: " . json_encode(array_slice($error['data'], 0, 5)) . "\n";
            }
        }
        if (count($errorDetails) > 5) {
            echo "... and " . (count($errorDetails) - 5) . " more errors\n";
        }
    }
    
    // Check if MOHD NIZAM was imported
    $nizam = User::where('name', 'LIKE', '%NIZAM%')->first();
    if ($nizam) {
        echo "\n✅ MOHD NIZAM found in database:\n";
        echo "  Name: {$nizam->name}\n";
        echo "  IC: {$nizam->ic}\n";
        echo "  Email: {$nizam->email}\n";
        echo "  Source: {$nizam->source_sheet}\n";
    } else {
        echo "\n❌ MOHD NIZAM not found in database\n";
    }
    
} catch (Exception $e) {
    echo "❌ Import error: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETED ===\n";
