# Google Sheets Integration for LMS Olympia

This document explains how to use the Google Sheets integration for automatic student data import in the LMS Olympia system.

## Overview

The Google Sheets integration allows you to automatically import student data directly from a Google Sheets document, eliminating the need to manually download and upload Excel files. The system monitors the Google Sheets for changes and automatically imports new or updated student data.

## Google Sheets URL

The system is configured to monitor this Google Sheets document:
- **URL**: https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk/edit?usp=sharing&ouid=117738643589016699947&rtpof=true&sd=true
- **Format**: CSV export is used for data retrieval
- **Check Interval**: Every 5 minutes

## Features

- **Automatic Change Detection**: Monitors Google Sheets for changes
- **Real-time Import**: Automatically imports student data when changes are detected
- **Flexible Data Mapping**: Handles various column formats and structures
- **Error Handling**: Comprehensive error logging and reporting
- **Email Notifications**: Optional email notifications for import results
- **Backward Compatibility**: Can switch between Google Sheets and Excel file monitoring

## Files Created/Modified

### New Files
1. `app/Services/GoogleSheetsImportService.php` - Main service for Google Sheets import
2. `app/Console/Commands/GoogleSheetsImport.php` - Artisan command for manual import
3. `google_sheets_automation_watcher.php` - Standalone automation watcher
4. `start_google_sheets_automation.bat` - Windows batch file to start automation
5. `start_google_sheets_automation.ps1` - PowerShell script to start automation
6. `test_google_sheets_import.php` - Test script for Google Sheets integration

### Modified Files
1. `automation_watcher.php` - Updated to support both Google Sheets and Excel file monitoring

## Usage

### Method 1: Using the Updated Automation Watcher

The main automation watcher now supports both Google Sheets and Excel files:

```bash
php automation_watcher.php
```

To switch between Google Sheets and Excel file monitoring, edit `automation_watcher.php` and change:
```php
$useGoogleSheets = true; // Set to true for Google Sheets, false for Excel
```

### Method 2: Using Dedicated Google Sheets Automation

Run the dedicated Google Sheets automation watcher:

```bash
php google_sheets_automation_watcher.php
```

Or use the provided batch files:
- **Windows**: Double-click `start_google_sheets_automation.bat`
- **PowerShell**: Run `start_google_sheets_automation.ps1`

### Method 3: Manual Import via Artisan Command

Import data manually using the Artisan command:

```bash
# Import with change detection
php artisan students:google-sheets-import

# Force import (ignore change detection)
php artisan students:google-sheets-import --force

# Import with email notification
php artisan students:google-sheets-import --email=admin@example.com
```

### Method 4: Test the Integration

Test the Google Sheets connection and import:

```bash
php test_google_sheets_import.php
```

## Data Structure

The Google Sheets integration expects the same data structure as the Excel import:

### Required Columns
- **NAME**: Student's full name
- **IC/PASSPORT**: Student's IC or passport number
- **EMAIL**: Student's email address (optional, will be generated if missing)

### Optional Columns
- **ADDRESS**: Student's address
- **CONTACT NO.**: Student's phone number
- **STUDENT ID**: Student's ID number
- **COL REF. NO.**: Reference number
- **PROGRAMME NAME**: Course/program name
- **SUPERVISOR**: Research supervisor
- **RESEARCH TITLE**: Research project title
- And many more...

## Configuration

### Google Sheets URL
To change the Google Sheets URL, edit the following files:
- `app/Services/GoogleSheetsImportService.php` (line 12)
- `automation_watcher.php` (line 13)
- `google_sheets_automation_watcher.php` (line 8)

### Check Interval
To change how often the system checks for changes:
- **Google Sheets**: 5 minutes (300 seconds) - edit `checkInterval` variable
- **Excel File**: 1 minute (60 seconds) - edit `checkInterval` variable

### Email Notifications
Email notifications are sent when:
- New students are created
- Import errors occur
- Email is configured in the system

## Monitoring and Logs

### Log Files
- **Laravel Logs**: `storage/logs/laravel.log`
- **Console Output**: Real-time status updates

### Log Messages
The system logs detailed information about:
- Import start/completion
- Number of students created/updated
- Error details
- Change detection status

## Troubleshooting

### Common Issues

1. **Connection Timeout**
   - Check internet connection
   - Verify Google Sheets URL is accessible
   - Increase timeout in `GoogleSheetsImportService.php`

2. **No Changes Detected**
   - Use `--force` flag for manual import
   - Check if Google Sheets is publicly accessible
   - Verify CSV export URL is correct

3. **Import Errors**
   - Check Laravel logs for detailed error messages
   - Verify data format matches expected structure
   - Ensure required fields are present

4. **Permission Issues**
   - Ensure Google Sheets is publicly viewable
   - Check if CSV export is enabled
   - Verify URL parameters are correct

### Debug Mode

Enable debug mode by adding logging statements or running the test script:

```bash
php test_google_sheets_import.php
```

## Security Considerations

- Google Sheets URL should be publicly accessible for CSV export
- No authentication is required for read-only access
- Student data is processed locally and stored in the database
- Email notifications should use secure SMTP configuration

## Performance

- **Check Interval**: 5 minutes for Google Sheets (configurable)
- **Timeout**: 30 seconds for HTTP requests
- **Memory Usage**: Optimized for large datasets
- **Caching**: Uses Laravel cache for change detection

## Migration from Excel to Google Sheets

To migrate from Excel file monitoring to Google Sheets:

1. Update `automation_watcher.php`:
   ```php
   $useGoogleSheets = true;
   ```

2. Stop the current automation watcher
3. Start the new automation watcher
4. Verify the integration is working

## Support

For issues or questions:
1. Check the Laravel logs
2. Run the test script
3. Verify Google Sheets accessibility
4. Check network connectivity

## Future Enhancements

Potential improvements:
- Multiple Google Sheets support
- Real-time webhook integration
- Advanced change detection
- Custom field mapping
- Batch processing optimization

