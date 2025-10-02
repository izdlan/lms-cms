<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Storage;

echo "=== TESTING UPLOAD FUNCTIONALITY ===\n";

// Test creating a file in the announcements directory
$testContent = "This is a test file";
$testFilename = 'test_' . time() . '.txt';

try {
    // Test storage disk
    $path = Storage::disk('public')->put("announcements/$testFilename", $testContent);
    echo "Storage put result: " . ($path ? $path : 'FAILED') . "\n";
    
    // Check if file exists
    $exists = Storage::disk('public')->exists("announcements/$testFilename");
    echo "File exists in storage: " . ($exists ? 'YES' : 'NO') . "\n";
    
    // Check physical file
    $fullPath = storage_path("app/public/announcements/$testFilename");
    echo "Physical file path: $fullPath\n";
    echo "Physical file exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    
    // Check public access
    $publicPath = public_path("storage/announcements/$testFilename");
    echo "Public path: $publicPath\n";
    echo "Public file exists: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
    
    // Clean up
    if ($exists) {
        Storage::disk('public')->delete("announcements/$testFilename");
        echo "Test file cleaned up\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== END TEST ===\n";
