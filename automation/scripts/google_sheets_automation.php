<?php
/**
 * Google Sheets Automation Script
 * 
 * This script monitors a Google Sheets document for changes and automatically
 * imports student data into the LMS Olympia system.
 * 
 * @author LMS Olympia Team
 * @version 1.0.0
 * @since 2025-09-24
 */

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\GoogleSheetsImportService;

class GoogleSheetsAutomation
{
    private $googleSheetsService;
    private $config;
    
    public function __construct()
    {
        $this->googleSheetsService = new GoogleSheetsImportService();
        $this->config = [
            'google_sheets_url' => 'https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk/edit?usp=sharing&ouid=117738643589016699947&rtpof=true&sd=true',
            'check_interval' => 300, // 5 minutes
            'log_file' => __DIR__ . '/../logs/google_sheets_automation.log',
            'max_retries' => 3,
            'retry_delay' => 60 // seconds
        ];
    }
    
    public function run()
    {
        $this->log("Starting Google Sheets Automation...");
        $this->log("Monitoring: " . $this->config['google_sheets_url']);
        $this->log("Check interval: " . ($this->config['check_interval'] / 60) . " minutes");
        $this->log("Press Ctrl+C to stop");
        $this->log("=" . str_repeat("=", 60));
        
        $retryCount = 0;
        
        while (true) {
            try {
                $this->log("[" . $this->getCurrentTime() . "] Checking Google Sheets for changes...");
                
                if ($this->googleSheetsService->checkForChanges()) {
                    $this->log("[" . $this->getCurrentTime() . "] Changes detected! Starting import...");
                    
                    $result = $this->googleSheetsService->importFromGoogleSheets();
                    
                    if ($result['success']) {
                        $this->log("[" . $this->getCurrentTime() . "] ✓ Import completed successfully");
                        $this->log("  Created: " . $result['created'] . " students");
                        $this->log("  Updated: " . $result['updated'] . " students");
                        $this->log("  Errors: " . $result['errors'] . " students");
                        $retryCount = 0; // Reset retry count on success
                    } else {
                        $this->log("[" . $this->getCurrentTime() . "] ✗ Import failed: " . ($result['message'] ?? 'Unknown error'));
                        $retryCount++;
                        
                        if ($retryCount >= $this->config['max_retries']) {
                            $this->log("[" . $this->getCurrentTime() . "] ✗ Maximum retries reached. Stopping automation.");
                            break;
                        }
                        
                        $this->log("[" . $this->getCurrentTime() . "] Retrying in " . $this->config['retry_delay'] . " seconds... (Attempt " . $retryCount . "/" . $this->config['max_retries'] . ")");
                        sleep($this->config['retry_delay']);
                        continue;
                    }
                } else {
                    $this->log("[" . $this->getCurrentTime() . "] No changes detected");
                }
                
                $this->log("[" . $this->getCurrentTime() . "] Next check in " . ($this->config['check_interval'] / 60) . " minutes...");
                $this->log(""); // Empty line for readability
                
                sleep($this->config['check_interval']);
                
            } catch (Exception $e) {
                $this->log("[" . $this->getCurrentTime() . "] ✗ Error during check/import: " . $e->getMessage());
                $retryCount++;
                
                if ($retryCount >= $this->config['max_retries']) {
                    $this->log("[" . $this->getCurrentTime() . "] ✗ Maximum retries reached. Stopping automation.");
                    break;
                }
                
                $this->log("[" . $this->getCurrentTime() . "] Retrying in " . $this->config['retry_delay'] . " seconds... (Attempt " . $retryCount . "/" . $this->config['max_retries'] . ")");
                sleep($this->config['retry_delay']);
            }
        }
        
        $this->log("[" . $this->getCurrentTime() . "] Google Sheets automation stopped.");
    }
    
    private function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        
        // Output to console
        echo $logMessage;
        
        // Write to log file
        file_put_contents($this->config['log_file'], $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    private function getCurrentTime()
    {
        return date('Y-m-d H:i:s', strtotime('+8 hours'));
    }
}

// Run the automation
$automation = new GoogleSheetsAutomation();
$automation->run();

