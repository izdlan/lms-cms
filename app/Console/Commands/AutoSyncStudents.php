<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StudentSyncService;

class AutoSyncStudents extends Command
{
    protected $signature = 'students:auto-sync';
    protected $description = 'Automatically sync students from Excel file if it has been updated';

    protected $studentSyncService;

    public function __construct(StudentSyncService $studentSyncService)
    {
        parent::__construct();
        $this->studentSyncService = $studentSyncService;
    }

    public function handle()
    {
        $this->info('Checking for Excel file updates...');

        if (!$this->studentSyncService->isExcelFileUpdated()) {
            $this->info('No updates found in Excel file.');
            return 0;
        }

        $this->info('Excel file has been updated. Starting sync...');

        if ($this->studentSyncService->syncFromExcel()) {
            $this->studentSyncService->updateLastSyncTime();
            $this->info('Students synced successfully!');
            return 0;
        } else {
            $this->error('Failed to sync students from Excel file.');
            return 1;
        }
    }
}