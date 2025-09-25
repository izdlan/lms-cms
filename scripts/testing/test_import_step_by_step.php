<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== STEP-BY-STEP IMPORT TEST ===\n\n";

use App\Services\OneDriveExcelImportService;
use Illuminate\Support\Collection;

// Create OneDrive service instance
$onedriveService = new OneDriveExcelImportService();

// Override URL with working OneDrive URL
$reflection = new ReflectionClass($onedriveService);
$urlProperty = $reflection->getProperty('onedriveUrl');
$urlProperty->setAccessible(true);
$urlProperty->setValue($onedriveService, 'https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw&download=1');

// Download the file
echo "Downloading file...\n";
$downloadMethod = $reflection->getMethod('downloadExcelFile');
$downloadMethod->setAccessible(true);
$downloadResult = $downloadMethod->invoke($onedriveService);

if (!$downloadResult['success']) {
    echo "❌ Failed to download file: " . $downloadResult['error'] . "\n";
    exit;
}

$tempFilePath = storage_path('app/temp_enrollment.xlsx');

// Test with IUC LMS sheet
$sheetName = 'IUC LMS';
echo "=== TESTING SHEET: {$sheetName} ===\n";

try {
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    $spreadsheet = $reader->load($tempFilePath);
    $worksheet = $spreadsheet->getSheetByName($sheetName);
    
    if (!$worksheet) {
        echo "❌ Sheet '{$sheetName}' not found\n";
        exit;
    }
    
    // Convert to collection like the import class does
    $allRows = [];
    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();
    
    for ($row = 1; $row <= $highestRow; $row++) {
        $rowData = [];
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $cellValue = $worksheet->getCell($col . $row)->getValue();
            $rowData[] = $cellValue;
        }
        $allRows[] = $rowData;
    }
    
    $rows = collect($allRows);
    echo "Total rows loaded: " . $rows->count() . "\n";
    
    // Test header detection logic
    echo "\n=== TESTING HEADER DETECTION ===\n";
    $firstRow = $rows->first();
    $firstRowArray = is_array($firstRow) ? $firstRow : $firstRow->toArray();
    $firstRowText = implode(' ', array_filter($firstRowArray, function($cell) {
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
        
        // Process headers
        $header = collect($firstRow)->map(function($h) {
            $h = trim($h);
            if (substr($h, 0, 3) === "\xEF\xBB\xBF") {
                $h = substr($h, 3);
            }
            return trim($h);
        })->toArray();
        
        echo "Headers: " . implode(' | ', array_slice($header, 0, 5)) . "\n";
        
        // Skip header row
        $rows = $rows->slice(1);
        echo "Rows after skipping header: " . $rows->count() . "\n";
        
        // Test column mapping
        echo "\n=== TESTING COLUMN MAPPING ===\n";
        $mappedHeader = [];
        foreach ($header as $index => $headerName) {
            $originalHeader = $headerName;
            $headerName = strtolower(trim($headerName));
            
            if (strpos($headerName, 'name') !== false && strpos($headerName, 'learners') === false) {
                $mappedHeader[$index] = 'name';
                echo "Column {$index} '{$originalHeader}' -> 'name'\n";
            } elseif (strpos($headerName, 'address') !== false) {
                $mappedHeader[$index] = 'address';
                echo "Column {$index} '{$originalHeader}' -> 'address'\n";
            } elseif (strpos($headerName, 'ic') !== false || strpos($headerName, 'passport') !== false) {
                $mappedHeader[$index] = 'ic/passport';
                echo "Column {$index} '{$originalHeader}' -> 'ic/passport'\n";
            } elseif (strpos($headerName, 'email') !== false) {
                $mappedHeader[$index] = 'email';
                echo "Column {$index} '{$originalHeader}' -> 'email'\n";
            } elseif (strpos($headerName, 'contact') !== false || strpos($headerName, 'phone') !== false) {
                $mappedHeader[$index] = 'contact no.';
                echo "Column {$index} '{$originalHeader}' -> 'contact no.'\n";
            }
        }
        
        echo "\nMapped headers: " . json_encode($mappedHeader) . "\n";
        
        // Test data processing for first few rows
        echo "\n=== TESTING DATA PROCESSING ===\n";
        $processedRows = 0;
        foreach ($rows->take(3) as $index => $row) {
            $rowArray = is_array($row) ? $row : $row->toArray();
            
            // Skip empty rows
            if (empty(array_filter($rowArray))) {
                echo "Skipping empty row " . ($index + 1) . "\n";
                continue;
            }
            
            echo "\nProcessing row " . ($index + 1) . ":\n";
            
            // Map data using the mapped headers
            $data = [];
            foreach ($mappedHeader as $colIndex => $mappedName) {
                if (isset($rowArray[$colIndex])) {
                    $data[$mappedName] = trim($rowArray[$colIndex]);
                }
            }
            
            echo "Mapped data: " . json_encode($data) . "\n";
            
            // Check required fields
            $hasName = !empty($data['name']);
            $hasEmail = !empty($data['email']);
            $hasIc = !empty($data['ic/passport']);
            
            echo "Has name: " . ($hasName ? 'YES' : 'NO') . " ('{$data['name']}')\n";
            echo "Has email: " . ($hasEmail ? 'YES' : 'NO') . " ('{$data['email']}')\n";
            echo "Has IC: " . ($hasIc ? 'YES' : 'NO') . " ('{$data['ic/passport']}')\n";
            
            if ($hasName && $hasEmail && $hasIc) {
                echo "✅ Row " . ($index + 1) . " has all required fields\n";
                $processedRows++;
            } else {
                echo "❌ Row " . ($index + 1) . " missing required fields\n";
            }
        }
        
        echo "\nProcessed {$processedRows} valid rows out of 3 tested\n";
        
    } else {
        echo "❌ No header keywords found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} finally {
    // Clean up
    if (file_exists($tempFilePath)) {
        unlink($tempFilePath);
        echo "\n✅ Temp file cleaned up\n";
    }
}

echo "\n=== TEST COMPLETED ===\n";
