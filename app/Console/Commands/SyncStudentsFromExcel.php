<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class SyncStudentsFromExcel extends Command
{
    protected $signature = 'students:sync {file}';
    protected $description = 'Sync students from Excel file (updates existing and adds new students)';

    public function handle()
    {
        $file = $this->argument('file');
        
        if (!file_exists($file)) {
            $this->error('File not found: ' . $file);
            return 1;
        }

        $this->info('Starting student sync from: ' . $file);
        
        $allowedSheets = ['DHU LMS', 'IUC LMS', 'VIVA-IUC LMS', 'LUC LMS'];
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;

        foreach ($allowedSheets as $sheetName) {
            $this->info("Processing sheet: {$sheetName}");
            try {
                $import = new StudentsImport();
                $import->setCurrentSheet($sheetName);
                Excel::import($import, $file, null, \Maatwebsite\Excel\Excel::XLSX, $sheetName);
                $this->info("Completed processing sheet: {$sheetName}");
            } catch (\Exception $e) {
                $this->warn("Error processing sheet {$sheetName}: " . $e->getMessage());
            }
        }

        $this->info('Student sync completed for all allowed sheets!');
        return 0;
    }
}