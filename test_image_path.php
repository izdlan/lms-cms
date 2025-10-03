<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PublicAnnouncement;

$announcement = PublicAnnouncement::first();
echo "Image URL from DB: " . $announcement->image_url . "\n";

$imagePath = public_path(ltrim($announcement->image_url, '/'));
echo "Full image path: " . $imagePath . "\n";
echo "File exists: " . (file_exists($imagePath) ? 'YES' : 'NO') . "\n";

if (!file_exists($imagePath)) {
    echo "Checking storage path...\n";
    $storagePath = storage_path('app/public' . str_replace('/storage', '', $announcement->image_url));
    echo "Storage path: " . $storagePath . "\n";
    echo "Storage file exists: " . (file_exists($storagePath) ? 'YES' : 'NO') . "\n";
}
