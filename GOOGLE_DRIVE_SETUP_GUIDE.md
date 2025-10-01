# Google Drive Auto-Sync Setup Guide

## 🎯 **Overview**

Your Laravel application now uses **Google Drive** instead of OneDrive for auto-sync. Google Drive is much more reliable and easier to set up!

## ✅ **What's Already Working**

- ✅ **Google Drive Import Service** - New service created
- ✅ **HTTP Endpoints** - Updated to use Google Drive
- ✅ **Error Handling** - Comprehensive logging
- ✅ **Student Data Processing** - All previous fixes included

## 🚀 **Quick Setup (5 Minutes)**

### **Step 1: Upload Excel File to Google Drive**

1. **Go to Google Drive** (drive.google.com)
2. **Create a new folder** called "LMS Data" (or any name you prefer)
3. **Upload your Excel file** (e.g., `students.xlsx`) to this folder
4. **Right-click the file** → **Share** → **Anyone with the link can view**
5. **Copy the sharing link**

### **Step 2: Get Direct Download URL**

Your sharing link will look like:
```
https://drive.google.com/file/d/1ABC123DEF456GHI789JKL/view?usp=sharing
```

Convert it to direct download format:
```
https://drive.google.com/uc?export=download&id=1ABC123DEF456GHI789JKL
```

### **Step 3: Update Your .env File**

Add this line to your `.env` file:
```env
GOOGLE_DRIVE_EXCEL_URL=https://drive.google.com/uc?export=download&id=YOUR_FILE_ID
```

### **Step 4: Test the Import**

```bash
# Test via command line
php artisan tinker
>>> $service = new App\Services\GoogleDriveImportService();
>>> $result = $service->importFromGoogleDrive();
>>> dd($result);
```

### **Step 5: Test HTTP Endpoints**

```bash
# Test main endpoint
curl -s "http://127.0.0.1:8000/admin/import-students"

# Test alternative endpoint
curl -s "http://127.0.0.1:8000/import-students"

# Test endpoint
curl -s "http://127.0.0.1:8000/admin/test-import"
```

## 🔧 **How It Works**

### **1. Google Drive Monitoring**
- Laravel checks Google Drive every time the endpoint is called
- Downloads the latest Excel file when accessed
- No authentication required (public file)

### **2. Excel Processing**
- Reads all sheets from the Excel file
- Processes specific LMS sheets (DHU, IUC, LUC, EXECUTIVE, UPM, TVET)
- Extracts student data (Name, IC, Email, Phone, Address)

### **3. Database Import**
- Creates new students or updates existing ones
- Handles all the data validation and error checking
- Logs all operations for monitoring

## 📊 **Expected Response**

When working correctly, you should see:
```json
{
  "status": "success",
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

## 🚀 **Set Up Auto-Sync**

### **Option 1: External Cron Service (Recommended)**

1. Go to [cron-job.org](https://cron-job.org) (free)
2. Create account and add new cron job:
   - **URL**: `http://your-domain.com/admin/import-students`
   - **Schedule**: `*/5 * * * *` (every 5 minutes)
   - **Method**: GET

### **Option 2: Windows Task Scheduler**

1. Open Task Scheduler
2. Create Basic Task:
   - **Name**: "LMS Google Drive Sync"
   - **Trigger**: Daily, repeat every 5 minutes
   - **Action**: Start a program
   - **Program**: `curl`
   - **Arguments**: `-s "http://127.0.0.1:8000/admin/import-students"`

### **Option 3: cPanel Cron Jobs**

1. Login to cPanel
2. Go to "Cron Jobs"
3. Add new cron job:
   - **Minute**: `*/5`
   - **Hour**: `*`
   - **Day**: `*`
   - **Month**: `*`
   - **Weekday**: `*`
   - **Command**: `curl -s "http://your-domain.com/admin/import-students"`

## 🔍 **Monitoring & Debugging**

### **Check Logs**
```bash
tail -f storage/logs/laravel.log
```

### **Test Endpoints Manually**
```bash
# Test main endpoint
curl -s "http://127.0.0.1:8000/admin/import-students"

# Test with verbose output
curl -v "http://127.0.0.1:8000/admin/import-students"
```

### **Debug Commands**
```bash
# Test the import service directly
php artisan tinker
>>> $service = new App\Services\GoogleDriveImportService();
>>> $result = $service->importFromGoogleDrive();
>>> dd($result);
```

## 🔧 **Troubleshooting**

### **Common Issues:**

1. **"Google Drive URL not configured"**
   - Check your `.env` file has `GOOGLE_DRIVE_EXCEL_URL`
   - Make sure the URL is correct

2. **"Failed to download file from Google Drive"**
   - Check if the file is publicly accessible
   - Verify the direct download URL format
   - Make sure the file ID is correct

3. **"No data found in sheet"**
   - Check if the Excel file has the expected sheet structure
   - Verify the sheet names match the configuration

4. **"Could not find header row"**
   - Check if the Excel file has proper headers
   - Look for "NAME", "IC", "PASSPORT" columns

### **URL Format Examples:**

**Sharing URL:**
```
https://drive.google.com/file/d/1ABC123DEF456GHI789JKL/view?usp=sharing
```

**Direct Download URL:**
```
https://drive.google.com/uc?export=download&id=1ABC123DEF456GHI789JKL
```

**Alternative formats that work:**
```
https://drive.google.com/uc?id=1ABC123DEF456GHI789JKL&export=download
https://docs.google.com/spreadsheets/d/1ABC123DEF456GHI789JKL/export?format=xlsx
```

## 🎯 **Advantages of Google Drive**

✅ **No authentication required** - public files work directly  
✅ **More reliable** - Google's infrastructure is very stable  
✅ **Easier setup** - no Azure App Registration needed  
✅ **Better error handling** - clearer error messages  
✅ **Faster processing** - direct download without API complexity  
✅ **Free to use** - no additional costs  

## 📋 **Configuration Summary**

### **Environment Variables**
```env
# Google Drive Configuration
GOOGLE_DRIVE_EXCEL_URL=https://drive.google.com/uc?export=download&id=YOUR_FILE_ID

# Optional: Keep existing Google Sheets as backup
GOOGLE_SHEETS_SPREADSHEET_ID=1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk
```

### **Processed Sheets**
- DHU LMS (Sheet 11)
- IUC LMS (Sheet 12)
- LUC LMS (Sheet 14)
- EXECUTIVE LMS (Sheet 15)
- UPM LMS (Sheet 16)
- TVET LMS (Sheet 17)

## 🎉 **Success!**

Your auto-sync system is now using **Google Drive** which is:
- ✅ **More reliable** than OneDrive
- ✅ **Easier to set up** than Azure authentication
- ✅ **Better for public file sharing**
- ✅ **Faster and more stable**

Just upload your Excel file to Google Drive, get the direct download URL, update your `.env` file, and set up your cron job! 🚀

## 🚀 **Next Steps**

1. **Upload Excel file** to Google Drive
2. **Get direct download URL** and update `.env`
3. **Test the import** with the endpoints
4. **Set up your cron job** for auto-sync
5. **Monitor the logs** to ensure everything works

Your Laravel application will now automatically sync with Google Drive Excel files! 🎯


