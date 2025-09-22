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

        $this->info('Import finished for all allowed sheets.');
        return 0;
    }
}