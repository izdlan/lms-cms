<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckStudents extends Command
{
    protected $signature = 'check:students';
    protected $description = 'Check imported students';

    public function handle()
    {
        $totalStudents = User::where('role', 'student')->count();
        $this->info("Total students: {$totalStudents}");
        
        $recentStudents = User::where('role', 'student')
            ->latest()
            ->take(5)
            ->get(['name', 'email', 'ic', 'phone', 'address', 'student_id']);
            
        $this->info("\nRecent students:");
        foreach ($recentStudents as $student) {
            $this->info("- {$student->name} | {$student->email} | {$student->ic} | {$student->phone}");
        }
        
        return 0;
    }
}