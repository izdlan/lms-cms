@echo off
echo ========================================
echo    LMS Auto-Sync Test
echo ========================================
echo.

echo Testing auto-sync functionality...
cd /d "C:\xampp\htdocs\lms-cms"

echo.
echo 1. Testing auto-sync run command...
php artisan auto-sync:run

echo.
echo 2. Testing Google Sheets import command...
php artisan students:google-sheets-import --force

echo.
echo 3. Checking available auto-sync commands...
php artisan list | findstr auto-sync

echo.
echo Test completed!
echo.
echo If you see any errors above, please check:
echo - Your .env file configuration
echo - Google Sheets URL accessibility
echo - Database connection
echo - File permissions
echo.
pause
