<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PublicAnnouncement;
use Illuminate\Support\Facades\Storage;

echo "=== CREATING TEST ANNOUNCEMENT WITH IMAGE ===\n";

// Create a simple test image (1x1 pixel PNG)
$imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAI9jU77zgAAAABJRU5ErkJggg==');

// Save it to storage
$testFilename = 'test_' . time() . '.png';
$path = "announcements/$testFilename";
$result = Storage::disk('public')->put($path, $imageData);

if ($result) {
    echo "Test image created: $path\n";
    
    // Create announcement with this image
    $announcement = PublicAnnouncement::create([
        'title' => 'Test Announcement with Image',
        'content' => 'This is a test announcement created programmatically with an image.',
        'category' => 'general',
        'priority' => 'medium',
        'image_url' => '/storage/' . $path,
        'published_at' => now(),
        'is_featured' => true,
        'is_active' => true,
        'admin_id' => 1
    ]);
    
    echo "Announcement created with ID: {$announcement->id}\n";
    echo "Image URL: {$announcement->image_url}\n";
    
    // Verify the file exists
    $fullPath = storage_path("app/public/$path");
    echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    
    // Check public access
    $publicPath = public_path("storage/$path");
    echo "Public access: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
    
} else {
    echo "Failed to create test image\n";
}

echo "\n=== END TEST ===\n";
