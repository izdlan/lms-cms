<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SetupAutomation extends Command
{
    protected $signature = 'automation:setup 
                            {--file= : Path to Excel file}
                            {--email= : Admin email for notifications}
                            {--frequency=hourly : Import frequency (hourly, every-6-hours, daily, weekly)}';
    
    protected $description = 'Setup student import automation';

    public function handle()
    {
        $this->info('Setting up Student Import Automation...');
        $this->info('=====================================');

        // Get configuration
        $filePath = $this->option('file') ?: $this->ask('Enter Excel file path', storage_path('app/students/Enrollment OEM.xlsx'));
        $email = $this->option('email') ?: $this->ask('Enter admin email for notifications');
        $frequency = $this->option('frequency') ?: $this->choice('Select import frequency', [
            'every-minute' => 'Every Minute',
            'every-5-minutes' => 'Every 5 Minutes',
            'every-15-minutes' => 'Every 15 Minutes',
            'hourly' => 'Every Hour',
            'daily' => 'Daily'
        ], 'every-minute');

        // Validate file path
        if (!file_exists($filePath)) {
            $this->error("Excel file not found: {$filePath}");
            
            if ($this->confirm('Would you like to create the directory and continue?')) {
                $directory = dirname($filePath);
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                    $this->info("Created directory: {$directory}");
                }
            } else {
                return 1;
            }
        }

        // Create storage directory if it doesn't exist
        $storageDir = storage_path('app/students');
        if (!File::exists($storageDir)) {
            File::makeDirectory($storageDir, 0755, true);
            $this->info("Created storage directory: {$storageDir}");
        }

        // Test the import command
        $this->info('Testing import command...');
        try {
            $exitCode = $this->call('students:auto-import', [
                '--file' => $filePath,
                '--email' => $email,
                '--force' => true
            ]);

            if ($exitCode === 0) {
                $this->info('✓ Import test successful');
            } else {
                $this->warn('⚠ Import test completed with warnings');
            }
        } catch (\Exception $e) {
            $this->error('✗ Import test failed: ' . $e->getMessage());
            return 1;
        }

        // Create configuration file
        $config = [
            'excel_file' => $filePath,
            'notification_email' => $email,
            'import_frequency' => $frequency,
            'enabled' => true,
            'last_setup' => now()->toDateTimeString()
        ];

        $configPath = storage_path('app/automation.json');
        File::put($configPath, json_encode($config, JSON_PRETTY_PRINT));
        $this->info("✓ Configuration saved to: {$configPath}");

        // Display setup summary
        $this->info('');
        $this->info('Setup Summary:');
        $this->info('==============');
        $this->info("Excel File: {$filePath}");
        $this->info("Notification Email: {$email}");
        $this->info("Import Frequency: {$frequency}");
        $this->info('Status: Enabled');

        // Display next steps
        $this->info('');
        $this->info('Next Steps:');
        $this->info('===========');
        $this->info('1. Make sure your cron job is set up to run Laravel scheduler:');
        $this->info('   * * * * * cd ' . base_path() . ' && php artisan schedule:run >> /dev/null 2>&1');
        $this->info('   (This runs every minute to check for scheduled tasks)');
        $this->info('');
        $this->info('2. Test the automation:');
        $this->info('   php artisan students:auto-import');
        $this->info('');
        $this->info('3. Monitor the automation:');
        $this->info('   Visit /admin/automation in your web browser');
        $this->info('');
        $this->info('4. Check logs:');
        $this->info('   tail -f storage/logs/laravel.log');
        $this->info('');
        $this->info('5. Automation runs every minute to check for file changes');

        $this->info('');
        $this->info('✓ Automation setup completed successfully!');

        return 0;
    }
}
