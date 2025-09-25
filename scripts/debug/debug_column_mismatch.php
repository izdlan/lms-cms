<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING COLUMN MISMATCH ===\n\n";

$localFilePath = 'Enrollment OEM.xlsx';

if (!file_exists($localFilePath)) {
    echo "❌ Local file '{$localFilePath}' not found\n";
    exit;
}

// Test with IUC LMS sheet
$sheetName = 'IUC LMS';
echo "=== ANALYZING SHEET: {$sheetName} ===\n";

try {
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    $reader->setLoadSheetsOnly([$sheetName]);
    $spreadsheet = $reader->load($localFilePath);
    $worksheet = $spreadsheet->getSheetByName($sheetName);
    
    if (!$worksheet) {
        echo "❌ Sheet '{$sheetName}' not found\n";
        exit;
    }
    
    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
    
    echo "Sheet dimensions: {$highestRow} rows x {$highestColumn} columns (index: {$highestColumnIndex})\n\n";
    
    // Check first 10 rows for column counts
    echo "Column count analysis for first 10 rows:\n";
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
    
    // Check what the import logic expects
    echo "\n=== TESTING IMPORT LOGIC ===\n";
    
    // Convert to array like the import does
    $allRows = [];
    for ($row = 1; $row <= $highestRow; $row++) {
        $rowData = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            $rowData[] = $cellValue;
        }
        $allRows[] = $rowData;
    }
    
    $rows = collect($allRows);
    echo "Total rows loaded: " . $rows->count() . "\n";
    
    // Test header detection
    $firstRow = $rows->first();
    $firstRowArray = is_array($firstRow) ? $firstRow : $firstRow->toArray();
    $firstRowText = implode(' ', array_filter($firstRowArray, function($cell) {
        return !empty(trim($cell));
    }));
    
    echo "First row text: '{$firstRowText}'\n";
    echo "First row array length: " . count($firstRowArray) . "\n";
    
    // Check header keywords
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
        
        // Process headers
        $header = collect($firstRow)->map(function($h) {
            $h = trim($h);
            if (substr($h, 0, 3) === "\xEF\xBB\xBF") {
                $h = substr($h, 3);
            }
            return trim($h);
        })->toArray();
        
        // Filter out empty headers
        $header = array_filter($header, function($h) {
            return !empty(trim($h));
        });
        
        echo "Headers after filtering: " . count($header) . "\n";
        echo "Headers: " . implode(' | ', array_slice($header, 0, 5)) . "\n";
        
        // Skip header row
        $rows = $rows->slice(1);
        echo "Rows after skipping header: " . $rows->count() . "\n";
        
        // Test first few data rows
        echo "\n=== TESTING DATA ROWS ===\n";
        foreach ($rows->take(3) as $index => $row) {
            $rowArray = is_array($row) ? $row : $row->toArray();
            $nonEmptyCells = array_filter($rowArray, function($cell) {
                return !empty(trim($cell));
            });
            
            echo "Data row " . ($index + 1) . ": Total columns = " . count($rowArray) . ", Non-empty = " . count($nonEmptyCells) . "\n";
            
            if (count($rowArray) !== count($header)) {
                echo "  ❌ COLUMN MISMATCH! Headers: " . count($header) . ", Row: " . count($rowArray) . "\n";
            } else {
                echo "  ✅ Column count matches\n";
            }
        }
        
    } else {
        echo "❌ No header keywords found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUG COMPLETED ===\n";
