<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use PhpOffice\PhpSpreadsheet\IOFactory;

echo "Analyzing Excel structure for course information...\n\n";

// Check if Google Drive URL is configured
$googleDriveUrl = env('GOOGLE_DRIVE_EXCEL_URL');

if (empty($googleDriveUrl)) {
    echo "❌ Google Drive URL is not configured. Please set GOOGLE_DRIVE_EXCEL_URL in your .env file.\n";
    exit(1);
}

echo "Google Drive URL: {$googleDriveUrl}\n\n";

try {
    // Download the Excel file from Google Drive
    $response = \Illuminate\Support\Facades\Http::get($googleDriveUrl);
    
    if (!$response->successful()) {
        echo "❌ Failed to download file from Google Drive. Status: " . $response->status() . "\n";
        exit(1);
    }
    
    $tempFile = storage_path('app/temp_analysis.xlsx');
    file_put_contents($tempFile, $response->body());
    
    echo "✅ File downloaded successfully\n\n";
    
    // Process the file
    $reader = IOFactory::createReader('Xlsx');
    $reader->setReadDataOnly(true);
    $reader->setReadEmptyCells(false);
    
    $spreadsheet = $reader->load($tempFile);
    $sheetCount = $spreadsheet->getSheetCount();
    
    echo "Total sheets: {$sheetCount}\n\n";
    
    // Process specific sheets by index (only the 6 required sheets)
    $lmsSheets = [
        10 => 'DHU LMS',        // Sheet 10
        11 => 'IUC LMS',        // Sheet 11
        13 => 'LUC LMS',        // Sheet 13
        14 => 'EXECUTIVE LMS',  // Sheet 14
        15 => 'UPM LMS',        // Sheet 15
        16 => 'TVET LMS'        // Sheet 16
    ];
    
    foreach ($lmsSheets as $sheetIndex => $sheetName) {
        echo "=== {$sheetName} (Sheet {$sheetIndex}) ===\n";
        
        if ($spreadsheet->getSheetCount() <= $sheetIndex) {
            echo "❌ Sheet not found\n\n";
            continue;
        }
        
        $worksheet = $spreadsheet->getSheet($sheetIndex);
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        
        echo "Rows: {$highestRow}, Columns: {$highestColumn}\n";
        
        // Analyze first 10 rows to understand structure
        echo "First 10 rows analysis:\n";
        for ($row = 1; $row <= min(10, $highestRow); $row++) {
            $rowData = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = $worksheet->getCell($col . $row)->getCalculatedValue();
                $rowData[] = $cellValue;
            }
            
            echo "Row {$row}: " . implode(' | ', array_slice($rowData, 0, 10)) . "\n";
        }
        
        // Look for course/program related columns
        echo "\nLooking for course/program information...\n";
        
        // Check if there are any columns that might contain course codes or program names
        $courseColumns = [];
        $programColumns = [];
        
        // Analyze column headers (first few rows)
        for ($row = 1; $row <= min(5, $highestRow); $row++) {
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = trim($worksheet->getCell($col . $row)->getCalculatedValue());
                
                // Look for course-related keywords
                if (preg_match('/course|subject|program|module|class/i', $cellValue)) {
                    $courseColumns[] = "{$col}{$row}: {$cellValue}";
                }
                
                // Look for program-related keywords
                if (preg_match('/program|degree|diploma|certificate|bachelor|master/i', $cellValue)) {
                    $programColumns[] = "{$col}{$row}: {$cellValue}";
                }
            }
        }
        
        if (!empty($courseColumns)) {
            echo "Potential course columns: " . implode(', ', $courseColumns) . "\n";
        }
        
        if (!empty($programColumns)) {
            echo "Potential program columns: " . implode(', ', $programColumns) . "\n";
        }
        
        // Look for actual course codes in the data
        echo "\nLooking for course codes in data...\n";
        $courseCodes = [];
        $programCodes = [];
        
        for ($row = 5; $row <= min(20, $highestRow); $row++) {
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = trim($worksheet->getCell($col . $row)->getCalculatedValue());
                
                // Look for course code patterns (e.g., ABC123, CS101, etc.)
                if (preg_match('/^[A-Z]{2,4}\d{3,4}$/', $cellValue)) {
                    $courseCodes[] = "{$col}{$row}: {$cellValue}";
                }
                
                // Look for program code patterns
                if (preg_match('/^[A-Z]{2,6}$/', $cellValue) && strlen($cellValue) >= 3) {
                    $programCodes[] = "{$col}{$row}: {$cellValue}";
                }
            }
        }
        
        if (!empty($courseCodes)) {
            echo "Found course codes: " . implode(', ', array_slice($courseCodes, 0, 10)) . "\n";
        }
        
        if (!empty($programCodes)) {
            echo "Found program codes: " . implode(', ', array_slice($programCodes, 0, 10)) . "\n";
        }
        
        echo "\n" . str_repeat("-", 50) . "\n\n";
    }
    
    // Clean up
    unlink($tempFile);
    
    echo "✅ Analysis completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

