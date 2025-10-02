<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING STUDENT DATA MAPPING ===\n\n";

$localFilePath = 'storage/app/students/Enrollment OEM.xlsx';

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
    
    // Get all rows
    $allRows = [];
    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
    
    for ($row = 1; $row <= $highestRow; $row++) {
        $rowData = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            $rowData[] = $cellValue;
        }
        $allRows[] = $rowData;
    }
    
    $rows = collect($allRows);
    
    // Get headers
    $firstRow = $rows->first();
    $header = collect($firstRow)->map(function($h) {
        $h = trim($h);
        if (substr($h, 0, 3) === "\xEF\xBB\xBF") {
            $h = substr($h, 3);
        }
        return trim($h);
    })->toArray();
    
    echo "Headers: " . implode(' | ', array_slice($header, 0, 10)) . "\n\n";
    
    // Map headers
    $mappedHeader = [];
    foreach ($header as $index => $headerName) {
        $headerName = strtolower(trim($headerName));
        
        if (empty($headerName)) {
            $mappedHeader[$index] = 'empty_' . $index;
        } elseif (strpos($headerName, 'name') !== false && strpos($headerName, 'learners') === false && strpos($headerName, 'programme') === false) {
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
    
    echo "Mapped headers: " . json_encode(array_slice($mappedHeader, 0, 10)) . "\n\n";
    
    // Skip header row
    $rows = $rows->slice(1);
    
    // Test first few data rows
    echo "=== TESTING DATA MAPPING ===\n";
    foreach ($rows->take(5) as $index => $row) {
        $rowArray = is_array($row) ? $row : $row->toArray();
        
        echo "\nRow " . ($index + 1) . ":\n";
        
        // Map data using the mapped headers
        $data = [];
        foreach ($mappedHeader as $colIndex => $mappedName) {
            if (isset($rowArray[$colIndex])) {
                $data[$mappedName] = trim($rowArray[$colIndex]);
            }
        }
        
        echo "  Name: '{$data['name']}'\n";
        echo "  IC/Passport: '{$data['ic/passport']}'\n";
        echo "  Email: '{$data['email']}'\n";
        echo "  Programme: '{$data['programme name']}'\n";
        
        // Check if this looks like valid student data
        $hasName = !empty($data['name']);
        $hasEmail = !empty($data['email']);
        $hasIc = !empty($data['ic/passport']);
        
        if ($hasName && $hasEmail && $hasIc) {
            echo "  ✅ Valid student data\n";
        } else {
            echo "  ❌ Missing required fields (Name: {$hasName}, Email: {$hasEmail}, IC: {$hasIc})\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUG COMPLETED ===\n";
