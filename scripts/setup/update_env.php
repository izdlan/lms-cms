<?php

echo "=== UPDATING .ENV FILE ===\n\n";

$envFile = '.env';
$envContent = file_get_contents($envFile);

// Environment variables to add/update
$newVars = [
    'GOOGLE_SHEETS_URL' => 'https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk/edit?usp=sharing&ouid=117738643589016699947&rtpof=true&sd=true',
    'GOOGLE_SHEETS_SPREADSHEET_ID' => '1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk',
    'GOOGLE_SHEETS_API_KEY' => '',
    'GOOGLE_SHEETS_CREDENTIALS_PATH' => 'storage/app/google-credentials.json',
    'ONEDRIVE_EXCEL_URL' => 'https://api.onedrive.com/v1.0/shares/u!aHR0cHM6Ly8xZHJ2Lm1zL3gvYy81N0U3QTQ3MkJFODkxRkZDL0VTSWRWX1ZiVGVKQmh2Z3h5LWl0cFhjQkhZMEh0X0dWbXBwTXRvbUJCVWRVM1E_ZT1xNFc4aWE/root/content',
    'GOOGLE_DRIVE_EXCEL_URL' => 'https://drive.google.com/file/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk/view?usp=sharing'
];

$updated = false;

foreach ($newVars as $key => $value) {
    $pattern = "/^{$key}=.*$/m";
    $replacement = "{$key}={$value}";
    
    if (preg_match($pattern, $envContent)) {
        // Update existing variable
        $envContent = preg_replace($pattern, $replacement, $envContent);
        echo "✅ Updated: {$key}\n";
        $updated = true;
    } else {
        // Add new variable
        $envContent .= "\n{$replacement}";
        echo "✅ Added: {$key}\n";
        $updated = true;
    }
}

if ($updated) {
    file_put_contents($envFile, $envContent);
    echo "\n✅ .env file updated successfully!\n";
} else {
    echo "\n⚠️  No changes needed - all variables already exist.\n";
}

echo "\n=== CONFIGURATION COMPLETE ===\n";
echo "You can now test the OneDrive connection with:\n";
echo "php test_new_onedrive.php\n";
