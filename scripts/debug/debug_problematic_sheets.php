<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING PROBLEMATIC SHEETS ===\n\n";

$localFilePath = 'Enrollment OEM.xlsx';

// Test problematic sheets
$problematicSheets = ['DHU LMS', 'VIVA-IUC LMS', 'TVET LMS'];

foreach ($problematicSheets as $sheetName) {
    echo "=== ANALYZING SHEET: {$sheetName} ===\n";
    
    try {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly([$sheetName]);
        $spreadsheet = $reader->load($localFilePath);
        $worksheet = $spreadsheet->getSheetByName($sheetName);
        
        if (!$worksheet) {
            echo "❌ Sheet '{$sheetName}' not found\n\n";
            continue;
        }
        
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        echo "Sheet dimensions: {$highestRow} rows x {$highestColumn} columns (index: {$highestColumnIndex})\n";
        
        // Check first 10 rows for column counts and content
        echo "\nFirst 10 rows analysis:\n";
        for ($row = 1; $row <= min(10, $highestRow); $row++) {
            $rowData = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                $rowData[] = $cellValue;
            }
            
            $nonEmptyCells = array_filter($rowData, function($cell) {
                return !empty(trim($cell));
            });
            
            echo "Row {$row}: Total columns = {$highestColumnIndex}, Non-empty = " . count($nonEmptyCells) . "\n";
            
            if ($row <= 3) {
                echo "  First 5 values: " . implode(' | ', array_slice($rowData, 0, 5)) . "\n";
            }
        }
        
        // Check if headers are on row 1
        echo "\nHeader analysis:\n";
        $firstRowData = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellValue = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
            $firstRowData[] = $cellValue;
        }
        
        $firstRowText = implode(' ', array_filter($firstRowData, function($cell) {
            return !empty(trim($cell));
        }));
        
        echo "First row text: '{$firstRowText}'\n";
        
        $headerKeywords = ['name', 'ic', 'passport', 'email', 'address', 'contact', 'student', 'programme'];
        $hasHeaderKeywords = false;
        foreach ($headerKeywords as $keyword) {
            if (stripos($firstRowText, $keyword) !== false) {
                echo "✅ Found header keyword: '{$keyword}'\n";
                $hasHeaderKeywords = true;
            }
        }
        
        if ($hasHeaderKeywords) {
            echo "✅ Headers detected on row 1\n";
        } else {
            echo "❌ No header keywords found on row 1\n";
            
            // Check if headers might be on a different row
            echo "\nChecking other rows for headers:\n";
            for ($row = 2; $row <= min(5, $highestRow); $row++) {
                $rowData = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $rowData[] = $cellValue;
                }
                
                $rowText = implode(' ', array_filter($rowData, function($cell) {
                    return !empty(trim($cell));
                }));
                
                $hasKeywords = false;
                foreach ($headerKeywords as $keyword) {
                    if (stripos($rowText, $keyword) !== false) {
                        $hasKeywords = true;
                        break;
                    }
                }
                
                if ($hasKeywords) {
                    echo "✅ Found headers on row {$row}: '{$rowText}'\n";
                    break;
                } else {
                    echo "Row {$row}: No headers found\n";
                }
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Error analyzing {$sheetName}: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n\n";
}

echo "=== DEBUG COMPLETED ===\n";
