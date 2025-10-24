@echo off
echo ========================================
echo    LMS Auto-Sync Service Installer
echo ========================================
echo.
echo This will install the auto-sync service as a Windows service.
echo You need administrator privileges to run this.
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: This script must be run as Administrator!
    echo Right-click and select "Run as administrator"
    pause
    exit /b 1
)

echo Creating service definition...
echo.

REM Create the service definition file
(
echo @echo off
echo cd /d "C:\xampp\htdocs\lms-cms"
echo php artisan auto-sync:check
) > auto_sync_service_definition.bat

echo Service definition created.
echo.
echo To install as Windows Service:
echo 1. Download and install NSSM ^(Non-Sucking Service Manager^)
echo 2. Run: nssm install "LMS Auto-Sync" "C:\xampp\htdocs\lms-cms\auto_sync_service_definition.bat"
echo 3. Run: nssm start "LMS Auto-Sync"
echo.
echo Or use the simple batch file approach:
echo - Run setup_auto_sync.bat
echo - Choose option 1 or 2
echo.
echo For cPanel deployment:
echo - Follow the instructions in cpanel_auto_sync_setup.md
echo - Use cron jobs instead of Windows services
echo.
pause
