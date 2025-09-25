@echo off
title LMS Olympia - Excel Automation
color 0B

echo.
echo ========================================
echo     LMS Olympia - Excel Automation
echo ========================================
echo.
echo Starting Excel file automation...
echo This will monitor your Excel file for changes
echo and automatically import student data.
echo.
echo Press Ctrl+C to stop the automation.
echo.

cd /d "%~dp0\..\.."
php automation\scripts\excel_automation.php

echo.
echo Excel automation stopped.
pause

