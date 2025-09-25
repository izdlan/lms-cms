@echo off
echo Starting Continuous Google Sheets Automation...
echo.

REM Check if automation is already running
tasklist /FI "IMAGENAME eq php.exe" /FI "COMMANDLINE eq *continuous_automation*" 2>nul | find /I "php.exe" >nul
if %ERRORLEVEL% == 0 (
    echo Automation is already running!
    pause
    exit /b 1
)

REM Start the continuous automation script
echo Starting continuous automation process...
start /B php automation/scripts/continuous_automation.php

echo.
echo Continuous automation started in background.
echo Check automation/logs/continuous_automation.log for status.
echo.
echo To stop automation, run: stop_continuous_automation.bat
echo.
pause
