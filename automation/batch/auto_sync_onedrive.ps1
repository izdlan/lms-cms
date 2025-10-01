# OneDrive Auto Sync Service
# This script runs the OneDrive sync command every 5 minutes

Write-Host "Starting OneDrive Auto Sync Service..." -ForegroundColor Green
Write-Host "This will check for new students every 5 minutes" -ForegroundColor Yellow
Write-Host "Press Ctrl+C to stop" -ForegroundColor Red
Write-Host ""

# Set the working directory
Set-Location "C:\xampp\htdocs\LMS_Olympia"

while ($true) {
    try {
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        Write-Host "[$timestamp] Checking for OneDrive updates..." -ForegroundColor Cyan
        
        # Run the sync command
        php artisan sync:onedrive
        
        Write-Host "Waiting 5 minutes before next check..." -ForegroundColor Yellow
        Start-Sleep -Seconds 300  # 5 minutes
    }
    catch {
        Write-Host "Error occurred: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host "Waiting 1 minute before retry..." -ForegroundColor Yellow
        Start-Sleep -Seconds 60  # 1 minute before retry
    }
}

