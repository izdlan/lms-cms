<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OneDriveExcelImportService;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ReimportProgrammeNames extends Command
{
    protected $signature = 'programmes:reimport';
    protected $description = 'Re-import programme names from Excel file';

    public function handle()
    {
        $this->info('Starting programme names re-import...');
        
        // Check current state
        $studentsBefore = User::where('role', 'student')
            ->whereNotNull('programme_name')
            ->where('programme_name', '!=', '')
            ->count();
            
        $this->info("Students with programme names before: {$studentsBefore}");
        
        try {
            // Use OneDrive import service
            $importService = new OneDriveExcelImportService();
            $result = $importService->importFromOneDrive();
            
            if ($result['success']) {
                $this->info('Import completed successfully!');
                $this->info("Created: {$result['created']} students");
                $this->info("Updated: {$result['updated']} students");
                $this->info("Errors: {$result['errors']} errors");
                
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
                
            } else {
                $this->error('Import failed: ' . ($result['error'] ?? 'Unknown error'));
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('Import failed with exception: ' . $e->getMessage());
            Log::error('Programme re-import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
        
        return 0;
    }
}
