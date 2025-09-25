<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING NEW ONEDRIVE CONNECTION ===\n\n";

use App\Services\OneDriveExcelImportService;

// Test with the new direct sharing URL
$onedriveService = new OneDriveExcelImportService();

echo "Testing OneDrive connection with new URL...\n";
echo "URL: https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw&download=1\n\n";

// Override the URL for testing with the working URL
$reflection = new ReflectionClass($onedriveService);
$property = $reflection->getProperty('onedriveUrl');
$property->setAccessible(true);
$property->setValue($onedriveService, 'https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw&download=1');

try {
    $result = $onedriveService->testConnection();
    
    if ($result['success']) {
        echo "✅ SUCCESS: OneDrive connection test passed!\n";
        echo "Message: " . $result['message'] . "\n";
    } else {
        echo "❌ FAILED: OneDrive connection test failed!\n";
        echo "Error: " . $result['error'] . "\n";
        echo "Message: " . $result['message'] . "\n";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
