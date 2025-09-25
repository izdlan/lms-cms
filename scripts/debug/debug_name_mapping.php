<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING NAME MAPPING ISSUE ===\n\n";

$localFilePath = 'Enrollment OEM.xlsx';

// Test DHU LMS sheet specifically
$sheetName = 'DHU LMS';
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
    
    echo "Sheet dimensions: {$highestRow} rows x {$highestColumn} columns (index: {$highestColumnIndex})\n";
    
    // Get all rows
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
    
    // Find header row (should be row 5 for DHU LMS)
    $headerRowIndex = 4; // Row 5 (0-indexed)
    $headerRow = $rows->skip($headerRowIndex)->first();
    
    echo "\nHeader row (row 5):\n";
    $header = collect($headerRow)->map(function($h) {
        $h = trim($h);
        if (substr($h, 0, 3) === "\xEF\xBB\xBF") {
            $h = substr($h, 3);
        }
        return trim($h);
    })->toArray();
    
    foreach ($header as $index => $column) {
        echo ($index + 1) . ": '{$column}'\n";
    }
    
    // Map headers
    $mappedHeader = [];
    foreach ($header as $index => $headerName) {
        $headerName = strtolower(trim($headerName));
        
        if (empty($headerName)) {
            $mappedHeader[$index] = 'empty_' . $index;
        } elseif ((strpos($headerName, 'name') !== false || strpos($headerName, 'learners') !== false) && 
                  strpos($headerName, 'programme') === false && 
                  strpos($headerName, 'program') === false &&
                  strpos($headerName, 'progame') === false &&
                  strpos($headerName, 'programe') === false) {
            $mappedHeader[$index] = 'name';
        } elseif (strpos($headerName, 'address') !== false) {
            $mappedHeader[$index] = 'address';
        } elseif (strpos($headerName, 'ic') !== false || strpos($headerName, 'passport') !== false) {
            $mappedHeader[$index] = 'ic/passport';
        } elseif (strpos($headerName, 'email') !== false) {
            $mappedHeader[$index] = 'email';
        } elseif (strpos($headerName, 'contact') !== false || strpos($headerName, 'phone') !== false) {
            $mappedHeader[$index] = 'contact no.';
        } elseif (strpos($headerName, 'programme name') !== false || strpos($headerName, 'program name') !== false) {
            $mappedHeader[$index] = 'programme name';
        } else {
            $mappedHeader[$index] = 'unknown_' . $index;
        }
    }
    
    echo "\nMapped headers:\n";
    foreach ($mappedHeader as $index => $mapped) {
        echo ($index + 1) . ": '{$mapped}' (from '{$header[$index]}')\n";
    }
    
    // Test first few data rows
    echo "\nFirst few data rows:\n";
    $dataRows = $rows->slice($headerRowIndex + 1);
    foreach ($dataRows->take(5) as $index => $row) {
        $rowArray = is_array($row) ? $row : $row->toArray();
        
        echo "\nRow " . ($index + 1) . " (actual row " . ($headerRowIndex + $index + 2) . "):\n";
        
        // Map data
        $data = [];
        for ($i = 0; $i < count($rowArray); $i++) {
            $key = $mappedHeader[$i] ?? 'unknown_' . $i;
            $value = trim($rowArray[$i] ?? '');
            $data[$key] = empty($value) ? '-' : $value;
        }
        
        echo "  Name: '{$data['name']}'\n";
        echo "  IC/Passport: '{$data['ic/passport']}'\n";
        echo "  Email: '{$data['email']}'\n";
        echo "  Programme: '{$data['programme name']}'\n";
        
        // Show raw data for first few columns
        echo "  Raw data (first 5 columns): " . implode(' | ', array_slice($rowArray, 0, 5)) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error analyzing {$sheetName}: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETED ===\n";
