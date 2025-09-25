<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING ONEDRIVE WITH REDIRECTS ===\n\n";

use Illuminate\Support\Facades\Http;

$originalUrl = 'https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw';

echo "Original URL: {$originalUrl}\n\n";

try {
    // Follow redirects to get the actual download URL
    $response = Http::withOptions([
        'allow_redirects' => true,
        'max_redirects' => 10,
        'timeout' => 30
    ])->get($originalUrl);
    
    echo "Final URL after redirects: " . $response->effectiveUri() . "\n";
    echo "Status: " . $response->status() . "\n";
    echo "Content-Type: " . $response->header('Content-Type') . "\n";
    echo "Content-Length: " . $response->header('Content-Length') . "\n";
    
    if ($response->successful()) {
        $content = $response->body();
        $isExcel = strpos($content, 'PK') === 0;
        
        echo "Content size: " . strlen($content) . " bytes\n";
        echo "Is Excel file: " . ($isExcel ? "Yes" : "No") . "\n";
        
        if ($isExcel) {
            echo "\n🎉 SUCCESS! OneDrive file is accessible and is a valid Excel file!\n";
            echo "You can use this URL in your .env file:\n";
            echo "ONEDRIVE_EXCEL_URL={$originalUrl}\n";
        } else {
            echo "\n⚠️  File downloaded but doesn't appear to be an Excel file.\n";
            echo "First 100 characters: " . substr($content, 0, 100) . "\n";
        }
    } else {
        echo "\n❌ FAILED! Status: " . $response->status() . "\n";
        echo "Response: " . substr($response->body(), 0, 200) . "\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
