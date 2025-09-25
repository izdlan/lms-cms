<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== WEB AUTOMATION STARTER ===\n";
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

// Kill any existing automation processes
echo "🔄 Stopping existing automation processes...\n";
if (PHP_OS_FAMILY === 'Windows') {
    exec('taskkill /F /IM php.exe /FI "COMMANDLINE eq *automation:simple-watch*" 2>nul');
} else {
    exec('pkill -f "automation:simple-watch" 2>/dev/null');
}

sleep(2);

// Start the simple automation
echo "🚀 Starting web automation...\n";
$command = "php artisan automation:simple-watch --file=\"{$filePath}\" --interval=30";

if (PHP_OS_FAMILY === 'Windows') {
    // Use start command to run in background
    $command = "start /B php artisan automation:simple-watch --file=\"{$filePath}\" --interval=30";
}

echo "📝 Command: $command\n";
exec($command);

// Set status in cache
\Illuminate\Support\Facades\Cache::put('automation_status', 'running', now()->addDays(30));
\Illuminate\Support\Facades\Cache::put('automation_file', $filePath, now()->addDays(30));
\Illuminate\Support\Facades\Cache::put('automation_interval', 30, now()->addDays(30));

echo "✅ Web automation started successfully!\n";
echo "🔄 Status: Running\n";
echo "⏱️  Check interval: 30 seconds\n";
echo "📁 Monitoring file: " . basename($filePath) . "\n\n";

echo "=== INSTRUCTIONS ===\n";
echo "1. Go to: http://localhost/LMS_Olympia/admin/automation\n";
echo "2. The automation should now be running\n";
echo "3. Add new students to Excel file and save\n";
echo "4. Wait 30 seconds for detection\n";
echo "5. Check the status cards for updates\n\n";

echo "🔧 To stop automation:\n";
echo "1. Go to the web interface and click 'Stop Automation'\n";
echo "2. Or run: php stop_web_automation.php\n\n";

echo "📊 To check status:\n";
echo "1. Refresh the web page\n";
echo "2. Or run: php check_automation_status.php\n\n";