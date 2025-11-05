<?php
/**
 * Imagick Verification Script
 * Run this via browser or command line to check if imagick is properly enabled
 * 
 * Usage: php check_imagick.php
 * Or visit: http://your-domain.com/check_imagick.php
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Force output (no buffering)
ob_implicit_flush(true);
if (ob_get_level()) {
    ob_end_clean();
}

echo "=== Imagick Extension Check ===\n\n";
flush();

// Check 1: Extension loaded
$extensionLoaded = extension_loaded('imagick');
echo "1. Extension Loaded: " . ($extensionLoaded ? "✅ YES" : "❌ NO") . "\n";

if (!$extensionLoaded) {
    echo "\n❌ ERROR: Imagick extension is NOT loaded!\n";
    echo "\nTo fix:\n";
    echo "1. Go to cPanel → Select PHP Version\n";
    echo "2. Click 'Extensions'\n";
    echo "3. Find 'imagick' and check the box\n";
    echo "4. Click 'Save'\n";
    echo "5. Restart PHP/Apache if needed\n";
    exit(1);
}

// Check 2: Class exists
$classExists = class_exists('Imagick');
echo "2. Imagick Class Available: " . ($classExists ? "✅ YES" : "❌ NO") . "\n";

if (!$classExists) {
    echo "\n❌ ERROR: Imagick class is not available!\n";
    echo "The extension is loaded but the class cannot be found.\n";
    exit(1);
}

// Check 3: Can instantiate
try {
    $imagick = new Imagick();
    $version = $imagick->getVersion();
    echo "3. Can Instantiate Imagick: ✅ YES\n";
    echo "   Version: " . ($version['versionString'] ?? 'Unknown') . "\n";
    
    // Test basic operation
    $imagick->newImage(100, 100, new ImagickPixel('white'));
    $imagick->setImageFormat('png');
    $blob = $imagick->getImageBlob();
    
    echo "4. Can Create PNG Image: ✅ YES\n";
    echo "   Test PNG size: " . strlen($blob) . " bytes\n";
    
    $imagick->clear();
    $imagick->destroy();
    
    echo "\n✅ SUCCESS: Imagick is fully functional!\n";
    echo "QR code generation should work properly.\n";
    
} catch (Exception $e) {
    echo "3. Can Instantiate Imagick: ❌ NO\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "\n❌ ERROR: Imagick is loaded but not functional!\n";
    echo "Contact your hosting provider (Serverfreak) for assistance.\n";
    exit(1);
}

// Check 4: QR Code library compatibility
echo "\n=== QR Code Library Check ===\n";
try {
    if (file_exists('vendor/autoload.php')) {
        require 'vendor/autoload.php';
        
        use SimpleSoftwareIO\QrCode\Facades\QrCode;
        
        $testData = 'test123';
        $qrCode = QrCode::format('png')
            ->size(100)
            ->margin(1)
            ->generate($testData);
        
        if (is_string($qrCode) && substr($qrCode, 0, 8) === "\x89PNG\r\n\x1a\n") {
            echo "✅ QR Code PNG Generation: WORKING\n";
            echo "   Generated PNG size: " . strlen($qrCode) . " bytes\n";
        } else {
            echo "❌ QR Code PNG Generation: FAILED\n";
            echo "   Generated data is not valid PNG\n";
        }
    } else {
        echo "⚠️  Cannot test QR Code library (vendor/autoload.php not found)\n";
    }
} catch (Exception $e) {
    echo "❌ QR Code Library Test Failed: " . $e->getMessage() . "\n";
}

echo "\n=== Summary ===\n";
echo "If all checks pass, QR codes should generate correctly.\n";
echo "If any check fails, follow the instructions above.\n";

