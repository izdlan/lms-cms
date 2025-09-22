<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\Log;

class StudentSyncService
{
    protected $excelFilePath;

    public function __construct()
    {
        $this->excelFilePath = config('students.excel_file_path');
    }

    /**
     * Sync students from Excel file
     */
    public function syncFromExcel($filePath = null)
    {
        $filePath = $filePath ?: $this->excelFilePath;

        if (!file_exists($filePath)) {
            Log::warning('Students Excel file not found: ' . $filePath);
            return false;
        }

        try {
            Excel::import(new StudentsImport, $filePath);
            Log::info('Students synced successfully from: ' . $filePath);
            return true;
        } catch (\Exception $e) {
            Log::error('Error syncing students from Excel: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if Excel file has been updated
     */
    public function isExcelFileUpdated()
    {
        if (!file_exists($this->excelFilePath)) {
            return false;
        }

        $lastModified = filemtime($this->excelFilePath);
        $lastSync = cache()->get('students_last_sync', 0);

        return $lastModified > $lastSync;
    }

    /**
     * Update last sync timestamp
     */
    public function updateLastSyncTime()
    {
        cache()->put('students_last_sync', time(), now()->addDay());
    }

    /**
     * Get Excel file info
     */
    public function getExcelFileInfo()
    {
        if (!file_exists($this->excelFilePath)) {
            return null;
        }

        return [
            'path' => $this->excelFilePath,
            'size' => filesize($this->excelFilePath),
            'last_modified' => filemtime($this->excelFilePath),
            'exists' => true,
        ];
    }
}
