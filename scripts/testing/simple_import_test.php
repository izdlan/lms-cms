<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SIMPLE IMPORT TEST ===\n\n";

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
echo "Downloading file...\n";
$downloadMethod = $reflection->getMethod('downloadExcelFile');
$downloadMethod->setAccessible(true);
$downloadResult = $downloadMethod->invoke($onedriveService);

if (!$downloadResult['success']) {
    echo "❌ Failed to download file: " . $downloadResult['error'] . "\n";
    exit;
}

$tempFilePath = storage_path('app/temp_enrollment.xlsx');
echo "✅ File downloaded: {$tempFilePath}\n";
echo "File size: " . number_format(filesize($tempFilePath)) . " bytes\n\n";

// Test reading the Excel file directly
echo "=== TESTING EXCEL FILE READING ===\n";
try {
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    $spreadsheet = $reader->load($tempFilePath);
    $worksheet = $spreadsheet->getSheetByName('IUC LMS');
    
    if (!$worksheet) {
        echo "❌ Sheet 'IUC LMS' not found\n";
        exit;
    }
    
    echo "✅ Sheet loaded successfully\n";
    
    // Get first few rows
    $highestRow = $worksheet->getHighestRow();
    echo "Total rows: {$highestRow}\n\n";
    
    // Test the import logic manually
    echo "=== TESTING IMPORT LOGIC MANUALLY ===\n";
    
    // Get all rows
    $allRows = [];
    for ($row = 1; $row <= min(10, $highestRow); $row++) {
        $rowData = [];
        for ($col = 'A'; $col <= 'Z'; $col++) {
            $cellValue = $worksheet->getCell($col . $row)->getValue();
            $rowData[] = $cellValue;
        }
        $allRows[] = $rowData;
    }
    
    echo "First 10 rows:\n";
    foreach ($allRows as $index => $row) {
        $nonEmpty = array_filter($row, function($cell) { return !empty(trim($cell)); });
        echo "Row " . ($index + 1) . ": " . count($nonEmpty) . " non-empty cells\n";
        if (count($nonEmpty) > 0) {
            echo "  Data: " . implode(' | ', array_slice($row, 0, 3)) . "\n";
        }
    }
    
    // Test header detection
    echo "\n=== TESTING HEADER DETECTION ===\n";
    $firstRow = $allRows[0];
    $firstRowText = implode(' ', array_filter($firstRow, function($cell) {
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
