<?php
// Simple Imagick diagnostic script
// Access via: http://localhost/lms-cms/test_imagick.php

header('Content-Type: text/plain');

echo "Imagick Test\n";
echo str_repeat('=', 40) . "\n\n";

// Check extension
if (!extension_loaded('imagick')) {
    echo "Status: Imagick extension NOT loaded.\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Loaded Extensions: \n- " . implode("\n- ", get_loaded_extensions()) . "\n";
    exit(0);
}

echo "Status: Imagick extension is loaded.\n";

// Version info
try {
    $version = \Imagick::getVersion();
    echo "Imagick Version: " . ($version['versionString'] ?? json_encode($version)) . "\n";
} catch (Throwable $e) {
    echo "Unable to read Imagick version: " . $e->getMessage() . "\n";
}

// Attempt basic operations
try {
    $img = new \Imagick();
    $width = 300;
    $height = 120;
    $background = new \ImagickPixel('#ffffff');
    $img->newImage($width, $height, $background);
    $img->setImageFormat('png');

    // Draw a green rectangle and some text
    $draw = new \ImagickDraw();
    $draw->setFillColor('#20c997');
    $draw->rectangle(10, 10, $width - 10, $height - 10);

    $draw->setFillColor('#000000');
    $draw->setFontSize(16);
    $draw->annotation(20, 65, 'Imagick OK - ' . date('Y-m-d H:i:s'));

    $img->drawImage($draw);

    $outputPath = __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'imagick_test.png';
    $saved = $img->writeImage($outputPath);

    echo "Image Create: " . ($saved ? 'SUCCESS' : 'FAILED') . "\n";
    echo "Saved To: " . $outputPath . "\n";
    echo "Public URL: /imagick_test.png\n";

    // Clean up
    $img->clear();
    $img->destroy();
} catch (Throwable $e) {
    echo "Image Operation Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
