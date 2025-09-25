<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING COLUMN STRUCTURES ===\n\n";

$localFilePath = 'Enrollment OEM.xlsx';

// Test problematic sheets
$problematicSheets = ['VIVA-IUC LMS', 'TVET LMS'];

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
        
        // Find header row
        $headerRowIndex = 0;
        $headerRow = null;
        $hasHeaderKeywords = false;
        
        for ($i = 0; $i < min(10, $rows->count()); $i++) {
            $currentRow = $rows->skip($i)->first();
            $currentRowArray = is_array($currentRow) ? $currentRow : $currentRow->toArray();
            $currentRowText = implode(' ', array_filter($currentRowArray, function($cell) {
                return !empty(trim($cell));
            }));
            
            $headerKeywords = ['name', 'ic', 'passport', 'email', 'address', 'contact', 'student', 'programme', 'learners'];
            foreach ($headerKeywords as $keyword) {
                if (stripos($currentRowText, $keyword) !== false) {
                    $hasHeaderKeywords = true;
                    $headerRowIndex = $i;
                    $headerRow = $currentRow;
                    break 2;
                }
            }
        }
        
        if ($hasHeaderKeywords) {
            echo "Headers found on row " . ($headerRowIndex + 1) . "\n";
            
            // Process headers
            $header = collect($headerRow)->map(function($h) {
                $h = trim($h);
                if (substr($h, 0, 3) === "\xEF\xBB\xBF") {
                    $h = substr($h, 3);
                }
                return trim($h);
            })->toArray();
            
            echo "Available columns:\n";
            foreach ($header as $index => $column) {
                echo ($index + 1) . ": '{$column}'\n";
            }
            
            // Map headers
            $mappedHeader = [];
            foreach ($header as $index => $headerName) {
                $headerName = strtolower(trim($headerName));
                
                if (empty($headerName)) {
                    $mappedHeader[$index] = 'empty_' . $index;
                } elseif ((strpos($headerName, 'name') !== false || strpos($headerName, 'learners') !== false) && strpos($headerName, 'programme') === false) {
                    $mappedHeader[$index] = 'name';
                } elseif (strpos($headerName, 'address') !== false) {
                    $mappedHeader[$index] = 'address';
                } elseif (strpos($headerName, 'ic') !== false || strpos($headerName, 'passport') !== false) {
                    $mappedHeader[$index] = 'ic/passport';
                } elseif (strpos($headerName, 'email') !== false) {
                    $mappedHeader[$index] = 'email';
                } elseif (strpos($headerName, 'contact') !== false || strpos($headerName, 'phone') !== false) {
                    $mappedHeader[$index] = 'contact no.';
                } else {
                    $mappedHeader[$index] = 'unknown_' . $index;
                }
            }
            
            echo "\nMapped headers:\n";
            foreach ($mappedHeader as $index => $mapped) {
                echo ($index + 1) . ": '{$mapped}'\n";
            }
            
            // Test first few data rows
            echo "\nFirst few data rows:\n";
            $dataRows = $rows->slice($headerRowIndex + 1);
            foreach ($dataRows->take(3) as $index => $row) {
                $rowArray = is_array($row) ? $row : $row->toArray();
                
                echo "\nRow " . ($index + 1) . ":\n";
                
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
                
                // Check if this looks like valid student data
                $hasName = !empty($data['name']) && $data['name'] !== '-';
                $hasEmail = !empty($data['email']) && $data['email'] !== '-';
                $hasIc = !empty($data['ic/passport']) && $data['ic/passport'] !== '-';
                
                if ($hasName && $hasEmail && $hasIc) {
                    echo "  ✅ Valid student data\n";
                } else {
                    echo "  ❌ Missing required fields (Name: {$hasName}, Email: {$hasEmail}, IC: {$hasIc})\n";
                }
            }
            
        } else {
            echo "❌ No headers found\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error analyzing {$sheetName}: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n\n";
}

echo "=== DEBUG COMPLETED ===\n";
