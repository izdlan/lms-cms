<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class WatchExcelFile extends Command
{
    protected $signature = 'students:watch-excel 
                            {--file= : Path to Excel file to watch}
                            {--email= : Email to notify on changes}
                            {--interval=5 : Check interval in seconds}';
    
    protected $description = 'Watch Excel file for changes and auto-import students';

    private $lastModified = 0;
    private $filePath;

    public function handle()
    {
        $this->filePath = $this->option('file') ?: storage_path('app/students/Enrollment OEM.xlsx');
        $notifyEmail = $this->option('email');
        $interval = (int) $this->option('interval');

        $this->info("Watching Excel file: {$this->filePath}");
        $this->info("Check interval: {$interval} seconds");
        $this->info("Press Ctrl+C to stop watching");

        if (!file_exists($this->filePath)) {
            $this->error("Excel file not found: {$this->filePath}");
            return 1;
        }

        $this->lastModified = filemtime($this->filePath);
        $this->info("Initial file modification time: " . date('Y-m-d H:i:s', $this->lastModified));

        while (true) {
            if ($this->checkFileChanged()) {
                $this->info("File change detected! Starting import...");
                
                try {
                    // Run the auto-import command
                    $exitCode = $this->call('students:auto-import', [
                        '--file' => $this->filePath,
                        '--email' => $notifyEmail,
                        '--force' => true
                    ]);

                    if ($exitCode === 0) {
                        $this->info("✓ Import completed successfully");
                        Log::info('File watcher triggered import', [
                            'file' => $this->filePath,
                            'timestamp' => now()->toDateTimeString()
                        ]);
                    } else {
                        $this->error("✗ Import failed with exit code: {$exitCode}");
                        Log::error('File watcher import failed', [
                            'file' => $this->filePath,
                            'exit_code' => $exitCode,
                            'timestamp' => now()->toDateTimeString()
                        ]);
                    }
                } catch (\Exception $e) {
                    $this->error("Import error: " . $e->getMessage());
                    Log::error('File watcher import exception', [
                        'file' => $this->filePath,
                        'error' => $e->getMessage(),
                        'timestamp' => now()->toDateTimeString()
                    ]);
                }
            }

            // Wait before next check
            sleep($interval);
        }
    }

    private function checkFileChanged()
    {
        if (!file_exists($this->filePath)) {
            $this->warn("File no longer exists: {$this->filePath}");
            return false;
        }

        $currentModified = filemtime($this->filePath);
        
        if ($currentModified > $this->lastModified) {
            $this->lastModified = $currentModified;
            return true;
        }

        return false;
    }
}
