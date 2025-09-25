@echo off
echo Starting automated OneDrive import...
echo Time: %date% %time%

cd /d "C:\xampp\htdocs\LMS_Olympia"

php artisan import:onedrive-auto

echo Automated OneDrive import completed.
echo Time: %date% %time%
echo.
