# Auto-Sync Setup Guide

## Overview
This guide explains how to set up automatic synchronization of student data from OneDrive Excel files using HTTP cron jobs.

## Available Endpoints

### 1. Main Import Endpoint
- **URL**: `http://your-domain.com/import-students`
- **Method**: GET
- **Authentication**: None required
- **Purpose**: Primary endpoint for external cron services

### 2. Alternative Controller Endpoint
- **URL**: `http://your-domain.com/admin/import-students`
- **Method**: GET
- **Authentication**: None required
- **Purpose**: Alternative endpoint using controller (as suggested by ChatGPT)

### 3. Test Endpoint
- **URL**: `http://your-domain.com/admin/test-import`
- **Method**: GET
- **Authentication**: None required
- **Purpose**: Test the import functionality

## Response Format

All endpoints return JSON responses in this format:

```json
{
    "success": true,
    "message": "Imported 5 new students, updated 92 students. Errors: 0",
    "created": 5,
    "updated": 92,
    "errors": 0,
    "processed_sheets": [
        {
            "sheet": "DHU LMS",
            "created": 2,
            "updated": 22,
            "errors": 0
        }
    ]
}
```

## Setting Up External Cron Jobs

### Option 1: Using cron-job.org (Recommended for external hosting)

1. Go to [cron-job.org](https://cron-job.org)
2. Create a free account
3. Add a new cron job with these settings:
   - **Title**: LMS Student Import
   - **URL**: `http://your-domain.com/import-students`
   - **Schedule**: Every 5 minutes (`*/5 * * * *`)
   - **Method**: GET

### Option 2: Using Windows Task Scheduler (For local development)

1. Open Task Scheduler
2. Create Basic Task
3. Set trigger to "Daily" and repeat every 5 minutes
4. Set action to "Start a program"
5. Program: `curl`
6. Arguments: `-s "http://127.0.0.1:8000/import-students"`

### Option 3: Using cPanel Cron Jobs (For shared hosting)

1. Login to cPanel
2. Go to "Cron Jobs"
3. Add new cron job:
   - **Minute**: `*/5`
   - **Hour**: `*`
   - **Day**: `*`
   - **Month**: `*`
   - **Weekday**: `*`
   - **Command**: `curl -s "http://your-domain.com/import-students"`

## Testing the Setup

### 1. Test Individual Endpoints

```bash
# Test main endpoint
curl -s "http://your-domain.com/import-students"

# Test controller endpoint
curl -s "http://your-domain.com/admin/import-students"

# Test endpoint
curl -s "http://your-domain.com/admin/test-import"
```

### 2. Expected Response

```json
{
    "success": true,
    "message": "Imported 0 new students, updated 92 students. Errors: 0",
    "created": 0,
    "updated": 92,
    "errors": 0,
    "processed_sheets": [
        {
            "sheet": "DHU LMS",
            "created": 0,
            "updated": 22,
            "errors": 0
        },
        {
            "sheet": "IUC LMS",
            "created": 0,
            "updated": 20,
            "errors": 0
        }
    ]
}
```

## Configuration

### Environment Variables

Make sure these are set in your `.env` file:

```env
ONEDRIVE_EXCEL_URL=https://your-onedrive-direct-download-link
```

### OneDrive URL Conversion

To get a direct download link from OneDrive:

1. Share your Excel file on OneDrive
2. Get the sharing link (usually starts with `https://1drv.ms/`)
3. Convert it to direct download format:
   - Replace `1drv.ms/` with `onedrive.live.com/download?`
   - Add `&resid=` and `&authkey=` parameters

Example:
- Original: `https://1drv.ms/x/s!ABC123...`
- Converted: `https://onedrive.live.com/download?cid=ABC123&resid=DEF456&authkey=GHI789`

## Monitoring and Logs

### Log Files
- **Location**: `storage/logs/laravel.log`
- **Key entries**: Look for "HTTP Cron:" entries

### Success Indicators
- Response status: `"success": true`
- Errors count: `"errors": 0`
- Students updated: `"updated": > 0`

### Error Handling
- Check logs for detailed error messages
- Verify OneDrive URL is accessible
- Ensure database connection is working
- Check file permissions

## Troubleshooting

### Common Issues

1. **"Import failed: Undefined array key"**
   - Solution: Use `/admin/import-students` endpoint instead

2. **"Connection timeout"**
   - Solution: Check OneDrive URL accessibility
   - Verify network connectivity

3. **"Database error"**
   - Solution: Check database connection
   - Verify table structure

4. **"File not found"**
   - Solution: Verify OneDrive URL format
   - Check file sharing permissions

### Debug Commands

```bash
# Test the import locally
php artisan tinker
>>> $service = new App\Services\SheetSpecificImportService();
>>> $result = $service->importFromOneDrive();
>>> dd($result);
```

## Security Considerations

- Endpoints are public (no authentication required)
- Consider adding IP whitelist if needed
- Monitor for abuse or excessive requests
- Use HTTPS in production

## Performance

- Import time: ~30-60 seconds for 100+ students
- Memory usage: ~512MB (configured)
- Execution time: 5 minutes max
- Recommended interval: Every 5-15 minutes

## Support

For issues or questions:
1. Check the logs first
2. Test endpoints manually
3. Verify OneDrive URL
4. Check database connectivity


