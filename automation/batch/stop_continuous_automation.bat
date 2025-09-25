@echo off
echo Stopping Continuous Google Sheets Automation...
echo.

REM Kill any running continuous automation processes
taskkill /F /IM php.exe /FI "COMMANDLINE eq *continuous_automation*" 2>nul

if %ERRORLEVEL% == 0 (
    echo Continuous automation stopped successfully.
) else (
    echo No continuous automation processes found.
)

echo.
pause
