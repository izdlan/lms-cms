<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\GoogleSheetsImportService;

// Configuration
$useGoogleSheets = true; // Set to true to use Google Sheets, false to use Excel file
$filePath = storage_path('app/students/Enrollment OEM.xlsx');
$googleSheetsUrl = 'https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk/edit?usp=sharing&ouid=117738643589016699947&rtpof=true&sd=true';

if ($useGoogleSheets) {
    echo "Starting Google Sheets automation watcher...\n";
    echo "Watching Google Sheets: " . $googleSheetsUrl . "\n";
    echo "Press Ctrl+C to stop\n\n";
    
    $checkInterval = 300; // Check every 5 minutes (300 seconds)
    $googleSheetsService = new GoogleSheetsImportService();
    
    while (true) {
        echo "[" . date('Y-m-d H:i:s', strtotime('+8 hours')) . "] Checking Google Sheets for changes...\n";
        
        try {
            // Check if there are changes in the Google Sheets
            if ($googleSheetsService->checkForChanges()) {
                echo "[" . date('Y-m-d H:i:s', strtotime('+8 hours')) . "] Changes detected! Starting import...\n";
                
                // Run the import
                $result = $googleSheetsService->importFromGoogleSheets();
                
                if ($result['success']) {
                    echo "[" . date('Y-m-d H:i:s', strtotime('+8 hours')) . "] ✓ Import completed successfully\n";
                    echo "  Created: " . $result['created'] . " students\n";
                    echo "  Updated: " . $result['updated'] . " students\n";
                    echo "  Errors: " . $result['errors'] . " students\n";
                } else {
                    echo "[" . date('Y-m-d H:i:s', strtotime('+8 hours')) . "] ✗ Import failed: " . ($result['message'] ?? 'Unknown error') . "\n";
                }
            } else {
                echo "[" . date('Y-m-d H:i:s', strtotime('+8 hours')) . "] No changes detected\n";
            }
            
        } catch (Exception $e) {
            echo "[" . date('Y-m-d H:i:s', strtotime('+8 hours')) . "] ✗ Error during check/import: " . $e->getMessage() . "\n";
        }
        
        echo "[" . date('Y-m-d H:i:s', strtotime('+8 hours')) . "] Next check in " . ($checkInterval / 60) . " minutes...\n\n";
        sleep($checkInterval);
    }
} else {
    // Original Excel file watcher
    echo "Starting Excel file watcher for automation...\n";
    echo "Watching file: " . $filePath . "\n";
    echo "Press Ctrl+C to stop\n\n";
    $lastModified = file_exists($filePath) ? filemtime($filePath) : 0;
    $checkInterval = 60; // Check every 60 seconds

    while (true) {
        if (file_exists($filePath)) {
            $currentModified = filemtime($filePath);
            
            if ($currentModified > $lastModified) {
                echo "[" . date('Y-m-d H:i:s', strtotime('+8 hours')) . "] File change detected! Starting import...\n";
                
                try {
                    // Run the auto-import command
                    $exitCode = 0;
                    $output = [];
                    exec('php artisan students:auto-import --force 2>&1', $output, $exitCode);
                    
                    if ($exitCode === 0) {
                        echo "[" . date('Y-m-d H:i:s', strtotime('+8 hours')) . "] ✓ Import completed successfully\n";
                        foreach ($output as $line) {
                            if (trim($line)) {
                                echo "  " . $line . "\n";
                            }
                        }
                    } else {
                        echo "[" . date('Y-m-d H:i:s', strtotime('+8 hours')) . "] ✗ Import failed with exit code: $exitCode\n";
                        foreach ($output as $line) {
                            if (trim($line)) {
                                echo "  " . $line . "\n";
                            }
                        }
                    }
                    
                    $lastModified = $currentModified;
                    
                } catch (Exception $e) {
                    echo "[" . date('Y-m-d H:i:s', strtotime('+8 hours')) . "] ✗ Error during import: " . $e->getMessage() . "\n";
                }
            } else {
                echo "[" . date('Y-m-d H:i:s', strtotime('+8 hours')) . "] No changes detected\n";
            }
        } else {
            echo "[" . date('Y-m-d H:i:s', strtotime('+8 hours')) . "] File not found: $filePath\n";
        }
        
        sleep($checkInterval);
    }
}
