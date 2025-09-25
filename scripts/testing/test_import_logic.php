<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING IMPORT LOGIC ===\n\n";

use App\Services\OneDriveExcelImportService;
use Illuminate\Support\Facades\Http;

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

// Test with IUC LMS sheet (which has data)
$sheetName = 'IUC LMS';
echo "Testing sheet: {$sheetName}\n\n";

try {
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    $spreadsheet = $reader->load($tempFilePath);
    $worksheet = $spreadsheet->getSheetByName($sheetName);
    
    if (!$worksheet) {
        echo "❌ Sheet '{$sheetName}' not found\n";
        exit;
    }
    
    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();
    
    echo "Sheet dimensions: {$highestRow} rows x {$highestColumn} columns\n\n";
    
    // Check first 10 rows to find headers
    echo "First 10 rows:\n";
    for ($row = 1; $row <= min(10, $highestRow); $row++) {
        $rowData = [];
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $cellValue = $worksheet->getCell($col . $row)->getValue();
            $rowData[] = $cellValue;
        }
        
        $nonEmptyCells = array_filter($rowData, function($cell) {
            return !empty(trim($cell));
        });
        
        echo "Row {$row}: " . count($nonEmptyCells) . " non-empty cells\n";
        if (count($nonEmptyCells) > 0) {
            echo "  Data: " . implode(' | ', array_slice($rowData, 0, 5)) . "\n";
        }
        echo "\n";
    }
    
    // Test the current import logic (skip first 6 rows)
    echo "=== TESTING CURRENT IMPORT LOGIC ===\n";
    echo "Skipping first 6 rows, starting from row 7...\n";
    
    $headerRow = 7;
    if ($headerRow <= $highestRow) {
        $rowData = [];
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $cellValue = $worksheet->getCell($col . $headerRow)->getValue();
            $rowData[] = $cellValue;
        }
        
        $nonEmptyCells = array_filter($rowData, function($cell) {
            return !empty(trim($cell));
        });
        
        echo "Row {$headerRow} (header row): " . count($nonEmptyCells) . " non-empty cells\n";
        if (count($nonEmptyCells) > 0) {
            echo "  Headers: " . implode(' | ', array_slice($rowData, 0, 5)) . "\n";
        } else {
            echo "  ❌ No headers found on row {$headerRow}\n";
        }
    } else {
        echo "❌ Row {$headerRow} doesn't exist (max row: {$highestRow})\n";
    }
    
    // Test alternative logic (headers on row 1)
    echo "\n=== TESTING ALTERNATIVE LOGIC ===\n";
    echo "Checking if headers are on row 1...\n";
    
    $headerRow = 1;
    $rowData = [];
    for ($col = 'A'; $col <= $highestColumn; $col++) {
        $cellValue = $worksheet->getCell($col . $headerRow)->getValue();
        $rowData[] = $cellValue;
    }
    
    $nonEmptyCells = array_filter($rowData, function($cell) {
        return !empty(trim($cell));
    });
    
    echo "Row {$headerRow} (potential header row): " . count($nonEmptyCells) . " non-empty cells\n";
    if (count($nonEmptyCells) > 0) {
        echo "  Headers: " . implode(' | ', array_slice($rowData, 0, 5)) . "\n";
        
        // Check if this looks like headers
        $headerKeywords = ['name', 'ic', 'passport', 'email', 'address', 'contact'];
        $hasHeaderKeywords = false;
        foreach ($rowData as $cell) {
            $cellLower = strtolower(trim($cell));
            foreach ($headerKeywords as $keyword) {
                if (strpos($cellLower, $keyword) !== false) {
                    $hasHeaderKeywords = true;
                    break 2;
                }
            }
        }
        
        if ($hasHeaderKeywords) {
            echo "  ✅ This looks like a header row!\n";
        } else {
            echo "  ❌ This doesn't look like headers\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} finally {
    // Clean up
    if (file_exists($tempFilePath)) {
        unlink($tempFilePath);
        echo "\n✅ Temp file cleaned up\n";
    }
}

echo "\n=== TEST COMPLETED ===\n";
