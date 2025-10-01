<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AutoOneDriveSyncService;

class AutoSyncOneDrive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:onedrive {--force : Force sync regardless of time/hash checks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically sync students from OneDrive Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting OneDrive auto-sync...');
        
        $syncService = new AutoOneDriveSyncService();
        
        // Check if force sync is requested
        if ($this->option('force')) {
            $this->info('Force sync requested - ignoring time and hash checks');
            $result = $syncService->forceSync();
        } else {
            $result = $syncService->performSync();
        }
        
        if ($result['success']) {
            if (isset($result['skipped']) && $result['skipped']) {
                $this->info('✅ Sync skipped - not needed');
            } else {
                $this->info('✅ Sync completed successfully');
                $this->line("📊 Results:");
                $this->line("   • New students: " . ($result['new_students'] ?? 0));
                $this->line("   • Created: " . ($result['created'] ?? 0));
                $this->line("   • Updated: " . ($result['updated'] ?? 0));
                $this->line("   • Errors: " . ($result['errors'] ?? 0));
                
                if (isset($result['processed_sheets']) && is_array($result['processed_sheets'])) {
                    $this->line("   • Processed sheets: " . count($result['processed_sheets']));
                }
            }
        } else {
            $this->error('❌ Sync failed: ' . ($result['message'] ?? 'Unknown error'));
            return 1;
        }
        
        // Show sync status
        $status = $syncService->getSyncStatus();
        $this->line("\n📈 Sync Status:");
        $this->line("   • Last sync: " . $status['last_sync']);
        $this->line("   • Next sync in: " . $status['next_sync_in_minutes'] . " minutes");
        $this->line("   • Sync interval: " . $status['sync_interval_minutes'] . " minutes");
        $this->line("   • File hash: " . $status['file_hash']);
        $this->line("   • Configured: " . ($status['is_configured'] ? 'Yes' : 'No'));
        
        return 0;
    }
}