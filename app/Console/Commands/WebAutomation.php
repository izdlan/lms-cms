<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\XlsxImportService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class WebAutomation extends Command
{
    protected $signature = 'automation:web-watch 
                            {--file= : Path to Excel file}
                            {--interval=30 : Check interval in seconds}
                            {--daemon : Run as daemon}';
    
    protected $description = 'Web-based automation watcher for student imports';

    private $filePath;
    private $interval;
    private $isRunning = true;

    public function handle()
    {
        $this->filePath = $this->option('file') ?: storage_path('app/students/Enrollment OEM.xlsx');
        $this->interval = (int) $this->option('interval');
        $isDaemon = $this->option('daemon');

        if (!$isDaemon) {
            $this->info('Starting web automation watcher...');
            $this->info("File: {$this->filePath}");
            $this->info("Interval: {$this->interval} seconds");
        }

        // Set status in cache
        Cache::put('automation_status', 'running', now()->addDays(30));
        Cache::put('automation_file', $this->filePath, now()->addDays(30));
        Cache::put('automation_interval', $this->interval, now()->addDays(30));

        // Check if file exists
        if (!file_exists($this->filePath)) {
            $this->error("Excel file not found: {$this->filePath}");
            Cache::put('automation_status', 'error', now()->addDays(30));
            return 1;
        }

        $lastModified = filemtime($this->filePath);
        $lastFileSize = filesize($this->filePath);
        $importCount = 0;

        if (!$isDaemon) {
            $this->info("File found! Starting monitoring...");
            $this->info("Last modified: " . Carbon::createFromTimestamp($lastModified)->toDateTimeString());
            $this->info("File size: " . number_format($lastFileSize) . " bytes");
        }

        // Set up signal handlers for graceful shutdown
        if (function_exists('pcntl_signal')) {
            pcntl_signal(15, [$this, 'shutdown']); // SIGTERM
            pcntl_signal(2, [$this, 'shutdown']);  // SIGINT
        }

        while ($this->isRunning) {
            if (file_exists($this->filePath)) {
                $currentModified = filemtime($this->filePath);
                $currentFileSize = filesize($this->filePath);
                
                // Check both modification time and file size
                if ($currentModified > $lastModified || $currentFileSize !== $lastFileSize) {
                    $importCount++;
                    
                    if (!$isDaemon) {
                        $this->info("[" . date('Y-m-d H:i:s') . "] CHANGE DETECTED! (Import #{$importCount})");
                    }
                    
                    Log::info("Web automation: File change detected", [
                        'file' => $this->filePath,
                        'previous_modified' => Carbon::createFromTimestamp($lastModified)->toDateTimeString(),
                        'current_modified' => Carbon::createFromTimestamp($currentModified)->toDateTimeString(),
                        'previous_size' => $lastFileSize,
                        'current_size' => $currentFileSize,
                        'import_count' => $importCount
                    ]);

                    try {
                        // Clear cache to ensure fresh import
                        Cache::forget('last_auto_import_time');
                        
                        // Run the import
                        $xlsxService = new XlsxImportService();
                        $result = $xlsxService->importFromXlsx($this->filePath);
                        
                        if ($result['success']) {
                            $created = $result['created'];
                            $updated = $result['updated'];
                            $errors = $result['errors'];
                            
                            // Update last import time
                            Cache::put('last_import_time', $currentModified, now()->addDays(30));
                            Cache::put('last_auto_import_time', $currentModified, now()->addDays(30));
                            
                            Log::info("Web automation: Import completed successfully", [
                                'created' => $created,
                                'updated' => $updated,
                                'errors' => $errors,
                                'file' => $this->filePath
                            ]);
                            
                            if (!$isDaemon) {
                                $this->info("  ✅ Import completed: {$created} created, {$updated} updated, {$errors} errors");
                            }
                            
                        } else {
                            Log::error("Web automation: Import failed", $result);
                            
                            if (!$isDaemon) {
                                $this->error("  ❌ Import failed: " . ($result['message'] ?? 'Unknown error'));
                            }
                        }
                        
                        // Update tracking variables
                        $lastModified = $currentModified;
                        $lastFileSize = $currentFileSize;
                        
                    } catch (\Exception $e) {
                        Log::error("Web automation: Import error", [
                            'error' => $e->getMessage(),
                            'file' => $this->filePath
                        ]);
                        
                        if (!$isDaemon) {
                            $this->error("  ❌ Error during import: " . $e->getMessage());
                        }
                    }
                }
            } else {
                Log::warning("Web automation: File not found", ['file' => $this->filePath]);
                
                if (!$isDaemon) {
                    $this->warn("[" . date('Y-m-d H:i:s') . "] File not found: {$this->filePath}");
                }
            }
            
            // Check for shutdown signal
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }
            
            sleep($this->interval);
        }

        // Set status to stopped
        Cache::put('automation_status', 'stopped', now()->addDays(30));
        
        if (!$isDaemon) {
            $this->info("Web automation stopped.");
        }
        
        return 0;
    }

    public function shutdown()
    {
        $this->isRunning = false;
        Cache::put('automation_status', 'stopped', now()->addDays(30));
        
        if (!$this->option('daemon')) {
            $this->info("\nShutting down web automation...");
        }
    }
}