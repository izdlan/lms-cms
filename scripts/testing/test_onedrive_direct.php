<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING ONEDRIVE DIRECT ACCESS ===\n\n";

use Illuminate\Support\Facades\Http;

$originalUrl = 'https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw';

echo "Original URL: {$originalUrl}\n\n";

// Try different approaches to access the file
$testUrls = [
    // Original URL
    $originalUrl,
    
    // Try with different parameters
    str_replace('?e=G4v8Jw', '?e=G4v8Jw&download=1', $originalUrl),
    str_replace('?e=G4v8Jw', '?e=G4v8Jw&download=1&authkey=!', $originalUrl),
    
    // Try direct download format
    'https://1drv.ms/x/s!ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw',
    'https://1drv.ms/x/s!ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw&download=1',
    
    // Try with different user agent
    'https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw&download=1&authkey=!',
];

foreach ($testUrls as $index => $url) {
    echo "Test " . ($index + 1) . ": {$url}\n";
    
    try {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,*/*',
            'Accept-Language' => 'en-US,en;q=0.9',
        ])->withOptions([
            'allow_redirects' => true,
            'max_redirects' => 5,
            'timeout' => 15
        ])->get($url);
        
        $status = $response->status();
        echo "  Status: {$status}\n";
        
        if ($response->successful()) {
            $content = $response->body();
            $isExcel = strpos($content, 'PK') === 0;
            
            echo "  ✅ SUCCESS! Size: " . strlen($content) . " bytes";
            if ($isExcel) {
                echo " (Valid Excel file)";
                echo "\n\n🎉 WORKING URL FOUND: {$url}\n";
                echo "You can use this URL in your .env file:\n";
                echo "ONEDRIVE_EXCEL_URL={$url}\n";
                break;
            } else {
                echo " (Not Excel file)";
            }
            echo "\n";
        } else {
            echo "  ❌ FAILED! Status: {$status}\n";
            if ($status == 403) {
                echo "  (Forbidden - may need different permissions)\n";
            } elseif ($status == 404) {
                echo "  (Not Found - URL format may be incorrect)\n";
            }
        }
    } catch (Exception $e) {
        echo "  ❌ ERROR: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "If all tests fail, try these steps:\n";
echo "1. Make sure the OneDrive file is set to 'Anyone with the link can view'\n";
echo "2. Try downloading the file manually in your browser first\n";
echo "3. Consider using Google Drive instead (already configured)\n";
echo "4. Or use local file upload through the admin panel\n";

echo "\n=== TEST COMPLETED ===\n";
