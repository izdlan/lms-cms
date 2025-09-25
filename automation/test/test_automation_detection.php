<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== AUTOMATION DETECTION TEST ===\n";
echo "================================\n\n";

$filePath = storage_path('app/students/Enrollment OEM.xlsx');

if (!file_exists($filePath)) {
    echo "❌ Excel file not found: $filePath\n";
    exit(1);
}

echo "✅ Excel file found: $filePath\n";
echo "📅 Last modified: " . date('Y-m-d H:i:s', filemtime($filePath)) . "\n";
echo "📊 File size: " . number_format(filesize($filePath)) . " bytes\n\n";

// Check cache status
$lastImportTime = cache()->get('last_auto_import_time', 0);
$automationStatus = cache()->get('automation_status', 'stopped');

echo "🔄 Automation Status: " . $automationStatus . "\n";
echo "🕒 Last import: " . ($lastImportTime > 0 ? date('Y-m-d H:i:s', $lastImportTime) : 'Never') . "\n";

// Check if file has been modified since last import
$fileModified = filemtime($filePath);
if ($fileModified > $lastImportTime) {
    echo "🔄 File has been modified since last import - automation should run\n";
    echo "   File modified: " . date('Y-m-d H:i:s', $fileModified) . "\n";
    echo "   Last import: " . ($lastImportTime > 0 ? date('Y-m-d H:i:s', $lastImportTime) : 'Never') . "\n";
} else {
    echo "✅ File unchanged since last import\n";
}

// Test the automation command directly
echo "\n=== TESTING AUTOMATION COMMAND ===\n";
echo "Running: php artisan automation:simple-watch --file=\"$filePath\" --interval=30\n";
echo "This will run for 60 seconds to test detection...\n\n";

// Start the automation in a separate process
$command = "php artisan automation:simple-watch --file=\"{$filePath}\" --interval=10";
$descriptorspec = array(
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("pipe", "w")
);

$process = proc_open($command, $descriptorspec, $pipes);

if (is_resource($process)) {
    echo "✅ Automation process started (PID: " . proc_get_status($process)['pid'] . ")\n";
    echo "⏱️  Running for 60 seconds...\n\n";
    
    // Read output for 60 seconds
    $startTime = time();
    while (time() - $startTime < 60) {
        $output = fread($pipes[1], 1024);
        if ($output) {
            echo $output;
        }
        usleep(100000); // 0.1 second
    }
    
    // Close the process
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($process);
    
    echo "\n✅ Test completed!\n";
} else {
    echo "❌ Failed to start automation process\n";
}

echo "\n=== FINAL STATUS ===\n";
$finalLastImportTime = cache()->get('last_auto_import_time', 0);
echo "🕒 Final last import: " . ($finalLastImportTime > 0 ? date('Y-m-d H:i:s', $finalLastImportTime) : 'Never') . "\n";

if ($finalLastImportTime > $lastImportTime) {
    echo "🎉 SUCCESS: Automation detected changes and imported data!\n";
} else {
    echo "⚠️  No changes detected during test period\n";
}

echo "\n";
