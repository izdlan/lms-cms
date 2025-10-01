@echo off
echo Starting OneDrive Auto Sync Service...
echo This will check for new students every 5 minutes
echo Press Ctrl+C to stop

:loop
echo.
echo [%date% %time%] Checking for OneDrive updates...
cd /d "C:\xampp\htdocs\LMS_Olympia"
php artisan sync:onedrive

echo Waiting 5 minutes before next check...
timeout /t 300 /nobreak > nul
goto loop

