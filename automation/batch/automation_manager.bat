@echo off
title LMS Olympia - Automation Manager
color 0E

echo.
echo ========================================
echo   LMS Olympia - Automation Manager
echo ========================================
echo.

cd /d "%~dp0\..\.."
php automation\scripts\automation_manager.php %*

echo.
pause

