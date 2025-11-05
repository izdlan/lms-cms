<?php
/**
 * Check if Imagick is available for WEB requests (not CLI)
 * Run this file via browser, not command line
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/plain; charset=utf-8');

echo "========================================\n";
echo "IMAGICK EXTENSION CHECK (WEB REQUEST)\n";
echo "========================================\n\n";

echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "PHP Config File: " . php_ini_loaded_file() . "\n";
echo "Additional Config: " . php_ini_scanned_files() . "\n\n";

// Check extension
$loaded = extension_loaded('imagick');
echo "Extension Loaded: " . ($loaded ? 'YES' : 'NO') . "\n";

if ($loaded) {
    echo "Class Exists: " . (class_exists('Imagick') ? 'YES' : 'NO') . "\n";
    
    try {
        $imagick = new Imagick();
        echo "Can Create Imagick: YES\n";
        
        // Get version
        $version = Imagick::getVersion();
        echo "Version: " . $version['versionString'] . "\n";
        
        // Try to create a simple PNG
        $imagick->newImage(100, 100, new ImagickPixel('white'));
        $imagick->setImageFormat('png');
        $pngData = $imagick->getImageBlob();
        echo "Can Create PNG: YES (" . strlen($pngData) . " bytes)\n";
        
        $imagick->clear();
        $imagick->destroy();
        
        echo "\n========================================\n";
        echo "SUCCESS: Imagick is working in WEB mode!\n";
        echo "QR codes will generate correctly.\n";
        echo "========================================\n";
    } catch (Exception $e) {
        echo "Can Create Imagick: NO\n";
        echo "Error: " . $e->getMessage() . "\n";
        echo "\n========================================\n";
        echo "ERROR: Imagick is loaded but not functional\n";
        echo "========================================\n";
    }
} else {
    echo "\n========================================\n";
    echo "ERROR: Imagick extension is NOT loaded\n";
    echo "\nTO FIX:\n";
    echo "1. Go to cPanel -> Select PHP Version\n";
    echo "2. Make sure you select the SAME PHP version for your domain\n";
    echo "3. Enable 'imagick' extension\n";
    echo "4. Click 'Save' and restart PHP-FPM\n";
    echo "\nNote: CLI PHP and Web PHP may use different configs!\n";
    echo "========================================\n";
}

