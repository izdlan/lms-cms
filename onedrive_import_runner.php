<?php
/**
 * OneDrive Auto Import Runner
 * Run this script to import students from OneDrive
 */

echo "Starting OneDrive import...\n";

// Change to the project directory
chdir("C:\xampp\htdocs\LMS_Olympia");

// Run the import command
$output = [];
$returnCode = 0;
exec("php artisan import:onedrive-auto 2>&1", $output, $returnCode);

echo "Import completed with return code: $returnCode\n";
echo "Output:\n" . implode("\n", $output) . "\n";

if ($returnCode === 0) {
    echo "✅ Import successful!\n";
} else {
    echo "❌ Import failed!\n";
}