Write-Host "Starting Student Import Automation..." -ForegroundColor Green
Write-Host "This will watch for changes in the Excel file and automatically import students." -ForegroundColor Yellow
Write-Host "Press Ctrl+C to stop the automation." -ForegroundColor Yellow
Write-Host ""

try {
    php automation_watcher.php
} catch {
    Write-Host "Error: $_" -ForegroundColor Red
}

Write-Host "Press any key to continue..." -ForegroundColor Cyan
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
