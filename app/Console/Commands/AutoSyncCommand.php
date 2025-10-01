<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AutoSyncService;
use Illuminate\Support\Facades\Log;

class AutoSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-sync:run {--continuous : Run continuously}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run auto-sync process';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $autoSyncService = new AutoSyncService();
        
        if ($this->option('continuous')) {
            $this->info('Starting continuous auto-sync...');
            $this->runContinuous($autoSyncService);
        } else {
            $this->info('Running single auto-sync...');
            $this->runSingle($autoSyncService);
        }
    }
    
    private function runSingle($autoSyncService)
    {
        $result = $autoSyncService->performAutoSync();
        
        if ($result['success']) {
            if ($result['skipped']) {
                $this->info('Auto-sync skipped - not yet time for next sync');
            } else {
                $this->info('Auto-sync completed successfully');
                if (isset($result['result'])) {
                    $this->info("Created: {$result['result']['created']}, Updated: {$result['result']['updated']}, Errors: {$result['result']['errors']}");
                }
            }
        } else {
            $this->error('Auto-sync failed: ' . $result['message']);
        }
    }
    
    private function runContinuous($autoSyncService)
    {
        $this->info('Auto-sync will run every 5 minutes. Press Ctrl+C to stop.');
        
        while (true) {
            try {
                $result = $autoSyncService->performAutoSync();
                
                $timestamp = now()->format('Y-m-d H:i:s');
                
                if ($result['success']) {
                    if ($result['skipped']) {
                        $this->line("[{$timestamp}] Auto-sync skipped - not yet time for next sync");
                    } else {
                        $this->info("[{$timestamp}] Auto-sync completed successfully");
                        if (isset($result['result'])) {
                            $this->line("  Created: {$result['result']['created']}, Updated: {$result['result']['updated']}, Errors: {$result['result']['errors']}");
                        }
                    }
                } else {
                    $this->error("[{$timestamp}] Auto-sync failed: " . $result['message']);
                }
                
                // Wait 5 minutes (300 seconds)
                sleep(300);
                
            } catch (\Exception $e) {
                $this->error("[{$timestamp}] Auto-sync error: " . $e->getMessage());
                Log::error('Auto-sync continuous error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Wait 1 minute before retrying on error
                sleep(60);
            }
        }
    }
}


