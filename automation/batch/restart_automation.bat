@echo off
echo Stopping existing automation processes...
taskkill /f /im php.exe 2>nul

echo Waiting 3 seconds...
timeout /t 3 /nobreak >nul

echo Starting improved automation watcher...
php improved_automation_watcher.php

pause

