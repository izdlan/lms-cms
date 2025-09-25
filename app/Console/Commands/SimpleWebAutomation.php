<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\XlsxImportService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SimpleWebAutomation extends Command
{
    protected $signature = 'automation:simple-watch 
                            {--file= : Path to Excel file}
                            {--interval=30 : Check interval in seconds}';
    
    protected $description = 'Simple web automation watcher for student imports';

    public function handle()
    {
        $filePath = $this->option('file') ?: storage_path('app/students/Enrollment OEM.xlsx');
        $interval = (int) $this->option('interval');

        $this->info('Starting simple web automation...');
        $this->info("File: {$filePath}");
        $this->info("Interval: {$interval} seconds");

        // Check if file exists
        if (!file_exists($filePath)) {
            $this->error("Excel file not found: {$filePath}");
            Cache::put('automation_status', 'error', now()->addDays(30));
            return 1;
        }

        // Set status to running
        Cache::put('automation_status', 'running', now()->addDays(30));
        Cache::put('automation_file', $filePath, now()->addDays(30));
        Cache::put('automation_interval', $interval, now()->addDays(30));

        $lastModified = filemtime($filePath);
        $lastFileSize = filesize($filePath);
        $importCount = 0;

        $this->info("File found! Starting monitoring...");
        $this->info("Last modified: " . Carbon::createFromTimestamp($lastModified)->toDateTimeString());
        $this->info("File size: " . number_format($lastFileSize) . " bytes");
        $this->info("Press Ctrl+C to stop");

        while (true) {
            try {
                if (file_exists($filePath)) {
                    $currentModified = filemtime($filePath);
                    $currentFileSize = filesize($filePath);
                    
                    // Check both modification time and file size
                    if ($currentModified > $lastModified || $currentFileSize !== $lastFileSize) {
                        $importCount++;
                        
                        $this->info("[" . date('Y-m-d H:i:s') . "] CHANGE DETECTED! (Import #{$importCount})");
                        $this->info("  Previous: " . Carbon::createFromTimestamp($lastModified)->toDateTimeString());
                        $this->info("  Current:  " . Carbon::createFromTimestamp($currentModified)->toDateTimeString());
                        $this->info("  Size: " . number_format($lastFileSize) . " → " . number_format($currentFileSize) . " bytes");
                        
                        Log::info("Simple web automation: File change detected", [
                            'file' => $filePath,
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
                            $result = $xlsxService->importFromXlsx($filePath);
                            
                            if ($result['success']) {
                                $created = $result['created'];
                                $updated = $result['updated'];
                                $errors = $result['errors'];
                                
                                // Update last import time
                                Cache::put('last_import_time', $currentModified, now()->addDays(30));
                                Cache::put('last_auto_import_time', $currentModified, now()->addDays(30));
                                
                                // Store import results for web display
                                Cache::put('last_import_results', [
                                    'created' => $created,
                                    'updated' => $updated,
                                    'errors' => $errors,
                                    'timestamp' => now()->toDateTimeString()
                                ], now()->addDays(30));
                                
                                Log::info("Simple web automation: Import completed successfully", [
                                    'created' => $created,
                                    'updated' => $updated,
                                    'errors' => $errors,
                                    'file' => $filePath
                                ]);
                                
                                $this->info("  ✅ Import completed: {$created} created, {$updated} updated, {$errors} errors");
                                
                            } else {
                                Log::error("Simple web automation: Import failed", $result);
                                $this->error("  ❌ Import failed: " . ($result['message'] ?? 'Unknown error'));
                            }
                            
                            // Update tracking variables
                            $lastModified = $currentModified;
                            $lastFileSize = $currentFileSize;
                            
                        } catch (\Exception $e) {
                            Log::error("Simple web automation: Import error", [
                                'error' => $e->getMessage(),
                                'file' => $filePath
                            ]);
                            $this->error("  ❌ Error during import: " . $e->getMessage());
                        }
                    } else {
                        // Show status every 2 minutes
                        if (time() % 120 === 0) {
                            $this->info("[" . date('Y-m-d H:i:s') . "] Monitoring... (No changes detected)");
                        }
                    }
                } else {
                    $this->warn("[" . date('Y-m-d H:i:s') . "] File not found: {$filePath}");
                    Log::warning("Simple web automation: File not found", ['file' => $filePath]);
                }
                
            } catch (\Exception $e) {
                $this->error("[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage());
                Log::error("Simple web automation: General error", [
                    'error' => $e->getMessage(),
                    'file' => $filePath
                ]);
            }
            
            sleep($interval);
        }
    }
}

