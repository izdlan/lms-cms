<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING ONEDRIVE URL CONVERSIONS ===\n\n";

use Illuminate\Support\Facades\Http;

$originalUrl = 'https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw';

echo "Original URL: {$originalUrl}\n\n";

// Test different URL conversion methods
$testUrls = [];

// Method 1: Direct URL
$testUrls[] = $originalUrl;

// Method 2: Convert to direct download format
if (preg_match('/\/x\/c\/([a-zA-Z0-9_-]+)\/([a-zA-Z0-9_-]+)/', $originalUrl, $matches)) {
    $fileId = $matches[2];
    $testUrls[] = "https://1drv.ms/x/s!{$fileId}/download";
    $testUrls[] = "https://1drv.ms/x/s!{$fileId}";
    $testUrls[] = "https://1drv.ms/x/s!{$fileId}?e=BzgW7r";
}

// Method 3: Try with different parameters
$testUrls[] = str_replace('?e=G4v8Jw', '/download', $originalUrl);
$testUrls[] = str_replace('?e=G4v8Jw', '', $originalUrl) . '/download';

// Method 4: Try Microsoft Graph API format
$encodedUrl = urlencode($originalUrl);
$testUrls[] = "https://api.onedrive.com/v1.0/shares/u!{$encodedUrl}/root/content";

echo "Testing " . count($testUrls) . " different URL formats...\n\n";

foreach ($testUrls as $index => $url) {
    echo "Test " . ($index + 1) . ": {$url}\n";
    
    try {
        $response = Http::timeout(10)->get($url);
        $status = $response->status();
        
        if ($response->successful()) {
            $content = $response->body();
            $isExcel = strpos($content, 'PK') === 0;
            
            echo "  ✅ SUCCESS! Status: {$status}, Size: " . strlen($content) . " bytes";
            if ($isExcel) {
                echo " (Valid Excel file)";
            } else {
                echo " (Not Excel file)";
            }
            echo "\n";
            
            if ($isExcel) {
                echo "\n🎉 WORKING URL FOUND: {$url}\n";
                echo "You can use this URL in your .env file:\n";
                echo "ONEDRIVE_EXCEL_URL={$url}\n";
                break;
            }
        } else {
            echo "  ❌ FAILED! Status: {$status}\n";
        }
    } catch (Exception $e) {
        echo "  ❌ ERROR: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "\n=== TEST COMPLETED ===\n";
