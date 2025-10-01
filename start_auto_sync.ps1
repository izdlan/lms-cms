# Auto-Sync PowerShell Script
Write-Host "Starting Auto-Sync Service..." -ForegroundColor Green
Write-Host "This will run continuously and sync every 5 minutes." -ForegroundColor Yellow
Write-Host "Press Ctrl+C to stop." -ForegroundColor Yellow
Write-Host ""

# Change to project directory
Set-Location "C:\xampp\htdocs\LMS_Olympia"

# Function to run auto-sync
function Start-AutoSync {
    try {
        Write-Host "[$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')] Running auto-sync..." -ForegroundColor Cyan
        php artisan auto-sync:run --continuous
    }
    catch {
        Write-Host "[$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')] Error: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host "Restarting in 30 seconds..." -ForegroundColor Yellow
        Start-Sleep -Seconds 30
    }
}

# Main loop
while ($true) {
    Start-AutoSync
}


