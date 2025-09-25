<?php
/**
 * Continuous Google Sheets Automation Script
 * This script runs every 5 minutes to check for updates in Google Sheets
 */

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\GoogleSheetsImportService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

echo "Starting continuous Google Sheets automation...\n";

// Set up logging
$logFile = __DIR__ . '/../../storage/logs/continuous_automation.log';
if (!file_exists(dirname($logFile))) {
    mkdir(dirname($logFile), 0755, true);
}

function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] {$message}\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    echo $logEntry;
}

logMessage("Continuous automation started");

// Main automation loop
while (true) {
    try {
        // Check if automation is enabled
        $isEnabled = Cache::get('google_sheets_automation_status', 'stopped') === 'running';
        
        if (!$isEnabled) {
            logMessage("Automation is disabled, waiting...");
            sleep(60); // Wait 1 minute before checking again
            continue;
        }
        
        logMessage("Running Google Sheets check...");
        
        $service = new GoogleSheetsImportService();
        
        // Check for changes and import
        if ($service->checkForChanges()) {
            logMessage("Changes detected, running import...");
            
            $result = $service->importFromGoogleSheets();
            
            if ($result['success']) {
                Cache::put('last_google_sheets_import_time', now(), now()->addDays(30));
                Cache::put('last_google_sheets_import_results', $result, now()->addDays(30));
                
                $totalImports = Cache::get('google_sheets_total_imports', 0);
                Cache::put('google_sheets_total_imports', $totalImports + 1, now()->addDays(30));
                
                logMessage("Import completed successfully - Created: {$result['created']}, Updated: {$result['updated']}, Errors: {$result['errors']}");
            } else {
                Cache::put('google_sheets_last_error', $result['message'] ?? 'Unknown error', now()->addDays(30));
                logMessage("Import failed: " . ($result['message'] ?? 'Unknown error'));
            }
        } else {
            logMessage("No changes detected in Google Sheets");
        }
        
        // Update last check time
        Cache::put('google_sheets_automation_last_check', now(), now()->addDays(30));
        
        // Wait 5 minutes before next check
        logMessage("Waiting 5 minutes before next check...");
        sleep(300); // 5 minutes
        
    } catch (Exception $e) {
        Cache::put('google_sheets_last_error', $e->getMessage(), now()->addDays(30));
        logMessage("Error in automation: " . $e->getMessage());
        
        // Wait 2 minutes before retrying on error
        sleep(120);
    }
}
