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
        
        try {
            Excel::import(new StudentsImport, $file, null, \Maatwebsite\Excel\Excel::XLSX, 'DHU LMS');
            $this->info('Student sync completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error during sync: ' . $e->getMessage());
            return 1;
        }
    }
}