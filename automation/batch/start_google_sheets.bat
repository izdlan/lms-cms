@echo off
title LMS Olympia - Google Sheets Automation
color 0A

echo.
echo ========================================
echo   LMS Olympia - Google Sheets Automation
echo ========================================
echo.
echo Starting Google Sheets automation...
echo This will monitor your Google Sheets for changes
echo and automatically import student data.
echo.
echo Press Ctrl+C to stop the automation.
echo.

cd /d "%~dp0\..\.."
php automation\scripts\google_sheets_automation.php

echo.
echo Google Sheets automation stopped.
pause

