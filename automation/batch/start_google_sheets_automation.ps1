Write-Host "Starting Google Sheets Automation Watcher..." -ForegroundColor Green
Write-Host ""
Write-Host "This will monitor the Google Sheets for changes and automatically import student data." -ForegroundColor Yellow
Write-Host "Press Ctrl+C to stop the automation." -ForegroundColor Yellow
Write-Host ""

# Change to the script directory
Set-Location $PSScriptRoot

# Start the Google Sheets automation watcher
php google_sheets_automation_watcher.php

Write-Host ""
Write-Host "Google Sheets automation stopped." -ForegroundColor Red
Read-Host "Press Enter to exit"
