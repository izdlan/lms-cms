<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AutoOneDriveSyncService
{
    protected $oneDriveService;
    protected $lastSyncKey = 'last_onedrive_sync';
    protected $lastFileHashKey = 'last_onedrive_file_hash';
    protected $syncInterval = 5; // minutes

    public function __construct()
    {
        $this->oneDriveService = new OneDriveExcelImportService();
    }

    /**
     * Check if it's time to sync and if there are changes
     */
    public function shouldSync()
    {
        $lastSync = Cache::get($this->lastSyncKey);
        
        // If never synced, sync now
        if (!$lastSync) {
            return true;
        }
        
        // Check if enough time has passed
        $minutesSinceLastSync = now()->diffInMinutes($lastSync);
        if ($minutesSinceLastSync < $this->syncInterval) {
            return false;
        }
        
        // Check if file has changed
        return $this->hasFileChanged();
    }

    /**
     * Check if the OneDrive file has changed by comparing file hash
     */
    protected function hasFileChanged()
    {
        try {
            $oneDriveUrl = config('google_sheets.onedrive_url') ?: env('ONEDRIVE_EXCEL_URL');
            
            if (empty($oneDriveUrl)) {
                Log::warning('OneDrive URL not configured for auto-sync');
                return false;
            }

            // Get file headers to check last modified date
            $response = Http::timeout(10)->head($oneDriveUrl);
            
            if (!$response->successful()) {
                Log::warning('Could not check OneDrive file for changes', [
                    'status' => $response->status(),
                    'url' => $oneDriveUrl
                ]);
                return false;
            }

            $lastModified = $response->header('Last-Modified');
            $currentHash = $lastModified ? md5($lastModified) : null;
            
            if (!$currentHash) {
                // If we can't get last modified, assume it changed
                return true;
            }

            $lastHash = Cache::get($this->lastFileHashKey);
            
            if ($lastHash !== $currentHash) {
                Log::info('OneDrive file has changed, sync needed', [
                    'last_hash' => $lastHash,
                    'current_hash' => $currentHash,
                    'last_modified' => $lastModified
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Error checking OneDrive file changes', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Perform the automatic sync
     */
    public function performSync()
    {
        if (!$this->shouldSync()) {
            Log::info('Auto-sync skipped - not needed');
            return [
                'success' => true,
                'message' => 'Sync not needed',
                'skipped' => true
            ];
        }

        Log::info('Starting automatic OneDrive sync');
        
        // Set execution time limit for auto-sync
        set_time_limit(300); // 5 minutes

        try {
            // Get current student count before import
            $studentsBefore = User::where('role', 'student')->count();
            
            // Perform the import
            $result = $this->oneDriveService->importFromOneDrive();
            
            if (!$result['success']) {
                Log::error('Auto-sync failed', [
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
                return [
                    'success' => false,
                    'message' => 'Import failed: ' . ($result['error'] ?? 'Unknown error'),
                    'error' => $result['error'] ?? 'Unknown error'
                ];
            }

            // Get student count after import
            $studentsAfter = User::where('role', 'student')->count();
            $newStudents = $studentsAfter - $studentsBefore;

            // Update sync tracking
            Cache::put($this->lastSyncKey, now(), now()->addDays(30));
            
            // Update file hash
            $oneDriveUrl = config('google_sheets.onedrive_url') ?: env('ONEDRIVE_EXCEL_URL');
            $response = Http::timeout(10)->head($oneDriveUrl);
            if ($response->successful()) {
                $lastModified = $response->header('Last-Modified');
                if ($lastModified) {
                    Cache::put($this->lastFileHashKey, md5($lastModified), now()->addDays(30));
                }
            }

            Log::info('Auto-sync completed successfully', [
                'students_before' => $studentsBefore,
                'students_after' => $studentsAfter,
                'new_students' => $newStudents,
                'created' => $result['created'] ?? 0,
                'updated' => $result['updated'] ?? 0,
                'errors' => $result['errors'] ?? 0
            ]);

            return [
                'success' => true,
                'message' => $newStudents > 0 ? "Successfully synced {$newStudents} new students" : 'Sync completed - no new students',
                'new_students' => $newStudents,
                'created' => $result['created'] ?? 0,
                'updated' => $result['updated'] ?? 0,
                'errors' => $result['errors'] ?? 0,
                'processed_sheets' => $result['processed_sheets'] ?? []
            ];

        } catch (\Exception $e) {
            Log::error('Auto-sync failed with exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get sync status
     */
    public function getSyncStatus()
    {
        $lastSync = Cache::get($this->lastSyncKey);
        $lastHash = Cache::get($this->lastFileHashKey);
        
        return [
            'last_sync' => $lastSync ? $lastSync->format('Y-m-d H:i:s') : 'Never',
            'sync_interval_minutes' => $this->syncInterval,
            'next_sync_in_minutes' => $lastSync ? max(0, $this->syncInterval - now()->diffInMinutes($lastSync)) : 0,
            'file_hash' => $lastHash ? substr($lastHash, 0, 8) . '...' : 'Unknown',
            'is_configured' => !empty(config('google_sheets.onedrive_url')) || !empty(env('ONEDRIVE_EXCEL_URL'))
        ];
    }

    /**
     * Force a sync (ignore time and hash checks)
     */
    public function forceSync()
    {
        Log::info('Force sync requested');
        
        // Clear the last sync time to force immediate sync
        Cache::forget($this->lastSyncKey);
        
        return $this->performSync();
    }

    /**
     * Set sync interval
     */
    public function setSyncInterval($minutes)
    {
        $this->syncInterval = max(1, $minutes); // Minimum 1 minute
        Log::info("Sync interval set to {$this->syncInterval} minutes");
    }
}
