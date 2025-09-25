# OneDrive Auto Import Setup Guide

## 🚀 Overview
This system automatically imports students from OneDrive Excel files every 5 minutes, ensuring your LMS stays up-to-date with the latest student data.

## 📋 Features
- **Automatic Import**: Checks OneDrive every 5 minutes for new students
- **Web Interface**: Manage automation through the admin panel
- **Email Notifications**: Get notified when new students are imported
- **Error Handling**: Comprehensive logging and error reporting
- **Manual Controls**: Run imports manually when needed

## 🛠️ Setup Instructions

### Step 1: Test the Import Command
```bash
php artisan import:onedrive-auto
```

### Step 2: Set Up Windows Task Scheduler (Recommended)
1. Open Command Prompt as Administrator
2. Run: `php scripts/setup/setup_auto_import.php`
3. Choose option 2 to create Windows Task Scheduler task
4. The system will automatically run every 5 minutes

### Step 3: Alternative - Manual Batch File
1. Run: `php scripts/setup/setup_auto_import.php`
2. Choose option 3 to create a manual batch file
3. Double-click `manual_onedrive_import.bat` to run imports manually

## 🌐 Web Interface

### Access the Automation Panel
1. Log in to your LMS admin panel
2. Click **"OneDrive Auto Import"** in the sidebar
3. Use the controls to manage automation

### Available Actions
- **Run Import Now**: Manually trigger an import
- **Check Status**: View current automation status
- **View Logs**: See import history and errors

## 📊 Expected Results
- **92 students** imported from all LMS sheets
- **Real student names** (not program names)
- **Generated emails** for students with missing emails
- **Automatic updates** every 5 minutes

## 📁 File Structure
```
automation/
├── batch/
│   ├── auto_onedrive_import.bat      # Windows batch file
│   └── auto_onedrive_import.ps1      # PowerShell script
└── logs/
    └── auto_import.log               # Import logs

app/Console/Commands/
└── AutoOneDriveImport.php            # Laravel command

resources/views/
├── admin/
│   └── automation-onedrive.blade.php # Web interface
└── emails/
    └── auto-import-notification.blade.php # Email template
```

## ⚙️ Configuration

### Environment Variables
Make sure these are set in your `.env` file:
```env
ONEDRIVE_EXCEL_URL=https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=BzgW7r
MAIL_ADMIN_EMAIL=admin@olympia.edu.my
```

### Email Notifications
The system will send email notifications when:
- New students are imported
- Import errors occur
- Automation status changes

## 🔧 Troubleshooting

### Common Issues
1. **Import not running automatically**
   - Check Windows Task Scheduler
   - Verify the batch file path is correct
   - Check the automation logs

2. **OneDrive connection errors**
   - Verify the OneDrive URL is correct
   - Check if the file is publicly accessible
   - Review the import logs for specific errors

3. **Students not importing**
   - Check the Excel file format
   - Verify sheet names match expected format
   - Review the import validation rules

### Log Files
- **Automation Logs**: `automation/logs/auto_import.log`
- **Laravel Logs**: `storage/logs/laravel.log`
- **Import Results**: Available in the web interface

## 📈 Monitoring

### Check Automation Status
1. Go to **OneDrive Auto Import** in admin panel
2. Click **"Check Status"** to see current status
3. View **"Last Import"** time and results

### View Import History
1. Click **"View Logs"** in the automation panel
2. Review import results and any errors
3. Check email notifications for updates

## 🎯 Success Indicators
- ✅ **92 students** imported successfully
- ✅ **0 errors** in import logs
- ✅ **Automatic updates** every 5 minutes
- ✅ **Email notifications** working
- ✅ **Web interface** accessible

## 📞 Support
If you encounter any issues:
1. Check the troubleshooting section above
2. Review the log files for specific errors
3. Test the manual import first
4. Contact system administrator if needed

---

**Note**: This automation system is designed to work with the specific OneDrive Excel file format used by Olympia Education. If the Excel file structure changes, the import logic may need to be updated.
