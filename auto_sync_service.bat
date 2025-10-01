@echo off
title Auto-Sync Service
color 0A

echo ========================================
echo    LMS Auto-Sync Service
echo ========================================
echo.
echo Starting continuous auto-sync...
echo This service will:
echo - Run auto-sync every 5 minutes
echo - Restart automatically on errors
echo - Log all activities
echo.
echo Press Ctrl+C to stop the service
echo.

cd /d "C:\xampp\htdocs\LMS_Olympia"

:start_service
echo [%date% %time%] Starting auto-sync service...
php artisan auto-sync:run --continuous

if %errorlevel% neq 0 (
    echo [%date% %time%] Auto-sync service encountered an error.
    echo [%date% %time%] Restarting in 30 seconds...
    timeout /t 30 /nobreak >nul
    echo [%date% %time%] Restarting service...
)

goto start_service


