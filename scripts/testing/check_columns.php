<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING EXCEL COLUMNS ===\n\n";

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

// Test with IUC LMS sheet
$sheetName = 'IUC LMS';
echo "=== CHECKING SHEET: {$sheetName} ===\n";

try {
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    $spreadsheet = $reader->load($tempFilePath);
    $worksheet = $spreadsheet->getSheetByName($sheetName);
    
    if (!$worksheet) {
        echo "❌ Sheet '{$sheetName}' not found\n";
        exit;
    }
    
    // Get header row
    $headerRow = [];
    for ($col = 'A'; $col <= 'Z'; $col++) {
        $cellValue = $worksheet->getCell($col . '1')->getValue();
        if (!empty(trim($cellValue))) {
            $headerRow[] = trim($cellValue);
        }
    }
    
    echo "Available columns:\n";
    foreach ($headerRow as $index => $column) {
        echo ($index + 1) . ": {$column}\n";
    }
    
    echo "\n=== CHECKING FOR REQUIRED FIELDS ===\n";
    
    // Check for name
    $hasName = false;
    foreach ($headerRow as $column) {
        if (stripos($column, 'name') !== false) {
            echo "✅ Found NAME column: '{$column}'\n";
            $hasName = true;
            break;
        }
    }
    if (!$hasName) echo "❌ No NAME column found\n";
    
    // Check for IC/Passport
    $hasIc = false;
    foreach ($headerRow as $column) {
        if (stripos($column, 'ic') !== false || stripos($column, 'passport') !== false) {
            echo "✅ Found IC/PASSPORT column: '{$column}'\n";
            $hasIc = true;
            break;
        }
    }
    if (!$hasIc) echo "❌ No IC/PASSPORT column found\n";
    
    // Check for email
    $hasEmail = false;
    foreach ($headerRow as $column) {
        if (stripos($column, 'email') !== false) {
            echo "✅ Found EMAIL column: '{$column}'\n";
            $hasEmail = true;
            break;
        }
    }
    if (!$hasEmail) echo "❌ No EMAIL column found\n";
    
    // Check for contact
    $hasContact = false;
    foreach ($headerRow as $column) {
        if (stripos($column, 'contact') !== false || stripos($column, 'phone') !== false) {
            echo "✅ Found CONTACT column: '{$column}'\n";
            $hasContact = true;
            break;
        }
    }
    if (!$hasContact) echo "❌ No CONTACT column found\n";
    
    // Check for address
    $hasAddress = false;
    foreach ($headerRow as $column) {
        if (stripos($column, 'address') !== false) {
            echo "✅ Found ADDRESS column: '{$column}'\n";
            $hasAddress = true;
            break;
        }
    }
    if (!$hasAddress) echo "❌ No ADDRESS column found\n";
    
    echo "\n=== CHECKING SAMPLE DATA ===\n";
    
    // Check first few data rows
    for ($row = 2; $row <= 4; $row++) {
        echo "\nRow {$row} data:\n";
        
        $name = '';
        $ic = '';
        $email = '';
        $contact = '';
        $address = '';
        
        for ($col = 'A'; $col <= 'Z'; $col++) {
            $cellValue = $worksheet->getCell($col . $row)->getValue();
            $headerValue = $worksheet->getCell($col . '1')->getValue();
            
            if (!empty(trim($cellValue)) && !empty(trim($headerValue))) {
                $headerLower = strtolower(trim($headerValue));
                
                if (stripos($headerLower, 'name') !== false && empty($name)) {
                    $name = trim($cellValue);
                } elseif ((stripos($headerLower, 'ic') !== false || stripos($headerLower, 'passport') !== false) && empty($ic)) {
                    $ic = trim($cellValue);
                } elseif (stripos($headerLower, 'email') !== false && empty($email)) {
                    $email = trim($cellValue);
                } elseif ((stripos($headerLower, 'contact') !== false || stripos($headerLower, 'phone') !== false) && empty($contact)) {
                    $contact = trim($cellValue);
                } elseif (stripos($headerLower, 'address') !== false && empty($address)) {
                    $address = trim($cellValue);
                }
            }
        }
        
        echo "  Name: '{$name}'\n";
        echo "  IC/Passport: '{$ic}'\n";
        echo "  Email: '{$email}'\n";
        echo "  Contact: '{$contact}'\n";
        echo "  Address: '{$address}'\n";
        
        // Check if required fields are present
        if (empty($name)) echo "  ❌ Missing NAME\n";
        if (empty($ic)) echo "  ❌ Missing IC/PASSPORT\n";
        if (empty($email)) echo "  ❌ Missing EMAIL\n";
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

echo "\n=== CHECK COMPLETED ===\n";
