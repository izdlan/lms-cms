<?php
/**
 * Excel File Automation Script
 * 
 * This script monitors an Excel file for changes and automatically
 * imports student data into the LMS Olympia system.
 * 
 * @author LMS Olympia Team
 * @version 1.0.0
 * @since 2025-09-24
 */

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\XlsxImportService;

class ExcelAutomation
{
    private $xlsxService;
    private $config;
    private $lastModified;
    
    public function __construct()
    {
        $this->xlsxService = new XlsxImportService();
        $this->config = [
            'file_path' => storage_path('app/students/Enrollment OEM.xlsx'),
            'check_interval' => 60, // 1 minute
            'log_file' => __DIR__ . '/../logs/excel_automation.log',
            'max_retries' => 3,
            'retry_delay' => 30 // seconds
        ];
        $this->lastModified = file_exists($this->config['file_path']) ? filemtime($this->config['file_path']) : 0;
    }
    
    public function run()
    {
        $this->log("Starting Excel File Automation...");
        $this->log("Monitoring file: " . $this->config['file_path']);
        $this->log("Check interval: " . $this->config['check_interval'] . " seconds");
        $this->log("Press Ctrl+C to stop");
        $this->log("=" . str_repeat("=", 60));
        
        $retryCount = 0;
        
        while (true) {
            try {
                $this->log("[" . $this->getCurrentTime() . "] Checking Excel file for changes...");
                
                if (file_exists($this->config['file_path'])) {
                    $currentModified = filemtime($this->config['file_path']);
                    
                    if ($currentModified > $this->lastModified) {
                        $this->log("[" . $this->getCurrentTime() . "] File change detected! Starting import...");
                        
                        $result = $this->xlsxService->importFromXlsx($this->config['file_path']);
                        
                        if ($result['success']) {
                            $this->log("[" . $this->getCurrentTime() . "] ✓ Import completed successfully");
                            $this->log("  Created: " . $result['created'] . " students");
                            $this->log("  Updated: " . $result['updated'] . " students");
                            $this->log("  Errors: " . $result['errors'] . " students");
                            $this->lastModified = $currentModified;
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
                } else {
                    $this->log("[" . $this->getCurrentTime() . "] File not found: " . $this->config['file_path']);
                    $retryCount++;
                    
                    if ($retryCount >= $this->config['max_retries']) {
                        $this->log("[" . $this->getCurrentTime() . "] ✗ Maximum retries reached. File not found.");
                        break;
                    }
                    
                    $this->log("[" . $this->getCurrentTime() . "] Retrying in " . $this->config['retry_delay'] . " seconds... (Attempt " . $retryCount . "/" . $this->config['max_retries'] . ")");
                    sleep($this->config['retry_delay']);
                    continue;
                }
                
                $this->log("[" . $this->getCurrentTime() . "] Next check in " . $this->config['check_interval'] . " seconds...");
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
        
        $this->log("[" . $this->getCurrentTime() . "] Excel automation stopped.");
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
$automation = new ExcelAutomation();
$automation->run();

