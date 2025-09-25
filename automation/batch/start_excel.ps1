# LMS Olympia - Excel Automation
# PowerShell Script

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "     LMS Olympia - Excel Automation" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Starting Excel file automation..." -ForegroundColor Yellow
Write-Host "This will monitor your Excel file for changes" -ForegroundColor Yellow
Write-Host "and automatically import student data." -ForegroundColor Yellow
Write-Host ""
Write-Host "Press Ctrl+C to stop the automation." -ForegroundColor Red
Write-Host ""

# Change to the project directory
Set-Location $PSScriptRoot\..\..

# Start the Excel automation
php automation\scripts\excel_automation.php

Write-Host ""
Write-Host "Excel automation stopped." -ForegroundColor Red
Read-Host "Press Enter to exit"

