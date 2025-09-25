<?php
/**
 * Simple Automation Setup (No Admin Required)
 * Creates files that can be used without administrator privileges
 */

echo "=== SIMPLE ONEDRIVE AUTOMATION SETUP (NO ADMIN REQUIRED) ===\n\n";

echo "This will create automation files that don't require administrator privileges.\n\n";

echo "📋 SETUP OPTIONS:\n";
echo "1. Create batch file for manual runs\n";
echo "2. Create PHP script for manual runs\n";
echo "3. Create Windows Task Scheduler XML file\n";
echo "4. Show manual setup instructions\n";
echo "5. Exit\n\n";

$choice = readline("Enter your choice (1-5): ");

switch ($choice) {
    case '1':
        echo "\n📄 CREATING BATCH FILE...\n";
        
        $batchContent = '@echo off
echo Starting automated OneDrive import...
echo Time: %date% %time%

cd /d "C:\xampp\htdocs\LMS_Olympia"

php artisan import:onedrive-auto

echo Automated OneDrive import completed.
echo Time: %date% %time%
echo.
pause';

        file_put_contents('onedrive_auto_import.bat', $batchContent);
        echo "✅ Created: onedrive_auto_import.bat\n";
        echo "You can double-click this file to run imports manually.\n";
        echo "To schedule it automatically, use Windows Task Scheduler.\n";
        break;
        
    case '2':
        echo "\n🐘 CREATING PHP SCRIPT...\n";
        
        $phpContent = '<?php
/**
 * OneDrive Auto Import Runner
 * Run this script to import students from OneDrive
 */

echo "Starting OneDrive import...\n";

// Change to the project directory
chdir("C:\\xampp\\htdocs\\LMS_Olympia");

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
}';

        file_put_contents('onedrive_import_runner.php', $phpContent);
        echo "✅ Created: onedrive_import_runner.php\n";
        echo "Run with: php onedrive_import_runner.php\n";
        break;
        
    case '3':
        echo "\n📋 CREATING TASK SCHEDULER XML...\n";
        
        $xmlContent = '<?xml version="1.0" encoding="UTF-16"?>
<Task version="1.2" xmlns="http://schemas.microsoft.com/windows/2004/02/mit/task">
  <RegistrationInfo>
    <Date>2025-01-01T00:00:00</Date>
    <Author>LMS System</Author>
    <Description>Automatically import students from OneDrive every 5 minutes</Description>
  </RegistrationInfo>
  <Triggers>
    <TimeTrigger>
      <Repetition>
        <Interval>PT5M</Interval>
        <StopAtDurationEnd>false</StopAtDurationEnd>
      </Repetition>
      <StartBoundary>2025-01-01T09:00:00</StartBoundary>
      <Enabled>true</Enabled>
    </TimeTrigger>
  </Triggers>
  <Principals>
    <Principal id="Author">
      <UserId>S-1-5-18</UserId>
      <RunLevel>LeastPrivilege</RunLevel>
    </Principal>
  </Principals>
  <Settings>
    <MultipleInstancesPolicy>IgnoreNew</MultipleInstancesPolicy>
    <DisallowStartIfOnBatteries>false</DisallowStartIfOnBatteries>
    <StopIfGoingOnBatteries>false</StopIfGoingOnBatteries>
    <AllowHardTerminate>true</AllowHardTerminate>
    <StartWhenAvailable>true</StartWhenAvailable>
    <RunOnlyIfNetworkAvailable>false</RunOnlyIfNetworkAvailable>
    <IdleSettings>
      <StopOnIdleEnd>true</StopOnIdleEnd>
      <RestartOnIdle>false</RestartOnIdle>
    </IdleSettings>
    <AllowStartOnDemand>true</AllowStartOnDemand>
    <Enabled>true</Enabled>
    <Hidden>false</Hidden>
    <RunOnlyIfIdle>false</RunOnlyIfIdle>
    <WakeToRun>false</WakeToRun>
    <ExecutionTimeLimit>PT1H</ExecutionTimeLimit>
    <Priority>7</Priority>
  </Settings>
  <Actions Context="Author">
    <Exec>
      <Command>php</Command>
      <Arguments>artisan import:onedrive-auto</Arguments>
      <WorkingDirectory>C:\xampp\htdocs\LMS_Olympia</WorkingDirectory>
    </Exec>
  </Actions>
</Task>';

        file_put_contents('LMS_OneDrive_AutoImport.xml', $xmlContent);
        echo "✅ Created: LMS_OneDrive_AutoImport.xml\n";
        echo "Import this file in Windows Task Scheduler:\n";
        echo "1. Open Task Scheduler\n";
        echo "2. Click 'Import Task' in the right panel\n";
        echo "3. Select the XML file\n";
        echo "4. Click OK\n";
        break;
        
    case '4':
        echo "\n📖 MANUAL SETUP INSTRUCTIONS:\n";
        echo "================================\n\n";
        echo "Method 1 - Batch File:\n";
        echo "1. Double-click 'onedrive_auto_import.bat' to run manually\n";
        echo "2. To schedule: Open Task Scheduler → Create Basic Task\n";
        echo "3. Set trigger to 'Every 5 minutes'\n";
        echo "4. Set action to run the batch file\n\n";
        
        echo "Method 2 - PHP Script:\n";
        echo "1. Run: php onedrive_import_runner.php\n";
        echo "2. To schedule: Use Task Scheduler with PHP command\n\n";
        
        echo "Method 3 - XML Import:\n";
        echo "1. Open Task Scheduler\n";
        echo "2. Click 'Import Task'\n";
        echo "3. Select 'LMS_OneDrive_AutoImport.xml'\n";
        echo "4. Click OK\n\n";
        
        echo "Method 4 - Web Interface:\n";
        echo "1. Go to Admin Panel → OneDrive Auto Import\n";
        echo "2. Click 'Run Import Now' whenever needed\n";
        echo "3. Set a reminder to check every few hours\n";
        break;
        
    case '5':
        echo "\n👋 Goodbye!\n";
        break;
        
    default:
        echo "\n❌ Invalid choice. Please run the script again.\n";
        break;
}

echo "\n=== SETUP COMPLETED ===\n";
