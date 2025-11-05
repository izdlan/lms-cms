<?php
// Simple Imagick Check Script
// Always outputs something, even if there's an error

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "\n";
echo "========================================\n";
echo "IMAGICK EXTENSION CHECK\n";
echo "========================================\n\n";

// Check 1: Extension loaded
$loaded = extension_loaded('imagick');
echo "Extension Loaded: " . ($loaded ? "YES" : "NO") . "\n";

if (!$loaded) {
    echo "\nERROR: Imagick is NOT loaded!\n";
    echo "\nSOLUTION:\n";
    echo "1. cPanel → Select PHP Version\n";
    echo "2. Find your domain: lms.olympia-education.com\n";
    echo "3. Click 'Use PHP Selector'\n";
    echo "4. Check 'imagick' extension\n";
    echo "5. Click 'Apply'\n";
    echo "\n";
    exit;
}

// Check 2: Class exists
$classExists = class_exists('Imagick');
echo "Class Exists: " . ($classExists ? "YES" : "NO") . "\n";

if (!$classExists) {
    echo "\nERROR: Imagick class not found!\n";
    exit;
}

// Check 3: Can use it
try {
    $img = new Imagick();
    $version = $img->getVersion();
    echo "Can Create Imagick: YES\n";
    echo "Version: " . ($version['versionString'] ?? 'Unknown') . "\n";
    
    // Test PNG creation
    $img->newImage(10, 10, new ImagickPixel('white'));
    $img->setImageFormat('png');
    $blob = $img->getImageBlob();
    echo "Can Create PNG: YES (" . strlen($blob) . " bytes)\n";
    
    $img->clear();
    $img->destroy();
    
    echo "\n";
    echo "========================================\n";
    echo "SUCCESS: Imagick is working!\n";
    echo "QR codes will generate correctly.\n";
    echo "========================================\n";
    echo "\n";
    
} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "\n";
}

echo "\n";

