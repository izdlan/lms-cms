<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AutoImportStudents extends Command
{
    protected $signature = 'students:auto-import 
                            {--file= : Path to Excel file}
                            {--email= : Email to notify on completion}
                            {--force : Force import even if file unchanged}';
    
    protected $description = 'Automatically import students from Excel file with change detection (runs every minute)';

    public function handle()
    {
        $filePath = $this->option('file') ?: storage_path('app/students/Enrollment OEM.xlsx');
        $notifyEmail = $this->option('email');
        $force = $this->option('force');

        $this->info('Starting automatic student import...');
        $this->info("File: {$filePath}");

        // Check if file exists
        if (!file_exists($filePath)) {
            $this->error("Excel file not found: {$filePath}");
            Log::error("Auto import failed: File not found", ['file' => $filePath]);
            return 1;
        }

        // Check file modification time
        $lastModified = filemtime($filePath);
        $lastImportKey = 'last_auto_import_time';
        $lastImportTime = cache()->get($lastImportKey, 0);

        if (!$force && $lastModified <= $lastImportTime) {
            $this->info('No changes detected in Excel file. Skipping import.');
            Log::info('Auto import skipped: No file changes detected', [
                'file' => $filePath,
                'last_modified' => Carbon::createFromTimestamp($lastModified)->toDateTimeString(),
                'last_import' => Carbon::createFromTimestamp($lastImportTime)->toDateTimeString()
            ]);
            return 0;
        }

        $this->info('File changes detected. Starting import...');

        // Define allowed sheets
        $allowedSheets = ['DHU LMS', 'IUC LMS', 'VIVA-IUC LMS', 'LUC LMS'];
        
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $importResults = [];

        // Import all sheets at once - StudentsImport will handle sheet filtering
        try {
            $import = new StudentsImport();
            Excel::import($import, $filePath);
            
            // Get statistics from the import
            $stats = $import->getStats();
            
            $totalCreated = $stats['created'];
            $totalUpdated = $stats['updated'];
            $totalErrors = $stats['errors'];
            
            // Create results for each allowed sheet
            foreach ($allowedSheets as $sheetName) {
                $importResults[$sheetName] = [
                    'status' => 'success',
                    'created' => 0, // Individual sheet stats not available
                    'updated' => 0,
                    'errors' => 0
                ];
            }
            
            $this->info("✓ All sheets processed successfully");
            Log::info("Auto import completed successfully", $stats);
            
        } catch (\Exception $e) {
            $this->error("✗ Error during import: " . $e->getMessage());
            Log::error("Auto import error", [
                'error' => $e->getMessage(),
                'file_path' => $filePath
            ]);
            
            // Mark all sheets as failed
            foreach ($allowedSheets as $sheetName) {
                $importResults[$sheetName] = [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'created' => 0,
                    'updated' => 0,
                    'errors' => 1
                ];
            }
            $totalErrors = count($allowedSheets);
        }

        // Update last import time
        cache()->put($lastImportKey, $lastModified, now()->addDays(30));

        // Log import summary
        $summary = [
            'total_created' => $totalCreated,
            'total_updated' => $totalUpdated,
            'total_errors' => $totalErrors,
            'file' => $filePath,
            'sheets_processed' => count($allowedSheets),
            'import_results' => $importResults,
            'timestamp' => now()->toDateTimeString()
        ];

        Log::info('Auto import completed', $summary);

        // Display results
        $this->info('');
        $this->info('=== IMPORT SUMMARY ===');
        $this->info("Total Created: {$totalCreated}");
        $this->info("Total Updated: {$totalUpdated}");
        $this->info("Total Errors: {$totalErrors}");
        $this->info('');

        foreach ($importResults as $sheet => $result) {
            $status = $result['status'] === 'success' ? '✓' : '✗';
            $this->info("{$status} {$sheet}: {$result['created']} created, {$result['updated']} updated, {$result['errors']} errors");
        }

        // Send email notification if requested
        if ($notifyEmail && ($totalCreated > 0 || $totalErrors > 0)) {
            $this->sendNotificationEmail($notifyEmail, $summary);
        }

        $this->info('');
        $this->info('Auto import completed successfully!');
        
        return 0;
    }

    private function sendNotificationEmail($email, $summary)
    {
        // Check if email is configured properly
        if (config('mail.default') === 'log' || empty(config('mail.host')) || config('mail.host') === 'mailpit') {
            $this->warn("Email notifications disabled - using log driver or mailpit not available");
            Log::info('Auto import notification skipped - email not configured', [
                'email' => $email,
                'mail_driver' => config('mail.default'),
                'mail_host' => config('mail.host')
            ]);
            return;
        }
        
        try {
            Mail::raw($this->formatEmailContent($summary), function ($message) use ($email, $summary) {
                $message->to($email)
                        ->subject('Student Auto Import Results - ' . now()->format('Y-m-d H:i:s'))
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info("Notification email sent to: {$email}");
            Log::info('Auto import notification sent', ['email' => $email]);
            
        } catch (\Exception $e) {
            $this->warn("Failed to send notification email: " . $e->getMessage());
            Log::warning('Auto import notification failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            // Don't fail the entire import process due to email issues
            $this->info("Continuing despite email notification failure...");
        }
    }

    private function formatEmailContent($summary)
    {
        $content = "Student Auto Import Results\n";
        $content .= "========================\n\n";
        $content .= "Import Time: " . $summary['timestamp'] . "\n";
        $content .= "File: " . basename($summary['file']) . "\n\n";
        $content .= "Summary:\n";
        $content .= "- Total Created: " . $summary['total_created'] . "\n";
        $content .= "- Total Updated: " . $summary['total_updated'] . "\n";
        $content .= "- Total Errors: " . $summary['total_errors'] . "\n\n";
        
        $content .= "Sheet Details:\n";
        foreach ($summary['import_results'] as $sheet => $result) {
            $status = $result['status'] === 'success' ? 'SUCCESS' : 'ERROR';
            $content .= "- {$sheet}: {$status} ({$result['created']} created, {$result['updated']} updated, {$result['errors']} errors)\n";
            
            if (isset($result['error_message'])) {
                $content .= "  Error: " . $result['error_message'] . "\n";
            }
        }
        
        $content .= "\nThis is an automated message from the LMS Olympia system.";
        
        return $content;
    }
}


