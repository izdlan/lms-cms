@echo off
echo ========================================
echo    LMS Auto-Sync Service
echo ========================================
echo.
echo Starting auto-sync service...
echo This will run every 5 minutes to check for updates.
echo.
echo To stop this service, close this window or press Ctrl+C
echo.

:loop
echo [%date% %time%] Checking for Google Sheets updates...
cd /d "C:\xampp\htdocs\lms-cms"
php artisan auto-sync:run

if %errorlevel% neq 0 (
    echo [%date% %time%] ERROR: Auto-sync check failed!
) else (
    echo [%date% %time%] Auto-sync check completed successfully.
)

echo.
echo Waiting 5 minutes before next check...
timeout /t 300 /nobreak >nul
goto loop