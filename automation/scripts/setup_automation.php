<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Setting up Student Import Automation...\n";
echo "=====================================\n\n";

// Check if Excel file exists
$filePath = 'C:\xampp\htdocs\LMS_Olympia\storage\app\students\Enrollment OEM.xlsx';
if (file_exists($filePath)) {
    echo "✓ Excel file found: $filePath\n";
    echo "  Last modified: " . date('Y-m-d H:i:s', filemtime($filePath)) . "\n";
} else {
    echo "✗ Excel file not found: $filePath\n";
    echo "  Please make sure the file exists before running automation.\n";
    exit(1);
}

// Test the import command
echo "\nTesting import command...\n";
$output = [];
$exitCode = 0;
exec('php artisan students:auto-import --force 2>&1', $output, $exitCode);

if ($exitCode === 0) {
    echo "✓ Import command working correctly\n";
    foreach ($output as $line) {
        if (trim($line) && !strpos($line, 'Starting automatic student import')) {
            echo "  " . $line . "\n";
        }
    }
} else {
    echo "✗ Import command failed with exit code: $exitCode\n";
    foreach ($output as $line) {
        if (trim($line)) {
            echo "  " . $line . "\n";
        }
    }
    exit(1);
}

// Create automation configuration
$config = [
    'enabled' => true,
    'excel_file' => $filePath,
    'check_interval' => 60, // seconds
    'last_import_time' => time(),
    'created_at' => date('Y-m-d H:i:s')
];

$configPath = 'storage/app/automation.json';
file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT));

echo "\n✓ Automation configuration saved to: $configPath\n";

// Display instructions
echo "\nAutomation Setup Complete!\n";
echo "==========================\n\n";
echo "To start the automation, you can:\n\n";
echo "1. Run the watcher script:\n";
echo "   php automation_watcher.php\n\n";
echo "2. Use the batch file (Windows):\n";
echo "   start_automation.bat\n\n";
echo "3. Use the PowerShell script (Windows):\n";
echo "   .\\start_automation.ps1\n\n";
echo "4. Set up a Windows Task Scheduler task to run:\n";
echo "   php " . realpath('automation_watcher.php') . "\n\n";
echo "The automation will:\n";
echo "- Check for file changes every 60 seconds\n";
echo "- Automatically import students when the Excel file is updated\n";
echo "- Process only sheets 11-17 (DHU LMS, IUC LMS, VIVA-IUC LMS, LUC LMS, EXECUTIVE LMS, UPM LMS, TVET LMS)\n";
echo "- Log all activities to storage/logs/laravel.log\n\n";
echo "To stop the automation, press Ctrl+C\n";
