<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateStudentPasswords extends Command
{
    protected $signature = 'students:update-passwords {--password=0000 : New password for all students}';
    protected $description = 'Update all student passwords to a common password';

    public function handle()
    {
        $newPassword = $this->option('password');
        
        $this->info("Updating all student passwords to: {$newPassword}");
        
        // Get all students
        $students = User::where('role', 'student')->get();
        
        if ($students->isEmpty()) {
            $this->warn('No students found in the database.');
            return 0;
        }
        
        $this->info("Found {$students->count()} students to update.");
        
        $updated = 0;
        $errors = 0;
        
        foreach ($students as $student) {
            try {
                $student->update([
                    'password' => Hash::make($newPassword),
                    'must_reset_password' => false
                ]);
                $updated++;
                $this->line("✓ Updated: {$student->name} ({$student->email})");
            } catch (\Exception $e) {
                $errors++;
                $this->error("✗ Failed to update {$student->name}: " . $e->getMessage());
            }
        }
        
        $this->info('');
        $this->info("Password update completed!");
        $this->info("Successfully updated: {$updated} students");
        if ($errors > 0) {
            $this->warn("Errors: {$errors} students");
        }
        
        $this->info('');
        $this->info('Students can now login with:');
        $this->info('- Username: Their email address');
        $this->info("- Password: {$newPassword}");
        $this->info('- Or using IC/Passport number as username');
        
        return 0;
    }
}