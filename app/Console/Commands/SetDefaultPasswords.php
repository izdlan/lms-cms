<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SetDefaultPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:set-default-password 
                            {--password=000000 : The default password to set}
                            {--role= : Specific role to update (student, lecturer, admin, or all)}
                            {--force : Force update even if password is already set}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set default password for users in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $defaultPassword = $this->option('password');
        $role = $this->option('role');
        $force = $this->option('force');
        
        // Build query based on role
        $query = User::query();
        
        if ($role && $role !== 'all') {
            $query->where('role', $role);
        }
        
        $users = $query->get();
        
        if ($users->isEmpty()) {
            $this->info('No users found with the specified criteria.');
            return;
        }
        
        $roleText = $role ? "{$role} users" : 'all users';
        $this->info("Found {$users->count()} {$roleText}. Setting default password to: {$defaultPassword}");
        
        if (!$force) {
            // Check if any users already have passwords set
            $usersWithPasswords = $users->filter(function ($user) {
                return !empty($user->password) && $user->password !== '';
            });
            
            if ($usersWithPasswords->count() > 0) {
                $this->warn("Warning: {$usersWithPasswords->count()} users already have passwords set.");
                if (!$this->confirm('Do you want to continue and overwrite existing passwords?')) {
                    $this->info('Operation cancelled.');
                    return;
                }
            }
        }
        
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();
        
        $updated = 0;
        
        foreach ($users as $user) {
            $user->update([
                'password' => Hash::make($defaultPassword),
                'must_reset_password' => true, // Force password reset on first login
            ]);
            $updated++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Successfully updated {$updated} users with default password: {$defaultPassword}");
        $this->info('All users will be required to change their password on first login.');
        
        // Show sample credentials by role
        $this->newLine();
        $this->info('Sample login credentials:');
        
        $roles = $users->groupBy('role');
        foreach ($roles as $userRole => $roleUsers) {
            $this->line("\n{$userRole} users:");
            $sampleUsers = $roleUsers->take(3);
            foreach ($sampleUsers as $user) {
                if ($userRole === 'student') {
                    $this->line("  IC: {$user->ic}, Password: {$defaultPassword}");
                } else {
                    $this->line("  Email: {$user->email}, Password: {$defaultPassword}");
                }
            }
            if ($roleUsers->count() > 3) {
                $this->line("  ... and " . ($roleUsers->count() - 3) . " more {$userRole} users");
            }
        }
    }
}