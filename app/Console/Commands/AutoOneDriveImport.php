<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OneDriveExcelImportService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AutoOneDriveImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:onedrive-auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically import students from OneDrive Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automated OneDrive import...');
        
        try {
            $oneDriveService = new OneDriveExcelImportService();
            $result = $oneDriveService->importFromOneDrive();
            
            if ($result['success']) {
                $this->info('✅ OneDrive import completed successfully!');
                $this->info("Created: {$result['created']} students");
                $this->info("Updated: {$result['updated']} students");
                $this->info("Errors: {$result['errors']}");
                
                // Log the results
                Log::info('Automated OneDrive import completed', [
                    'created' => $result['created'],
                    'updated' => $result['updated'],
                    'errors' => $result['errors'],
                    'processed_sheets' => $result['processed_sheets'] ?? []
                ]);
                
                // Send email notification if there are new students
                if ($result['created'] > 0) {
                    $this->sendNotification($result);
                }
                
                return 0; // Success
            } else {
                $this->error('❌ OneDrive import failed: ' . ($result['error'] ?? 'Unknown error'));
                Log::error('Automated OneDrive import failed', [
                    'error' => $result['error'] ?? 'Unknown error',
                    'created' => $result['created'] ?? 0,
                    'updated' => $result['updated'] ?? 0,
                    'errors' => $result['errors'] ?? 0
                ]);
                
                return 1; // Failure
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error during automated import: ' . $e->getMessage());
            Log::error('Automated OneDrive import error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1; // Failure
        }
    }
    
    /**
     * Send email notification about new students
     */
    private function sendNotification($result)
    {
        try {
            $adminEmail = config('mail.admin_email', 'admin@olympia.edu.my');
            
            $data = [
                'created' => $result['created'],
                'updated' => $result['updated'],
                'errors' => $result['errors'],
                'processed_sheets' => $result['processed_sheets'] ?? [],
                'timestamp' => now()->format('Y-m-d H:i:s')
            ];
            
            Mail::send('emails.auto-import-notification', $data, function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                        ->subject('LMS: New Students Imported from OneDrive');
            });
            
            $this->info('📧 Notification email sent to admin');
            
        } catch (\Exception $e) {
            $this->warn('⚠️ Failed to send notification email: ' . $e->getMessage());
            Log::warning('Failed to send auto-import notification', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
