<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetsImportService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleSheetsImport extends Command
{
    protected $signature = 'students:google-sheets-import 
                            {--force : Force import even if no changes detected}
                            {--email= : Email to notify on completion}';
    
    protected $description = 'Import students from Google Sheets with change detection';

    public function handle()
    {
        $force = $this->option('force');
        $notifyEmail = $this->option('email');

        $this->info('Starting Google Sheets student import...');
        $this->info("Google Sheets URL: https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk");

        $googleSheetsService = new GoogleSheetsImportService();

        // Check for changes unless force flag is used
        if (!$force) {
            $this->info('Checking for changes in Google Sheets...');
            if (!$googleSheetsService->checkForChanges()) {
                $this->info('No changes detected in Google Sheets. Use --force to import anyway.');
                Log::info('Google Sheets import skipped: No changes detected');
                return 0;
            }
            $this->info('Changes detected. Starting import...');
        }

        try {
            $result = $googleSheetsService->importFromGoogleSheets();
            
            if ($result['success']) {
                $totalCreated = $result['created'];
                $totalUpdated = $result['updated'];
                $totalErrors = $result['errors'];
                
                $this->info("✓ Google Sheets import completed successfully");
                Log::info("Google Sheets import completed successfully", $result);
            } else {
                $this->error("✗ Import failed: " . ($result['message'] ?? 'Unknown error'));
                Log::error("Google Sheets import failed", $result);
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error("✗ Error during import: " . $e->getMessage());
            Log::error("Google Sheets import error", [
                'error' => $e->getMessage()
            ]);
            return 1;
        }

        // Log import summary with professional message
        $summary = [
            'total_created' => $totalCreated,
            'total_updated' => $totalUpdated,
            'total_errors' => $totalErrors,
            'source' => 'Google Sheets',
            'timestamp' => now()->setTimezone('Asia/Kuala_Lumpur')->toDateTimeString()
        ];

        // Create professional log message
        $message = "Student Import from Google Sheets: ";
        if ($totalCreated > 0 || $totalUpdated > 0) {
            $message .= "Successfully processed {$totalCreated} new students and updated {$totalUpdated} existing students";
        } else {
            $message .= "No changes detected in student data";
        }
        
        if ($totalErrors > 0) {
            $message .= " with {$totalErrors} validation warnings";
        }
        
        Log::info($message, $summary);

        // Display results
        $this->info('');
        $this->info('=== IMPORT SUMMARY ===');
        $this->info("Total Created: {$totalCreated}");
        $this->info("Total Updated: {$totalUpdated}");
        $this->info("Total Errors: {$totalErrors}");
        $this->info("Source: Google Sheets");

        // Send email notification if requested
        if ($notifyEmail && ($totalCreated > 0 || $totalErrors > 0)) {
            $this->sendNotificationEmail($notifyEmail, $summary);
        }

        $this->info('');
        $this->info('Google Sheets import completed successfully!');
        
        return 0;
    }

    private function sendNotificationEmail($email, $summary)
    {
        // Check if email is configured properly
        if (config('mail.default') === 'log' || empty(config('mail.host')) || config('mail.host') === 'mailpit') {
            $this->warn("Email notifications disabled - using log driver or mailpit not available");
            Log::info('Google Sheets import notification skipped - email not configured', [
                'email' => $email,
                'mail_driver' => config('mail.default'),
                'mail_host' => config('mail.host')
            ]);
            return;
        }
        
        try {
            \Illuminate\Support\Facades\Mail::raw($this->formatEmailContent($summary), function ($message) use ($email, $summary) {
                $message->to($email)
                        ->subject('Google Sheets Student Import Results - ' . now()->format('Y-m-d H:i:s'))
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info("Notification email sent to: {$email}");
            Log::info('Google Sheets import notification sent', ['email' => $email]);
            
        } catch (\Exception $e) {
            $this->warn("Failed to send notification email: " . $e->getMessage());
            Log::warning('Google Sheets import notification failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            // Don't fail the entire import process due to email issues
            $this->info("Continuing despite email notification failure...");
        }
    }

    private function formatEmailContent($summary)
    {
        $content = "Google Sheets Student Import Results\n";
        $content .= "==================================\n\n";
        $content .= "Import Time: " . $summary['timestamp'] . "\n";
        $content .= "Source: " . $summary['source'] . "\n\n";
        $content .= "Summary:\n";
        $content .= "- Total Created: " . $summary['total_created'] . "\n";
        $content .= "- Total Updated: " . $summary['total_updated'] . "\n";
        $content .= "- Total Errors: " . $summary['total_errors'] . "\n\n";
        
        $content .= "\nThis is an automated message from the LMS Olympia system.";
        
        return $content;
    }
}

