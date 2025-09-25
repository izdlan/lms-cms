<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\GoogleSheetsImportService;

echo "Testing Google Sheets Import Service...\n";
echo "=====================================\n\n";

try {
    $googleSheetsService = new GoogleSheetsImportService();
    
    echo "1. Testing Google Sheets connection...\n";
    $result = $googleSheetsService->importFromGoogleSheets();
    
    if ($result['success']) {
        echo "✓ Google Sheets import successful!\n";
        echo "  Total Created: " . $result['created'] . " students\n";
        echo "  Total Updated: " . $result['updated'] . " students\n";
        echo "  Total Errors: " . $result['errors'] . " students\n";
        
        if (isset($result['processed_sheets'])) {
            echo "\n  Per-sheet results:\n";
            foreach ($result['processed_sheets'] as $sheet) {
                echo "    {$sheet['sheet']}: Created={$sheet['created']}, Updated={$sheet['updated']}, Errors={$sheet['errors']}\n";
            }
        }
    } else {
        echo "✗ Google Sheets import failed: " . ($result['message'] ?? 'Unknown error') . "\n";
    }
    
    echo "\n2. Testing change detection...\n";
    $hasChanges = $googleSheetsService->checkForChanges();
    echo ($hasChanges ? "✓ Changes detected" : "No changes detected") . "\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";

