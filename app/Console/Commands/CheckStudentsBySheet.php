<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckStudentsBySheet extends Command
{
    protected $signature = 'check:students-by-sheet';
    protected $description = 'Check students grouped by source sheet';

    public function handle()
    {
        $allowedSheets = ['DHU LMS', 'IUC LMS', 'VIVA-IUC LMS', 'LUC LMS'];
        
        $this->info("Students by Source Sheet:");
        $this->info("========================");
        
        foreach ($allowedSheets as $sheetName) {
            $count = User::where('role', 'student')
                ->where('source_sheet', $sheetName)
                ->count();
                
            $this->info("{$sheetName}: {$count} students");
            
            if ($count > 0) {
                $students = User::where('role', 'student')
                    ->where('source_sheet', $sheetName)
                    ->take(3)
                    ->get(['name', 'email', 'ic']);
                    
                foreach ($students as $student) {
                    $this->info("  - {$student->name} | {$student->email} | {$student->ic}");
                }
                
                if ($count > 3) {
                    $this->info("  ... and " . ($count - 3) . " more");
                }
            }
            $this->info("");
        }
        
        $totalStudents = User::where('role', 'student')->count();
        $this->info("Total students: {$totalStudents}");
        
        return 0;
    }
}
