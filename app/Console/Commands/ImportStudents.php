<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class ImportStudents extends Command
{
    protected $signature = 'import:students {file}';
    protected $description = 'Import students from an Excel file';

    public function handle()
    {
        $file = $this->argument('file');
        if (!file_exists($file)) {
            $this->error('File not found: '.$file);
            return 1;
        }

        Excel::import(new StudentsImport, $file, null, \Maatwebsite\Excel\Excel::XLSX, 'DHU LMS');
        $this->info('Import finished.');
        return 0;
    }
}