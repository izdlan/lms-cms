<?php
/**
 * cPanel Auto-Sync Cron Script
 * 
 * This script can be used as a cron job to run auto-sync
 * Place this file in your project root and set up a cron job to run it
 */

// Set the project root path
$projectRoot = __DIR__;

// Change to the project directory
chdir($projectRoot);

// Include the Laravel autoloader
require_once $projectRoot . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $projectRoot . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the current time for logging
$timestamp = date('Y-m-d H:i:s');

echo "[$timestamp] Starting auto-sync check...\n";

try {
    // Run the auto-sync run command
    $exitCode = Artisan::call('auto-sync:run');
    
    if ($exitCode === 0) {
        echo "[$timestamp] Auto-sync check completed successfully.\n";
    } else {
        echo "[$timestamp] Auto-sync check completed with warnings.\n";
    }
    
    // Get the output from the command
    $output = Artisan::output();
    if (!empty($output)) {
        echo "[$timestamp] Output: " . trim($output) . "\n";
    }
    
} catch (Exception $e) {
    echo "[$timestamp] ERROR: " . $e->getMessage() . "\n";
    echo "[$timestamp] Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "[$timestamp] Auto-sync cron job completed.\n";
exit(0);
