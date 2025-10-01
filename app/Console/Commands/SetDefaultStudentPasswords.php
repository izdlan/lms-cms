<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SetDefaultStudentPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:set-default-password {--password=000000 : The default password to set}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set default password for all students in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $defaultPassword = $this->option('password');
        
        // Get all students
        $students = User::where('role', 'student')->get();
        
        if ($students->isEmpty()) {
            $this->info('No students found in the system.');
            return;
        }
        
        $this->info("Found {$students->count()} students. Setting default password to: {$defaultPassword}");
        
        $bar = $this->output->createProgressBar($students->count());
        $bar->start();
        
        $updated = 0;
        
        foreach ($students as $student) {
            $student->update([
                'password' => Hash::make($defaultPassword),
                'must_reset_password' => true, // Force password reset on first login
            ]);
            $updated++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Successfully updated {$updated} students with default password: {$defaultPassword}");
        $this->info('All students will be required to change their password on first login.');
        
        // Show some sample student credentials
        $this->newLine();
        $this->info('Sample student login credentials:');
        $sampleStudents = $students->take(5);
        foreach ($sampleStudents as $student) {
            $this->line("IC: {$student->ic}, Password: {$defaultPassword}");
        }
        
        if ($students->count() > 5) {
            $this->line("... and " . ($students->count() - 5) . " more students");
        }
    }
}