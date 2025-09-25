<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== GOOGLE SHEETS CONFIGURATION HELPER ===\n\n";

echo "This script will help you configure the correct Google Sheets URL for your LMS system.\n\n";

echo "Current configuration:\n";
echo "- Google Sheets URL: " . config('google_sheets.url', 'Not configured') . "\n";
echo "- LMS Sheets: " . implode(', ', array_keys(config('google_sheets.lms_sheets', []))) . "\n\n";

echo "To configure a new Google Sheets URL:\n";
echo "1. Open your Google Sheets document\n";
echo "2. Click 'Share' and make sure it's set to 'Anyone with the link can view'\n";
echo "3. Copy the URL from your browser\n";
echo "4. The URL should look like: https://docs.google.com/spreadsheets/d/YOUR_SHEET_ID/edit...\n\n";

echo "Expected LMS sheets in your Google Sheets:\n";
foreach (config('google_sheets.lms_sheets', []) as $sheetName => $gid) {
    echo "- {$sheetName} (expected GID: {$gid})\n";
}

echo "\nTo test a Google Sheets URL, run:\n";
echo "php test_google_sheets_import.php\n\n";

echo "To update the configuration, edit the file: config/google_sheets.php\n";
echo "Or set the environment variable: GOOGLE_SHEETS_URL=your_url_here\n\n";

echo "=== CONFIGURATION HELPER COMPLETED ===\n";

