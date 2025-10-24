# LMS Auto-Sync Deployment Guide

## Overview
This guide explains how to deploy and run the LMS auto-sync feature in different environments.

## 🖥️ **For Local Windows Development**

### Option 1: Using Batch Files (Recommended)
1. **Run the setup script:**
   ```cmd
   setup_auto_sync.bat
   ```

2. **Choose your preferred method:**
   - **Option 1**: Windows Service (requires admin privileges)
   - **Option 2**: Background Process (runs in background)
   - **Option 3**: Current Window (runs in current terminal)

### Option 2: Manual Testing
```cmd
test_auto_sync.bat
```

### Option 3: Direct Command
```cmd
cd C:\xampp\htdocs\lms-cms
php artisan auto-sync:run
```

## 🌐 **For cPanel Hosting (Production)**

### Step 1: Upload Files
1. Upload your entire `lms-cms` project to cPanel
2. Ensure all files are in the correct directory

### Step 2: Configure Environment
Create/update your `.env` file:
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

### Step 3: Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
php artisan migrate
```

### Step 4: Set Up Cron Job
1. Go to **cPanel → Cron Jobs**
2. Add new cron job:
   - **Minute**: `*/5` (every 5 minutes)
   - **Hour**: `*`
   - **Day**: `*`
   - **Month**: `*`
   - **Weekday**: `*`
   - **Command**: `/usr/local/bin/php /home/yourusername/public_html/lms-cms/artisan auto-sync:run`

**Important**: Replace the path with your actual project path.

### Step 5: Alternative - PHP Cron Script
If direct artisan commands don't work, use the PHP script:
1. Upload `cron_auto_sync.php` to your project root
2. Set up cron job to run: `/usr/local/bin/php /home/yourusername/public_html/lms-cms/cron_auto_sync.php`

## 📋 **Available Commands**

### Auto-Sync Commands
```bash
# Run auto-sync once
php artisan auto-sync:run

# Force Google Sheets import
php artisan students:google-sheets-import --force

# Check available commands
php artisan list | grep auto-sync
```

### Manual Testing
```bash
# Test auto-sync functionality
php artisan auto-sync:run

# Check sync status (if available)
php artisan auto-sync:status
```

## 🔧 **Troubleshooting**

### Common Issues

#### 1. "Command not found" Error
- **Solution**: Make sure you're in the correct project directory
- **Check**: Run `pwd` (Linux) or `cd` (Windows) to verify location

#### 2. Permission Denied
- **Solution**: Check file permissions (755 for directories, 644 for files)
- **Linux**: `chmod -R 755 your_project_directory`

#### 3. Database Connection Error
- **Solution**: Verify `.env` file database settings
- **Check**: Test database connection manually

#### 4. Google Sheets Access Error
- **Solution**: Verify Google Sheets URL is accessible
- **Check**: Test URL in browser

#### 5. Cron Job Not Running
- **Solution**: 
  - Check cron job path is correct
  - Verify PHP path (`/usr/local/bin/php`)
  - Check cron job syntax
  - Test with a simple command first

### Testing Steps
1. **Test auto-sync manually:**
   ```bash
   php artisan auto-sync:run
   ```

2. **Check logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Verify database updates:**
   - Check admin panel
   - Look for recent student updates

## 📁 **File Structure**

```
lms-cms/
├── setup_auto_sync.bat          # Windows setup script
├── auto_sync_service.bat        # Windows service script
├── test_auto_sync.bat           # Windows test script
├── cron_auto_sync.php           # cPanel cron script
├── cpanel_auto_sync_setup.md    # cPanel setup guide
└── AUTO_SYNC_DEPLOYMENT_GUIDE.md # This guide
```

## 🚀 **Quick Start**

### For Windows Development:
```cmd
# 1. Run setup
setup_auto_sync.bat

# 2. Choose option 1 or 2
# 3. Auto-sync will start running every 5 minutes
```

### For cPanel Production:
```bash
# 1. Upload files to cPanel
# 2. Set up .env file
# 3. Run: composer install --no-dev --optimize-autoloader
# 4. Run: php artisan migrate
# 5. Set up cron job: */5 * * * * /usr/local/bin/php /path/to/artisan auto-sync:run
```

## 📊 **Monitoring**

### Check Auto-Sync Status
1. **Admin Panel**: Go to Auto-Sync section
2. **Logs**: Check `storage/logs/laravel.log`
3. **Database**: Check recent student updates
4. **Manual Test**: Run `php artisan auto-sync:run`

### Success Indicators
- ✅ No errors in logs
- ✅ Students being updated in database
- ✅ Recent activity in admin panel
- ✅ Auto-sync running every 5 minutes

## 🔒 **Security Notes**
- Keep `.env` file secure and not publicly accessible
- Use strong database passwords
- Keep Google Sheets URL private
- Regularly backup your database
- Monitor logs for any suspicious activity

## 📞 **Support**
If you encounter issues:
1. Check the troubleshooting section above
2. Review Laravel logs in `storage/logs/laravel.log`
3. Test each component individually
4. Verify all file permissions and paths
