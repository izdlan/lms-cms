# LMS Olympia Automation System

This directory contains all automation scripts and tools for the LMS Olympia system. The automation system provides both Google Sheets and Excel file monitoring capabilities for automatic student data import.

## 📁 Directory Structure

```
automation/
├── scripts/           # Main automation scripts
├── batch/            # Windows batch and PowerShell files
├── test/             # Test scripts and utilities
├── config/           # Configuration files
├── logs/             # Log files
└── README.md         # This file
```

## 🚀 Quick Start

### Using the Automation Manager (Recommended)

The automation manager provides a unified interface to control all automation processes:

```bash
# Start Google Sheets automation
php automation/scripts/automation_manager.php start google_sheets

# Start Excel file automation
php automation/scripts/automation_manager.php start excel

# Check status
php automation/scripts/automation_manager.php status

# Stop automation
php automation/scripts/automation_manager.php stop

# List available automation types
php automation/scripts/automation_manager.php list
```

### Using Windows Batch Files

Double-click any of these files in the `batch/` folder:

- `start_google_sheets.bat` - Start Google Sheets automation
- `start_excel.bat` - Start Excel file automation
- `automation_manager.bat` - Open automation manager

### Using PowerShell Scripts

Run these scripts in PowerShell:

```powershell
# Google Sheets automation
.\automation\batch\start_google_sheets.ps1

# Excel automation
.\automation\batch\start_excel.ps1
```

## 📋 Available Automation Types

### 1. Google Sheets Automation
- **Script**: `scripts/google_sheets_automation.php`
- **Description**: Monitors Google Sheets for changes and imports student data
- **Check Interval**: 5 minutes
- **Source**: https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk

### 2. Excel File Automation
- **Script**: `scripts/excel_automation.php`
- **Description**: Monitors Excel file for changes and imports student data
- **Check Interval**: 1 minute
- **Source**: `storage/app/students/Enrollment OEM.xlsx`

## 🧪 Testing

Run the test scripts to verify everything is working:

```bash
# Test Google Sheets import
php automation/test/test_google_sheets_import.php

# Test other automation features
php automation/test/test_automation_detection.php
```

## 📊 Monitoring and Logs

### Log Files
- **Google Sheets**: `automation/logs/google_sheets_automation.log`
- **Excel**: `automation/logs/excel_automation.log`
- **Manager**: `automation/logs/automation_manager.log`

### Process Management
- **PID File**: `automation/logs/automation.pid`
- **Status**: Use `automation_manager.php status` to check running processes

## ⚙️ Configuration

### Google Sheets Configuration
Edit `scripts/google_sheets_automation.php` to modify:
- Google Sheets URL
- Check interval
- Retry settings
- Log file location

### Excel Configuration
Edit `scripts/excel_automation.php` to modify:
- Excel file path
- Check interval
- Retry settings
- Log file location

## 🔧 Troubleshooting

### Common Issues

1. **Permission Errors**
   - Ensure the automation folder has write permissions
   - Check that log files can be created

2. **Google Sheets Access**
   - Verify the Google Sheets is publicly accessible
   - Check the CSV export URL is correct

3. **Excel File Not Found**
   - Ensure the Excel file exists at the specified path
   - Check file permissions

4. **Process Not Starting**
   - Check PHP is installed and accessible
   - Verify all dependencies are installed

### Debug Mode

Enable debug logging by modifying the log level in the automation scripts.

## 📝 Development

### Adding New Automation Types

1. Create a new script in `scripts/`
2. Add the automation type to `automation_manager.php`
3. Create batch files in `batch/`
4. Add tests in `test/`

### Script Structure

All automation scripts should follow this structure:
- Constructor: Initialize configuration
- `run()`: Main execution loop
- `log()`: Logging method
- Error handling and retry logic

## 🛡️ Security

- All scripts run with appropriate permissions
- Log files are protected from unauthorized access
- Process management includes proper cleanup
- No sensitive data is logged

## 📞 Support

For issues or questions:
1. Check the log files for error messages
2. Run the test scripts to verify functionality
3. Check the main LMS Olympia documentation
4. Contact the development team

## 📄 License

This automation system is part of the LMS Olympia project and follows the same licensing terms.

