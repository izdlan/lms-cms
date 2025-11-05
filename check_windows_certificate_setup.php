<?php

/**
 * Windows Certificate Generation Setup Check
 * Run this script to verify your Windows localhost setup for certificate generation
 * 
 * Usage: php check_windows_certificate_setup.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Windows Certificate Generation Setup Check ===\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Check template file exists
$templatePath = storage_path('app/templates/certificate_template.docx');
echo "1. Checking template file...\n";
if (file_exists($templatePath)) {
    echo "   ✓ Template file exists: {$templatePath}\n";
    $success[] = "Template file exists";
    
    // Check if readable
    if (is_readable($templatePath)) {
        echo "   ✓ Template file is readable\n";
        $success[] = "Template file is readable";
    } else {
        echo "   ✗ Template file is NOT readable\n";
        $errors[] = "Template file is not readable. Check file permissions.";
    }
    
    // Check file size
    $templateSize = filesize($templatePath);
    echo "   ℹ Template file size: " . number_format($templateSize) . " bytes\n";
    
    if ($templateSize < 10000) {
        echo "   ⚠ Template file seems small (< 10KB), might be corrupted\n";
        $warnings[] = "Template file is very small";
    }
    
    // Try to validate it's a valid DOCX (ZIP)
    $zip = new \ZipArchive();
    $zipResult = $zip->open($templatePath);
    if ($zipResult === true) {
        echo "   ✓ Template file is a valid DOCX (ZIP archive)\n";
        $success[] = "Template file is valid DOCX";
        $zip->close();
    } else {
        echo "   ✗ Template file is NOT a valid DOCX (ZIP archive). Error code: {$zipResult}\n";
        $errors[] = "Template file is corrupted or not a valid DOCX file";
    }
    
} else {
    echo "   ✗ Template file NOT found: {$templatePath}\n";
    $errors[] = "Template file not found at: {$templatePath}";
}

echo "\n";

// 2. Check directories
echo "2. Checking directories...\n";

$dirs = [
    'Temp' => storage_path('app/temp'),
    'Certificates' => storage_path('app/certificates'),
    'Templates' => storage_path('app/templates'),
];

foreach ($dirs as $name => $dir) {
    echo "   Checking {$name} directory: {$dir}\n";
    
    if (is_dir($dir)) {
        echo "     ✓ Directory exists\n";
        $success[] = "{$name} directory exists";
        
        if (is_writable($dir)) {
            echo "     ✓ Directory is writable\n";
            $success[] = "{$name} directory is writable";
        } else {
            echo "     ✗ Directory is NOT writable\n";
            $errors[] = "{$name} directory is not writable";
            echo "     → Try: Right-click folder → Properties → Security → Edit → Add your user → Full Control\n";
        }
    } else {
        echo "     ⚠ Directory does not exist (will be created automatically)\n";
        $warnings[] = "{$name} directory will be created";
        
        // Check if parent is writable
        $parent = dirname($dir);
        if (is_writable($parent)) {
            echo "     ✓ Parent directory is writable (can create)\n";
        } else {
            echo "     ✗ Parent directory is NOT writable (cannot create)\n";
            $errors[] = "Cannot create {$name} directory - parent not writable";
        }
    }
}

echo "\n";

// 3. Check PHP extensions
echo "3. Checking PHP extensions...\n";

$required = [
    'zip' => 'ZIP extension (required for DOCX processing)',
    'gd' => 'GD extension (optional, for image processing)',
    'imagick' => 'Imagick extension (optional, for better image handling)',
];

foreach ($required as $ext => $desc) {
    if (extension_loaded($ext)) {
        echo "   ✓ {$ext} extension loaded - {$desc}\n";
        $success[] = "{$ext} extension loaded";
    } else {
        if ($ext === 'zip') {
            echo "   ✗ {$ext} extension NOT loaded - {$desc} (REQUIRED)\n";
            $errors[] = "{$ext} extension is required but not loaded";
        } else {
            echo "   ⚠ {$ext} extension NOT loaded - {$desc} (optional)\n";
            $warnings[] = "{$ext} extension not loaded (optional)";
        }
    }
}

echo "\n";

// 4. Check PhpOffice/PhpWord
echo "4. Checking PhpWord library...\n";

try {
    $processor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
    echo "   ✓ PhpWord TemplateProcessor can load template\n";
    $success[] = "PhpWord can load template";
} catch (\Exception $e) {
    echo "   ✗ PhpWord cannot load template: " . $e->getMessage() . "\n";
    $errors[] = "PhpWord cannot load template: " . $e->getMessage();
}

echo "\n";

// 5. Test file creation
echo "5. Testing file creation...\n";

$testFile = storage_path('app/temp/test_' . time() . '.txt');
$testDir = dirname($testFile);

if (!is_dir($testDir)) {
    @mkdir($testDir, 0755, true);
}

if (file_put_contents($testFile, 'test') !== false) {
    echo "   ✓ Can create files in temp directory\n";
    $success[] = "Can create files";
    
    if (file_exists($testFile)) {
        echo "   ✓ File exists after creation\n";
        $success[] = "File exists after creation";
        
        if (@unlink($testFile)) {
            echo "   ✓ Can delete files\n";
            $success[] = "Can delete files";
        } else {
            echo "   ⚠ Cannot delete test file (Windows file locking?)\n";
            $warnings[] = "File deletion test failed";
        }
    } else {
        echo "   ✗ File does not exist after creation\n";
        $errors[] = "File creation test failed";
    }
} else {
    echo "   ✗ Cannot create files in temp directory\n";
    $errors[] = "Cannot create files in temp directory";
}

echo "\n";

// Summary
echo "=== SUMMARY ===\n\n";
echo "✓ Success: " . count($success) . " checks passed\n";
echo "⚠ Warnings: " . count($warnings) . "\n";
echo "✗ Errors: " . count($errors) . "\n\n";

if (count($errors) > 0) {
    echo "ERRORS FOUND:\n";
    foreach ($errors as $error) {
        echo "  - {$error}\n";
    }
    echo "\n";
}

if (count($warnings) > 0) {
    echo "WARNINGS:\n";
    foreach ($warnings as $warning) {
        echo "  - {$warning}\n";
    }
    echo "\n";
}

if (count($errors) === 0) {
    echo "✓ All critical checks passed! Certificate generation should work.\n";
} else {
    echo "✗ Please fix the errors above before generating certificates.\n";
}

echo "\n";

