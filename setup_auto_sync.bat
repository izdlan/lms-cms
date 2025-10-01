@echo off
echo ========================================
echo    LMS Auto-Sync Setup
echo ========================================
echo.

echo Testing auto-sync command...
cd /d "C:\xampp\htdocs\LMS_Olympia"
php artisan auto-sync:run

if %errorlevel% neq 0 (
    echo ERROR: Auto-sync command failed!
    echo Please check your Laravel installation.
    pause
    exit /b 1
)

echo.
echo Auto-sync command is working!
echo.
echo Choose how to start auto-sync:
echo.
echo 1. Start as Windows Service (Recommended)
echo 2. Start as Background Process
echo 3. Start in Current Window
echo 4. Exit
echo.

set /p choice="Enter your choice (1-4): "

if "%choice%"=="1" (
    echo Starting as Windows Service...
    start "Auto-Sync Service" auto_sync_service.bat
    echo Service started! Check the new window.
) else if "%choice%"=="2" (
    echo Starting as Background Process...
    start /min "Auto-Sync Background" auto_sync_service.bat
    echo Background process started!
) else if "%choice%"=="3" (
    echo Starting in Current Window...
    auto_sync_service.bat
) else if "%choice%"=="4" (
    echo Exiting...
    exit /b 0
) else (
    echo Invalid choice!
    pause
    goto :eof
)

echo.
echo Auto-sync is now running!
echo.
echo To stop auto-sync:
echo - Close the service window, or
echo - Press Ctrl+C in the service window
echo.
echo To check status:
echo - Visit your admin panel
echo - Check Recent Activity section
echo.
pause


