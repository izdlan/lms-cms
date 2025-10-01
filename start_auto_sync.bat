@echo off
echo Starting Auto-Sync Service...
echo This will run continuously and sync every 5 minutes.
echo Press Ctrl+C to stop.
echo.

cd /d "C:\xampp\htdocs\LMS_Olympia"

:loop
php artisan auto-sync:run --continuous
if %errorlevel% neq 0 (
    echo Auto-sync encountered an error. Restarting in 30 seconds...
    timeout /t 30 /nobreak >nul
)
goto loop


