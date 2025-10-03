<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PublicAnnouncement;

echo "=== DEBUGGING ANNOUNCEMENTS ===\n";

// Check total announcements
$total = PublicAnnouncement::count();
echo "Total announcements: $total\n\n";

// Get all announcements
$announcements = PublicAnnouncement::all();

foreach ($announcements as $announcement) {
    echo "ID: {$announcement->id}\n";
    echo "Title: {$announcement->title}\n";
    echo "Image URL: " . ($announcement->image_url ?: 'NONE') . "\n";
    echo "Is Active: " . ($announcement->is_active ? 'YES' : 'NO') . "\n";
    echo "Is Featured: " . ($announcement->is_featured ? 'YES' : 'NO') . "\n";
    echo "Published At: {$announcement->published_at}\n";
    echo "Created At: {$announcement->created_at}\n";
    echo "---\n";
}

// Check announcements with images specifically
$withImages = PublicAnnouncement::whereNotNull('image_url')->count();
echo "\nAnnouncements with images: $withImages\n";

// Check active and published announcements
$activePublished = PublicAnnouncement::active()->published()->count();
echo "Active & Published announcements: $activePublished\n";

// Check featured announcements
$featured = PublicAnnouncement::active()->published()->featured()->count();
echo "Featured announcements: $featured\n";

// Check gallery announcements (what the home page uses)
$galleryAnnouncements = PublicAnnouncement::active()->published()->whereNotNull('image_url')->get();
echo "Gallery announcements (with images): " . $galleryAnnouncements->count() . "\n";

foreach ($galleryAnnouncements as $announcement) {
    echo "  - {$announcement->title} (Image: {$announcement->image_url})\n";
}

echo "\n=== END DEBUG ===\n";
