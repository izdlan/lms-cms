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
        
        // Convert relative paths to absolute paths
        $this->info('Original file path: ' . $file);
        if (!file_exists($file)) {
            // Try with storage_path if it's a relative path
            $fullPath = storage_path('app/' . ltrim($file, 'storage/app/'));
            $this->info('Trying full path: ' . $fullPath);
            if (file_exists($fullPath)) {
                $file = $fullPath;
                $this->info('Using full path: ' . $file);
            } else {
                $this->error('File not found: '.$file);
                $this->error('Also tried: '.$fullPath);
                return 1;
            }
        } else {
            $this->info('File found at: ' . $file);
        }

        // Process the first sheet (index 0) which contains the student data
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        
        $this->info("Processing first sheet (index 0)");
        try {
            // Use toArray method instead of import method
            $this->info('Reading Excel file: ' . $file);
            $data = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array): array
                {
                    return $array;
                }
            }, $file);
            
            if (empty($data) || !isset($data[0])) {
                $this->error('No data found in Excel file');
                return 1;
            }
            
            $this->info('Found ' . count($data) . ' sheets, processing first sheet with ' . count($data[0]) . ' rows');
            
            // Process the data using our import class
            $import = new StudentsImport();
            $import->setCurrentSheet('Sheet 0');
            $import->collection(collect($data[0]));
            
            $stats = $import->getStats();
            $totalCreated += $stats['created'];
            $totalUpdated += $stats['updated'];
            $totalErrors += $stats['errors'];
            
            $this->info("Completed processing - Created: {$stats['created']}, Updated: {$stats['updated']}, Errors: {$stats['errors']}");
        } catch (\Exception $e) {
            $this->warn("Error processing sheet: " . $e->getMessage());
            $this->warn("Stack trace: " . $e->getTraceAsString());
            $totalErrors++;
        }

        $this->info('Import finished for all allowed sheets.');
        $this->info("Total Summary - Created: {$totalCreated}, Updated: {$totalUpdated}, Errors: {$totalErrors}");
        return 0;
    }
}