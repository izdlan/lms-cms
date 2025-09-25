<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== AUTOMATION TEST SCRIPT ===\n";
echo "==============================\n\n";

$filePath = storage_path('app/students/Enrollment OEM.xlsx');

// Check if file exists
if (!file_exists($filePath)) {
    echo "❌ Excel file not found: $filePath\n";
    exit(1);
}

echo "✅ Excel file found: $filePath\n";
echo "📅 Last modified: " . date('Y-m-d H:i:s', filemtime($filePath)) . "\n";
echo "📊 File size: " . number_format(filesize($filePath)) . " bytes\n\n";

// Test the import command
echo "🧪 Testing import command...\n";
$exitCode = 0;
$output = [];
exec('php artisan students:auto-import --force 2>&1', $output, $exitCode);

if ($exitCode === 0) {
    echo "✅ Import command working correctly\n\n";
    
    $created = 0;
    $updated = 0;
    $errors = 0;
    
    foreach ($output as $line) {
        if (strpos($line, 'Total Created:') !== false) {
            $created = (int)trim(str_replace('Total Created:', '', $line));
        } elseif (strpos($line, 'Total Updated:') !== false) {
            $updated = (int)trim(str_replace('Total Updated:', '', $line));
        } elseif (strpos($line, 'Total Errors:') !== false) {
            $errors = (int)trim(str_replace('Total Errors:', '', $line));
        }
        
        if (trim($line) && !strpos($line, 'Starting automatic student import')) {
            echo "  " . $line . "\n";
        }
    }
    
    echo "\n📊 IMPORT RESULTS:\n";
    echo "  ✨ Created: $created\n";
    echo "  🔄 Updated: $updated\n";
    echo "  ❌ Errors: $errors\n\n";
    
    if ($created > 0) {
        echo "🎉 SUCCESS: New students were imported!\n";
    } else {
        echo "ℹ️  INFO: No new students found (all existing)\n";
    }
    
} else {
    echo "❌ Import command failed with exit code: $exitCode\n";
    foreach ($output as $line) {
        if (trim($line)) {
            echo "  " . $line . "\n";
        }
    }
}

echo "\n=== AUTOMATION RECOMMENDATIONS ===\n";
echo "1. Use the improved automation watcher: php improved_automation_watcher.php\n";
echo "2. Check file permissions on the Excel file\n";
echo "3. Make sure the file is saved properly after adding new students\n";
echo "4. The automation checks every 30 seconds for changes\n";
echo "5. Both file modification time AND file size are monitored\n\n";

