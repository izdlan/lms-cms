<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CheckStudentLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student:check-login {ic} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check student login credentials and debug issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ic = $this->argument('ic');
        $password = $this->argument('password');
        
        $this->info("Checking login for IC: {$ic}");
        $this->info("Password provided: {$password}");
        
        // Find student by IC
        $student = User::where('ic', $ic)->where('role', 'student')->first();
        
        if (!$student) {
            $this->error("Student with IC '{$ic}' not found in database.");
            
            // Show all students
            $this->info("\nAll students in database:");
            $allStudents = User::where('role', 'student')->get(['id', 'ic', 'name', 'email']);
            foreach ($allStudents as $s) {
                $this->line("ID: {$s->id}, IC: {$s->ic}, Name: {$s->name}, Email: {$s->email}");
            }
            return;
        }
        
        $this->info("Student found:");
        $this->line("ID: {$student->id}");
        $this->line("IC: {$student->ic}");
        $this->line("Name: {$student->name}");
        $this->line("Email: {$student->email}");
        $this->line("Role: {$student->role}");
        $this->line("Password hash: " . substr($student->password, 0, 20) . "...");
        $this->line("Must reset password: " . ($student->must_reset_password ? 'Yes' : 'No'));
        
        // Test password
        $passwordMatch = Hash::check($password, $student->password);
        $this->info("\nPassword check: " . ($passwordMatch ? 'MATCH' : 'NO MATCH'));
        
        if (!$passwordMatch) {
            $this->warn("Password does not match. Testing common passwords:");
            
            $commonPasswords = ['000000', 'password123', 'password', '123456', 'admin123'];
            foreach ($commonPasswords as $testPassword) {
                $testMatch = Hash::check($testPassword, $student->password);
                $this->line("  {$testPassword}: " . ($testMatch ? 'MATCH' : 'NO MATCH'));
            }
        }
        
        // Test authentication
        $this->info("\nTesting authentication:");
        $credentials = [
            'ic' => $ic,
            'password' => $password,
        ];
        
        if (\Illuminate\Support\Facades\Auth::guard('student')->attempt($credentials)) {
            $this->info("Authentication SUCCESSFUL");
        } else {
            $this->error("Authentication FAILED");
        }
    }
}