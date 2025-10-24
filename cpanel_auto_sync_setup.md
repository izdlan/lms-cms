# cPanel Auto-Sync Setup Guide

## Overview
This guide explains how to set up the LMS auto-sync feature on cPanel hosting. Since cPanel runs on Linux servers, we cannot use Windows batch files (.bat). Instead, we'll use cron jobs.

## Step 1: Upload Your Files
1. Upload your entire `lms-cms` project to your cPanel hosting
2. Make sure all files are uploaded to the correct directory (usually `public_html` or a subdirectory)

## Step 2: Set Up Environment
1. In cPanel, go to **File Manager**
2. Navigate to your project directory
3. Create/update your `.env` file with the correct database and Google Sheets settings:

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Google Sheets Configuration
GOOGLE_SHEETS_URL=https://docs.google.com/spreadsheets/d/1MnAeovkeOM_pZGx6DqS7hMvJQyaQDEu8/edit?usp=sharing&ouid=117738643589016699947&rtpof=true&sd=true
GOOGLE_SHEETS_SPREADSHEET_ID=1MnAeovkeOM_pZGx6DqS7hMvJQyaQDEu8
```

## Step 3: Install Dependencies
1. In cPanel, go to **Terminal** (if available) or use **File Manager**
2. Navigate to your project directory
3. Run: `composer install --no-dev --optimize-autoloader`

## Step 4: Run Migrations
1. In Terminal, run: `php artisan migrate`
2. This will create all necessary database tables

## Step 5: Set Up Cron Job
1. In cPanel, go to **Cron Jobs**
2. Add a new cron job with these settings:
   - **Minute**: `*/5` (every 5 minutes)
   - **Hour**: `*`
   - **Day**: `*`
   - **Month**: `*`
   - **Weekday**: `*`
   - **Command**: `/usr/local/bin/php /home/yourusername/public_html/lms-cms/artisan auto-sync:run`

**Important**: Replace `/home/yourusername/public_html/lms-cms` with your actual project path.

## Step 6: Test the Setup
1. Go to your LMS admin panel
2. Navigate to the Auto-Sync section
3. Click "Force Sync" to test if it works
4. Check the logs to ensure everything is working

## Alternative: Manual Sync
If cron jobs are not available, you can manually trigger sync by:
1. Going to your admin panel
2. Clicking "Force Sync" in the Auto-Sync section
3. Or running the command manually in Terminal: `php artisan auto-sync:run`

## Troubleshooting

### If cron job doesn't work:
1. Check the cron job path is correct
2. Make sure PHP path is correct (usually `/usr/local/bin/php`)
3. Check file permissions (should be 755 for directories, 644 for files)

### If auto-sync fails:
1. Check your `.env` file configuration
2. Verify Google Sheets URL is accessible
3. Check Laravel logs in `storage/logs/laravel.log`

### To check if cron is working:
1. Add a simple test cron job that creates a file
2. Check if the file is created every 5 minutes

## Security Notes
- Make sure your `.env` file is not publicly accessible
- Use strong database passwords
- Keep your Google Sheets URL private
- Regularly backup your database

## Support
If you encounter issues:
1. Check the Laravel logs
2. Verify all file permissions
3. Ensure all dependencies are installed
4. Test the Google Sheets connection manually
