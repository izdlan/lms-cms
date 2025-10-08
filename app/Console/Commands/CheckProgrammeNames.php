<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckProgrammeNames extends Command
{
    protected $signature = 'programmes:check';
    protected $description = 'Check programme names in student records';

    public function handle()
    {
        $this->info('Checking programme names in student records...');
        
        $totalStudents = User::where('role', 'student')->count();
        $studentsWithProgramme = User::where('role', 'student')
            ->whereNotNull('programme_name')
            ->where('programme_name', '!=', '')
            ->count();
            
        $this->info("Total students: {$totalStudents}");
        $this->info("Students with programme names: {$studentsWithProgramme}");
        
        if ($studentsWithProgramme > 0) {
            $sampleStudent = User::where('role', 'student')
                ->whereNotNull('programme_name')
                ->where('programme_name', '!=', '')
                ->first();
                
            $this->info("Sample student: {$sampleStudent->name}");
            $this->info("Sample programme: {$sampleStudent->programme_name}");
        } else {
            $this->warn('No students have programme names!');
            
            // Check if students have programme data in other fields
            $studentsWithProgrammeData = User::where('role', 'student')
                ->where(function($query) {
                    $query->whereNotNull('programme_name')
                          ->orWhere('programme_name', '!=', '')
                          ->orWhereNotNull('category')
                          ->orWhere('category', '!=', '');
                })
                ->count();
                
            $this->info("Students with any programme data: {$studentsWithProgrammeData}");
        }
        
        return 0;
    }
}
