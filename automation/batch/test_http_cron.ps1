# Test HTTP Cron Endpoint
Write-Host "Testing HTTP Cron Endpoint..." -ForegroundColor Green
Write-Host ""

try {
    $response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/admin/import-students" -UseBasicParsing
    Write-Host "Status Code: $($response.StatusCode)" -ForegroundColor Yellow
    Write-Host "Response:" -ForegroundColor Yellow
    Write-Host $response.Content -ForegroundColor White
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "HTTP Cron test completed." -ForegroundColor Green
Write-Host "Check the response above for success/error status." -ForegroundColor Cyan
Read-Host "Press Enter to continue"



