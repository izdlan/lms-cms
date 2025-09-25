<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING ONEDRIVE IMPORT PROCESS ===\n\n";

use App\Services\OneDriveExcelImportService;
use Illuminate\Support\Facades\Log;

// Create OneDrive service instance
$onedriveService = new OneDriveExcelImportService();

// Override URL with working OneDrive URL
$reflection = new ReflectionClass($onedriveService);
$urlProperty = $reflection->getProperty('onedriveUrl');
$urlProperty->setAccessible(true);
$urlProperty->setValue($onedriveService, 'https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw&download=1');

echo "OneDrive URL: https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw&download=1\n\n";

// Test connection
echo "=== TESTING CONNECTION ===\n";
$connectionResult = $onedriveService->testConnection();
if ($connectionResult['success']) {
    echo "✅ Connection successful!\n\n";
} else {
    echo "❌ Connection failed: " . $connectionResult['error'] . "\n";
    exit;
}

// Test downloading the file manually
echo "=== TESTING FILE DOWNLOAD ===\n";
try {
    $downloadMethod = $reflection->getMethod('downloadExcelFile');
    $downloadMethod->setAccessible(true);
    $downloadResult = $downloadMethod->invoke($onedriveService);
    
    if ($downloadResult['success']) {
        echo "✅ File download successful!\n";
        
        // Check if temp file exists
        $tempFilePath = storage_path('app/temp_enrollment.xlsx');
        if (file_exists($tempFilePath)) {
            echo "✅ Temp file exists: {$tempFilePath}\n";
            echo "File size: " . number_format(filesize($tempFilePath)) . " bytes\n";
            
            // Try to read the Excel file and check sheets
            echo "\n=== CHECKING EXCEL FILE SHEETS ===\n";
            try {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                $spreadsheet = $reader->load($tempFilePath);
                $sheetNames = $spreadsheet->getSheetNames();
                
                echo "Available sheets: " . implode(', ', $sheetNames) . "\n";
                
                // Check each expected sheet
                $expectedSheets = ['DHU LMS', 'IUC LMS', 'VIVA-IUC LMS', 'LUC LMS', 'EXECUTIVE LMS', 'UPM LMS', 'TVET LMS'];
                foreach ($expectedSheets as $expectedSheet) {
                    if (in_array($expectedSheet, $sheetNames)) {
                        echo "✅ Found sheet: {$expectedSheet}\n";
                        
                        // Check if sheet has data
                        $worksheet = $spreadsheet->getSheetByName($expectedSheet);
                        $highestRow = $worksheet->getHighestRow();
                        $highestColumn = $worksheet->getHighestColumn();
                        
                        echo "  - Rows: {$highestRow}, Columns: {$highestColumn}\n";
                        
                        // Show first few rows
                        echo "  - First 3 rows:\n";
                        for ($row = 1; $row <= min(3, $highestRow); $row++) {
                            $rowData = [];
                            for ($col = 'A'; $col <= $highestColumn; $col++) {
                                $cellValue = $worksheet->getCell($col . $row)->getValue();
                                $rowData[] = $cellValue;
                            }
                            echo "    Row {$row}: " . implode(' | ', array_slice($rowData, 0, 5)) . "\n";
                        }
                    } else {
                        echo "❌ Missing sheet: {$expectedSheet}\n";
                    }
                }
                
            } catch (Exception $e) {
                echo "❌ Error reading Excel file: " . $e->getMessage() . "\n";
            }
            
            // Clean up temp file
            unlink($tempFilePath);
            echo "\n✅ Temp file cleaned up\n";
            
        } else {
            echo "❌ Temp file not found after download\n";
        }
    } else {
        echo "❌ File download failed: " . $downloadResult['error'] . "\n";
    }
} catch (Exception $e) {
    echo "❌ Download error: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETED ===\n";
