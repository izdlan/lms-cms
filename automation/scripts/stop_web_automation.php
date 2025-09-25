<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== STOPPING WEB AUTOMATION ===\n";
echo "===============================\n\n";

// Set status to stopped
\Illuminate\Support\Facades\Cache::put('automation_status', 'stopped', now()->addDays(30));

// Kill any running automation processes
echo "🔄 Stopping automation processes...\n";
if (PHP_OS_FAMILY === 'Windows') {
    exec('taskkill /F /IM php.exe /FI "COMMANDLINE eq *automation:simple-watch*" 2>nul');
} else {
    exec('pkill -f "automation:simple-watch" 2>/dev/null');
}

echo "✅ Web automation stopped successfully!\n";
echo "🔄 Status: Stopped\n\n";

echo "=== TO RESTART ===\n";
echo "1. Run: php start_web_automation.php\n";
echo "2. Or use the web interface at /admin/automation\n\n";

