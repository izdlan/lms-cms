<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConfigureEmail extends Command
{
    protected $signature = 'email:configure
                            {--driver= : Mail driver (smtp, log, mailgun, etc.)}
                            {--host= : SMTP host}
                            {--port= : SMTP port}
                            {--username= : SMTP username}
                            {--password= : SMTP password}
                            {--from-email= : From email address}
                            {--from-name= : From name}';

    protected $description = 'Configure email settings for the application';

    public function handle()
    {
        $this->info('Email Configuration Setup');
        $this->info('========================');

        // Get current .env file
        $envPath = base_path('.env');
        if (!File::exists($envPath)) {
            $this->error('.env file not found!');
            return 1;
        }

        $envContent = File::get($envPath);
        $lines = explode("\n", $envContent);

        // Get configuration options
        $driver = $this->option('driver') ?: $this->choice(
            'Select mail driver',
            ['log' => 'Log (write to log file)', 'smtp' => 'SMTP (send real emails)', 'mailpit' => 'Mailpit (development testing)'],
            'log'
        );

        $host = $this->option('host');
        $port = $this->option('port');
        $username = $this->option('username');
        $password = $this->option('password');
        $fromEmail = $this->option('from-email');
        $fromName = $this->option('from-name');

        if ($driver === 'smtp') {
            $host = $host ?: $this->ask('Enter SMTP host (e.g., smtp.gmail.com)');
            $port = $port ?: $this->ask('Enter SMTP port', '587');
            $username = $username ?: $this->ask('Enter SMTP username');
            $password = $password ?: $this->secret('Enter SMTP password');
        } elseif ($driver === 'mailpit') {
            $host = 'mailpit';
            $port = '1025';
            $username = 'null';
            $password = 'null';
        } else {
            $host = 'localhost';
            $port = '587';
            $username = 'null';
            $password = 'null';
        }

        $fromEmail = $fromEmail ?: $this->ask('Enter from email address', 'admin@example.com');
        $fromName = $fromName ?: $this->ask('Enter from name', 'LMS Olympia');

        // Update .env file
        $updatedLines = [];
        $mailSettings = [
            'MAIL_MAILER' => $driver,
            'MAIL_HOST' => $host,
            'MAIL_PORT' => $port,
            'MAIL_USERNAME' => $username,
            'MAIL_PASSWORD' => $password,
            'MAIL_ENCRYPTION' => $driver === 'smtp' ? 'tls' : 'null',
            'MAIL_FROM_ADDRESS' => $fromEmail,
            'MAIL_FROM_NAME' => $fromName,
        ];

        foreach ($lines as $line) {
            $updated = false;
            foreach ($mailSettings as $key => $value) {
                if (strpos($line, $key . '=') === 0) {
                    $updatedLines[] = $key . '=' . $value;
                    $updated = true;
                    break;
                }
            }
            if (!$updated) {
                $updatedLines[] = $line;
            }
        }

        // Write updated .env file
        File::put($envPath, implode("\n", $updatedLines));

        $this->info('');
        $this->info('Email configuration updated successfully!');
        $this->info('');
        $this->info('Configuration Summary:');
        $this->info('====================');
        $this->info("Driver: {$driver}");
        $this->info("Host: {$host}");
        $this->info("Port: {$port}");
        $this->info("From: {$fromEmail} ({$fromName})");

        if ($driver === 'log') {
            $this->info('');
            $this->warn('Note: Using log driver - emails will be written to storage/logs/laravel.log');
            $this->info('To send real emails, run: php artisan email:configure --driver=smtp');
        } elseif ($driver === 'mailpit') {
            $this->info('');
            $this->warn('Note: Using mailpit - make sure mailpit is running');
            $this->info('To start mailpit: docker run -d -p 1025:1025 -p 8025:8025 axllent/mailpit');
        }

        $this->info('');
        $this->info('To test email configuration:');
        $this->info('php artisan tinker');
        $this->info('Mail::raw("Test email", function($msg) { $msg->to("your@email.com")->subject("Test"); });');

        return 0;
    }
}