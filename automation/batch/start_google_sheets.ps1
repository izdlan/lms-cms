# LMS Olympia - Google Sheets Automation
# PowerShell Script

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  LMS Olympia - Google Sheets Automation" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Starting Google Sheets automation..." -ForegroundColor Yellow
Write-Host "This will monitor your Google Sheets for changes" -ForegroundColor Yellow
Write-Host "and automatically import student data." -ForegroundColor Yellow
Write-Host ""
Write-Host "Press Ctrl+C to stop the automation." -ForegroundColor Red
Write-Host ""

# Change to the project directory
Set-Location $PSScriptRoot\..\..

# Start the Google Sheets automation
php automation\scripts\google_sheets_automation.php

Write-Host ""
Write-Host "Google Sheets automation stopped." -ForegroundColor Red
Read-Host "Press Enter to exit"

