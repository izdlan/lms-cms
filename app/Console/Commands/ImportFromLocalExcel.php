<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\Log;

class ImportFromLocalExcel extends Command
{
    protected $signature = 'programmes:import-local';
    protected $description = 'Import programme names from local Excel file';

    public function handle()
    {
        $this->info('Starting programme names import from local Excel file...');
        
        $excelFile = 'data/Enrollment OEM.xlsx';
        
        if (!file_exists($excelFile)) {
            $this->error("Excel file not found: {$excelFile}");
            return 1;
        }
        
        // Check current state
        $studentsBefore = User::where('role', 'student')
            ->whereNotNull('programme_name')
            ->where('programme_name', '!=', '')
            ->count();
            
        $this->info("Students with programme names before: {$studentsBefore}");
        
        try {
            // Import using the StudentsImport class
            $import = new StudentsImport();
            
            // Process each sheet
            $sheets = [
                10 => 'DHU LMS',
                11 => 'IUC LMS', 
                13 => 'LUC LMS',
                14 => 'EXECUTIVE LMS',
                15 => 'UPM LMS',
                16 => 'TVET LMS'
            ];
            
            $totalCreated = 0;
            $totalUpdated = 0;
            $totalErrors = 0;
            
            foreach ($sheets as $sheetIndex => $sheetName) {
                $this->info("Processing sheet: {$sheetName} (index: {$sheetIndex})");
                
                try {
                    $import->setCurrentSheet($sheetName);
                    Excel::import($import, $excelFile, null, \Maatwebsite\Excel\Excel::XLSX, $sheetIndex);
                    
                    $stats = $import->getStats();
                    $totalCreated += $stats['created'];
                    $totalUpdated += $stats['updated'];
                    $totalErrors += $stats['errors'];
                    
                    $this->info("Sheet {$sheetName}: Created {$stats['created']}, Updated {$stats['updated']}, Errors {$stats['errors']}");
                    
                } catch (\Exception $e) {
                    $this->warn("Error processing sheet {$sheetName}: " . $e->getMessage());
                    $totalErrors++;
                }
            }
            
            $this->info("\n=== Import Summary ===");
            $this->info("Total Created: {$totalCreated}");
            $this->info("Total Updated: {$totalUpdated}");
            $this->info("Total Errors: {$totalErrors}");
            
            // Check final state
            $studentsAfter = User::where('role', 'student')
                ->whereNotNull('programme_name')
                ->where('programme_name', '!=', '')
                ->count();
                
            $this->info("Students with programme names after: {$studentsAfter}");
            
            // Show sample
            $sampleStudent = User::where('role', 'student')
                ->whereNotNull('programme_name')
                ->where('programme_name', '!=', '')
                ->first();
                
            if ($sampleStudent) {
                $this->info("Sample student: {$sampleStudent->name}");
                $this->info("Sample programme: {$sampleStudent->programme_name}");
            }
            
        } catch (\Exception $e) {
            $this->error('Import failed with exception: ' . $e->getMessage());
            Log::error('Local Excel import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
        
        return 0;
    }
}
