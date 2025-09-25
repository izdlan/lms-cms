<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\GoogleSheetsImportService;

$googleSheetsUrl = 'https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk/edit?usp=sharing&ouid=117738643589016699947&rtpof=true&sd=true';

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

