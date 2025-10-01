# 🚀 Auto-Sync Setup Guide

## Overview
This guide explains how to set up **always-running auto-sync** for your LMS system. The auto-sync will automatically import student data from Google Sheets every 5 minutes without any manual intervention.

## 🎯 What This Does
- **Automatically syncs** student data every 5 minutes
- **Runs continuously** in the background
- **Restarts automatically** if it encounters errors
- **Logs all activities** for monitoring
- **No manual intervention** required

## 📋 Prerequisites
- ✅ XAMPP is running
- ✅ Laravel application is working
- ✅ Google Drive URL is configured
- ✅ Database is accessible

## 🚀 Quick Start (Recommended)

### Step 1: Run Setup Script
1. Double-click `setup_auto_sync.bat`
2. Choose option **1** (Start as Windows Service)
3. The auto-sync will start running automatically

### Step 2: Verify It's Working
1. Open your admin panel
2. Go to Auto Sync section
3. Check "Recent Activity" - you should see new entries every 5 minutes
4. Check "Sync Status" - should show current sync times

## 🔧 Alternative Methods

### Method 1: Windows Service (Recommended)
```batch
# Double-click this file
auto_sync_service.bat
```

### Method 2: Background Process
```batch
# Double-click this file
start_auto_sync.bat
```

### Method 3: PowerShell
```powershell
# Right-click and "Run with PowerShell"
start_auto_sync.ps1
```

### Method 4: Manual Command
```bash
# Run in command prompt
php artisan auto-sync:run --continuous
```

## 📊 Monitoring

### Check Status in Admin Panel
1. **Sync Status**: Shows last sync time and next sync
2. **Recent Activity**: Shows all sync activities
3. **Student Count**: Should increase over time

### Check Logs
- Laravel logs: `storage/logs/laravel.log`
- Console output: Check the service window

## ⚙️ Configuration

### Sync Interval
- **Default**: 5 minutes
- **Location**: `app/Services/AutoSyncService.php`
- **Change**: Modify `$syncIntervalMinutes` variable

### Google Drive URL
- **Location**: `.env` file
- **Variable**: `GOOGLE_DRIVE_EXCEL_URL`
- **Format**: `https://docs.google.com/spreadsheets/d/.../export?format=xlsx`

## 🛠️ Troubleshooting

### Auto-Sync Not Starting
1. Check if XAMPP is running
2. Verify Laravel is working: `php artisan --version`
3. Check Google Drive URL in `.env`
4. Run test: `php artisan auto-sync:run`

### Auto-Sync Stopped
1. Check the service window for errors
2. Restart the service
3. Check Laravel logs for details

### No Students Imported
1. Check Google Drive URL is accessible
2. Verify sheet names and structure
3. Check database connection
4. Review import logs

## 🔄 Starting/Stopping

### Start Auto-Sync
```batch
# Run setup script
setup_auto_sync.bat

# Or start service directly
auto_sync_service.bat
```

### Stop Auto-Sync
- Close the service window
- Press Ctrl+C in the service window
- Kill the process in Task Manager

### Restart Auto-Sync
1. Stop the current service
2. Run `setup_auto_sync.bat` again
3. Choose option 1

## 📈 Expected Results

### After 1 Hour
- ✅ 12 sync attempts (every 5 minutes)
- ✅ New students imported
- ✅ Recent Activity shows entries
- ✅ Sync Status shows current times

### After 1 Day
- ✅ 288 sync attempts
- ✅ All available students imported
- ✅ System running smoothly
- ✅ No manual intervention needed

## 🎉 Success Indicators

### ✅ Working Correctly
- Recent Activity updates every 5 minutes
- Sync Status shows current times
- Student count increases
- No error messages

### ❌ Needs Attention
- No Recent Activity updates
- Sync Status shows "Never"
- Error messages in logs
- Service window shows errors

## 📞 Support

If you encounter issues:
1. Check this guide first
2. Review the troubleshooting section
3. Check Laravel logs
4. Restart the service

## 🎯 Summary

**Your boss will be happy because:**
- ✅ **No manual work** required
- ✅ **Runs automatically** every 5 minutes
- ✅ **Starts automatically** on server boot
- ✅ **Restarts automatically** on errors
- ✅ **Monitors itself** and logs activities
- ✅ **Easy to start/stop** when needed

**Just run `setup_auto_sync.bat` and you're done!** 🚀


