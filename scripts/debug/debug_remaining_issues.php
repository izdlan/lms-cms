<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING REMAINING ISSUES ===\n\n";

use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;

$localFilePath = 'Enrollment OEM.xlsx';

// Test remaining problematic sheets
$problematicSheets = ['VIVA-IUC LMS', 'TVET LMS'];

foreach ($problematicSheets as $sheetName) {
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
            foreach (array_slice($errorDetails, 0, 3) as $index => $error) {
                echo "Error " . ($index + 1) . ": " . $error['message'] . "\n";
                if (!empty($error['data'])) {
                    echo "  Data: " . json_encode(array_slice($error['data'], 0, 5)) . "\n";
                }
            }
            if (count($errorDetails) > 3) {
                echo "... and " . (count($errorDetails) - 3) . " more errors\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Import error for {$sheetName}: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n\n";
}

echo "=== DEBUG COMPLETED ===\n";
