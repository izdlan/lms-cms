@echo off
echo Starting Google Sheets Automation Watcher...
echo.
echo This will monitor the Google Sheets for changes and automatically import student data.
echo Press Ctrl+C to stop the automation.
echo.

cd /d "%~dp0"
php google_sheets_automation_watcher.php

pause
