<?php
/**
 * Setup Automated OneDrive Import
 * This script helps you set up automatic imports every 5 minutes
 */

echo "=== LMS AUTOMATED ONEDRIVE IMPORT SETUP ===\n\n";

echo "This will set up automatic OneDrive imports every 5 minutes.\n\n";

echo "📋 SETUP OPTIONS:\n";
echo "1. Test the import command first\n";
echo "2. Create Windows Task Scheduler task\n";
echo "3. Create manual batch file for testing\n";
echo "4. View current configuration\n";
echo "5. Exit\n\n";

$choice = readline("Enter your choice (1-5): ");

switch ($choice) {
    case '1':
        echo "\n🧪 TESTING IMPORT COMMAND...\n";
        echo "Running: php artisan import:onedrive-auto\n\n";
        
        $output = [];
        $returnCode = 0;
        exec('php artisan import:onedrive-auto 2>&1', $output, $returnCode);
        
        echo "Return Code: $returnCode\n";
        echo "Output:\n";
        foreach ($output as $line) {
            echo "  $line\n";
        }
        
        if ($returnCode === 0) {
            echo "\n✅ Test successful! The import command is working.\n";
        } else {
            echo "\n❌ Test failed! Please check the errors above.\n";
        }
        break;
        
    case '2':
        echo "\n📅 CREATING WINDOWS TASK SCHEDULER TASK...\n";
        echo "This will create a task that runs every 5 minutes.\n\n";
        
        $taskName = "LMS_OneDrive_AutoImport";
        $batchFile = "C:\\xampp\\htdocs\\LMS_Olympia\\automation\\batch\\auto_onedrive_import.bat";
        
        // Create the task
        $command = "schtasks /create /tn \"$taskName\" /tr \"$batchFile\" /sc minute /mo 5 /ru SYSTEM /f";
        
        echo "Running: $command\n\n";
        exec($command, $output, $returnCode);
        
        foreach ($output as $line) {
            echo "$line\n";
        }
        
        if ($returnCode === 0) {
            echo "\n✅ Task created successfully!\n";
            echo "The task will run every 5 minutes automatically.\n";
            echo "You can view it in Task Scheduler (taskschd.msc)\n";
        } else {
            echo "\n❌ Failed to create task. You may need to run as administrator.\n";
        }
        break;
        
    case '3':
        echo "\n📝 CREATING MANUAL BATCH FILE...\n";
        
        $batchContent = '@echo off
echo Starting manual OneDrive import...
echo Time: %date% %time%

cd /d "C:\xampp\htdocs\LMS_Olympia"

php artisan import:onedrive-auto

echo Manual OneDrive import completed.
echo Time: %date% %time%
pause';

        file_put_contents('manual_onedrive_import.bat', $batchContent);
        echo "✅ Created manual_onedrive_import.bat\n";
        echo "You can double-click this file to run a manual import.\n";
        break;
        
    case '4':
        echo "\n⚙️ CURRENT CONFIGURATION:\n";
        echo "OneDrive URL: " . config('google_sheets.onedrive_url') . "\n";
        echo "Admin Email: " . config('mail.admin_email', 'Not set') . "\n";
        echo "Log File: C:\\xampp\\htdocs\\LMS_Olympia\\automation\\logs\\auto_import.log\n";
        echo "Batch File: C:\\xampp\\htdocs\\LMS_Olympia\\automation\\batch\\auto_onedrive_import.bat\n";
        break;
        
    case '5':
        echo "\n👋 Goodbye!\n";
        break;
        
    default:
        echo "\n❌ Invalid choice. Please run the script again.\n";
        break;
}

echo "\n=== SETUP COMPLETED ===\n";
