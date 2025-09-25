<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== AUTOMATION STATUS CHECK ===\n";
echo "===============================\n\n";

$filePath = storage_path('app/students/Enrollment OEM.xlsx');

// Check file status
if (file_exists($filePath)) {
    echo "✅ Excel file exists\n";
    echo "📅 Last modified: " . date('Y-m-d H:i:s', filemtime($filePath)) . "\n";
    echo "📊 File size: " . number_format(filesize($filePath)) . " bytes\n";
} else {
    echo "❌ Excel file not found\n";
    exit(1);
}

// Check cache status
$lastImportTime = cache()->get('last_auto_import_time', 0);
if ($lastImportTime > 0) {
    echo "🕒 Last import: " . date('Y-m-d H:i:s', $lastImportTime) . "\n";
} else {
    echo "🕒 Last import: Never\n";
}

// Check if automation should run
$fileModified = filemtime($filePath);
if ($fileModified > $lastImportTime) {
    echo "🔄 File has been modified since last import - automation should run\n";
} else {
    echo "✅ File unchanged since last import\n";
}

// Check for running PHP processes
echo "\n=== RUNNING PROCESSES ===\n";
$output = [];
exec('tasklist /fi "imagename eq php.exe" /fo csv', $output);
$phpProcesses = count($output) - 1; // Subtract header row
echo "🔢 PHP processes running: $phpProcesses\n";

if ($phpProcesses > 5) {
    echo "⚠️  Many PHP processes running - automation might be stuck\n";
} elseif ($phpProcesses > 0) {
    echo "✅ PHP processes detected - automation might be running\n";
} else {
    echo "❌ No PHP processes running - automation is not active\n";
}

echo "\n=== RECOMMENDATIONS ===\n";
if ($fileModified > $lastImportTime) {
    echo "1. Run: php test_automation.php (to test import)\n";
    echo "2. Run: php improved_automation_watcher.php (to start automation)\n";
    echo "3. Or use: restart_automation.bat (Windows) or restart_automation.ps1 (PowerShell)\n";
} else {
    echo "1. Add new students to Excel file and save\n";
    echo "2. Run: php test_automation.php (to test import)\n";
    echo "3. Run: php improved_automation_watcher.php (to start automation)\n";
}

echo "\n";

