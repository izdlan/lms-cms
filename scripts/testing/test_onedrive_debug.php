<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING ONEDRIVE SERVICE ===\n\n";

use App\Services\OneDriveExcelImportService;
use Illuminate\Support\Facades\Http;

// Create service instance
$onedriveService = new OneDriveExcelImportService();

// Use reflection to access private properties and methods
$reflection = new ReflectionClass($onedriveService);

// Get the onedriveUrl property
$urlProperty = $reflection->getProperty('onedriveUrl');
$urlProperty->setAccessible(true);
$currentUrl = $urlProperty->getValue($onedriveService);

echo "Current URL from config: {$currentUrl}\n\n";

// Override with working URL
$urlProperty->setValue($onedriveService, 'https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw&download=1');
$newUrl = $urlProperty->getValue($onedriveService);

echo "New URL set to: {$newUrl}\n\n";

// Get the getDownloadUrls method
$method = $reflection->getMethod('getDownloadUrls');
$method->setAccessible(true);

// Call the method to see what URLs it generates
$urls = $method->invoke($onedriveService);

echo "URLs that will be tried:\n";
foreach ($urls as $index => $url) {
    echo ($index + 1) . ": {$url}\n";
}

echo "\nTesting each URL:\n";

foreach ($urls as $index => $url) {
    echo "\nTest " . ($index + 1) . ": {$url}\n";
    
    try {
        $response = Http::timeout(10)->get($url);
        $status = $response->status();
        
        if ($response->successful()) {
            $content = $response->body();
            $isExcel = strpos($content, 'PK') === 0;
            
            echo "  ✅ SUCCESS! Status: {$status}, Size: " . strlen($content) . " bytes";
            if ($isExcel) {
                echo " (Valid Excel file)";
                echo "\n\n🎉 WORKING URL: {$url}\n";
                break;
            } else {
                echo " (Not Excel file)";
            }
        } else {
            echo "  ❌ FAILED! Status: {$status}";
        }
    } catch (Exception $e) {
        echo "  ❌ ERROR: " . $e->getMessage();
    }
}

echo "\n\n=== DEBUG COMPLETED ===\n";
