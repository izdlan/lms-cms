<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\OneDriveExcelImportService;

class OneDriveImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes timeout
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting OneDrive import job');
        
        try {
            $oneDriveService = new OneDriveExcelImportService();
            $result = $oneDriveService->importFromOneDrive();
            
            if ($result['success']) {
                Log::info('OneDrive import job completed successfully', [
                    'created' => $result['created'],
                    'updated' => $result['updated'],
                    'errors' => $result['errors']
                ]);
            } else {
                Log::error('OneDrive import job failed', [
                    'error' => $result['error']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('OneDrive import job exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('OneDrive import job failed permanently', [
            'error' => $exception->getMessage()
        ]);
    }
}


