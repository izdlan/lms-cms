<?php

namespace App\Services;

use App\Models\SyncActivity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AutoSyncService
{
    protected $googleSheetsService;
    protected $syncIntervalMinutes;
    
    public function __construct()
    {
        $this->googleSheetsService = new GoogleSheetsImportService();
        $this->syncIntervalMinutes = 5; // Default 5 minutes
    }
    
    /**
     * Check if auto-sync should run based on interval
     */
    public function shouldRunSync()
    {
        $lastSync = Cache::get('last_google_sheets_sync');
        
        if (!$lastSync) {
            Log::info('Auto-sync: No previous sync found, running first sync');
            return true;
        }
        
        $minutesSinceLastSync = $lastSync->diffInMinutes(now());
        $shouldRun = $minutesSinceLastSync >= $this->syncIntervalMinutes;
        
        Log::info('Auto-sync check', [
            'last_sync' => $lastSync->format('Y-m-d H:i:s'),
            'minutes_since_last_sync' => $minutesSinceLastSync,
            'sync_interval' => $this->syncIntervalMinutes,
            'should_run' => $shouldRun
        ]);
        
        return $shouldRun;
    }
    
    /**
     * Perform auto-sync if needed
     */
    public function performAutoSync()
    {
        if (!$this->shouldRunSync()) {
            return [
                'success' => true,
                'message' => 'Auto-sync skipped - not yet time for next sync',
                'skipped' => true
            ];
        }
        
        Log::info('Auto-sync: Starting automatic Google Sheets import');
        
        try {
            $result = $this->googleSheetsService->importFromGoogleSheets();
            
            // Update cache with new sync time
            Cache::put('last_google_sheets_sync', now(), now()->addDays(30));
            Cache::put('last_google_sheets_import_time', now(), now()->addDays(30));
            Cache::put('last_google_sheets_import_results', $result, now()->addDays(30));
            
            Log::info('Auto-sync: Completed successfully', $result);
            
            return [
                'success' => true,
                'message' => 'Auto-sync completed successfully',
                'skipped' => false,
                'result' => $result
            ];
            
        } catch (\Exception $e) {
            Log::error('Auto-sync: Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Log error activity
            SyncActivity::logActivity('auto_sync', 'error', 'Auto-sync failed: ' . $e->getMessage(), [
                'created' => 0,
                'updated' => 0,
                'errors' => 1,
                'source' => 'google_sheets'
            ]);
            
            return [
                'success' => false,
                'message' => 'Auto-sync failed: ' . $e->getMessage(),
                'skipped' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Force sync (ignores interval check)
     */
    public function forceSync()
    {
        Log::info('Auto-sync: Force sync requested');
        
        try {
            $result = $this->googleSheetsService->importFromGoogleSheets();
            
            // Update cache with new sync time
            Cache::put('last_google_sheets_sync', now(), now()->addDays(30));
            Cache::put('last_google_sheets_import_time', now(), now()->addDays(30));
            Cache::put('last_google_sheets_import_results', $result, now()->addDays(30));
            
            Log::info('Auto-sync: Force sync completed', $result);
            
            return [
                'success' => true,
                'message' => 'Force sync completed successfully',
                'result' => $result
            ];
            
        } catch (\Exception $e) {
            Log::error('Auto-sync: Force sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Force sync failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get sync status with real-time data
     */
    public function getSyncStatus()
    {
        $lastSync = Cache::get('last_google_sheets_sync');
        $lastImportTime = Cache::get('last_google_sheets_import_time');
        $lastImportResults = Cache::get('last_google_sheets_import_results');
        
        $lastSyncFormatted = 'Never';
        if ($lastSync) {
            $lastSyncFormatted = $lastSync->format('Y-m-d H:i:s');
        } elseif ($lastImportTime) {
            $lastSyncFormatted = $lastImportTime->format('Y-m-d H:i:s');
        }
        
        $fileHash = 'Google Sheets file';
        if ($lastImportResults && isset($lastImportResults['processed_sheets'])) {
            $fileHash = 'Google Sheets (' . count($lastImportResults['processed_sheets']) . ' sheets)';
        }
        
        $nextSyncIn = 0;
        if ($lastSync) {
            $minutesSinceLastSync = $lastSync->diffInMinutes(now());
            $nextSyncIn = max(0, $this->syncIntervalMinutes - $minutesSinceLastSync);
        }
        
        return [
            'is_running' => false,
            'last_sync' => $lastSyncFormatted,
            'next_sync_in_minutes' => $nextSyncIn,
            'file_hash' => $fileHash,
            'sync_interval_minutes' => $this->syncIntervalMinutes,
            'is_configured' => !empty(env('GOOGLE_SHEETS_URL'))
        ];
    }
    
    /**
     * Set sync interval
     */
    public function setSyncInterval($minutes)
    {
        $this->syncIntervalMinutes = max(1, min(60, $minutes));
        Cache::put('auto_sync_interval', $this->syncIntervalMinutes, now()->addDays(30));
        
        Log::info('Auto-sync: Interval updated', ['interval' => $this->syncIntervalMinutes]);
        
        return $this->syncIntervalMinutes;
    }
}


