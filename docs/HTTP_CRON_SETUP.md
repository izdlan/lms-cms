# HTTP Cron Setup Guide

## 🚀 **Professional Auto-Sync Solution**

This guide shows you how to set up reliable auto-sync using HTTP Cron - the industry standard for scheduled tasks.

## **What is HTTP Cron?**

Instead of running commands on your server, an external service calls your website URL every few minutes. This is more reliable and works from anywhere.

## **Your Cron Endpoint**

**URL:** `https://yourdomain.com/admin/import-students`

**What it does:**
- Checks OneDrive for file changes
- Downloads latest Excel file
- Imports only new students
- Returns JSON status report

## **Setup Options**

### **Option 1: cPanel Cron Jobs (If you have cPanel)**

1. Login to your cPanel
2. Go to **Cron Jobs**
3. Add new cron job:
   - **Minute:** `*/5` (every 5 minutes)
   - **Hour:** `*`
   - **Day:** `*`
   - **Month:** `*`
   - **Weekday:** `*`
   - **Command:** `curl -s "https://yourdomain.com/admin/import-students"`

### **Option 2: External Cron Service (Recommended)**

#### **cron-job.org (Free)**

1. Go to [cron-job.org](https://cron-job.org)
2. Sign up for free account
3. Create new cron job:
   - **Title:** "LMS Auto Sync"
   - **URL:** `https://yourdomain.com/admin/import-students`
   - **Schedule:** Every 5 minutes
   - **Method:** GET

#### **EasyCron (Paid, More Features)**

1. Go to [EasyCron.com](https://www.easycron.com)
2. Create account
3. Add new cron job with your URL
4. Set to run every 5 minutes

### **Option 3: Your Own Server (Advanced)**

If you have server access, add to crontab:

```bash
# Edit crontab
crontab -e

# Add this line (runs every 5 minutes)
*/5 * * * * curl -s "https://yourdomain.com/admin/import-students" > /dev/null 2>&1
```

## **Testing Your Setup**

1. **Test the endpoint directly:**
   - Visit: `https://yourdomain.com/admin/import-students`
   - You should see JSON response with sync status

2. **Check logs:**
   - Look in `storage/logs/laravel.log` for "HTTP Cron" entries

3. **Monitor sync activity:**
   - Go to Auto Sync page in admin panel
   - Check "Recent Sync Activity" section

## **Response Format**

**Success Response:**
```json
{
  "success": true,
  "message": "Sync completed successfully",
  "timestamp": "2025-09-26T12:30:00.000000Z",
  "new_students": 5,
  "created": 3,
  "updated": 2,
  "errors": 0
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Import failed: Connection timeout",
  "timestamp": "2025-09-26T12:30:00.000000Z"
}
```

## **Troubleshooting**

### **Common Issues:**

1. **"Connection refused"**
   - Check if your website is accessible
   - Verify the URL is correct

2. **"404 Not Found"**
   - Make sure you're using the correct URL
   - Check if the route is properly registered

3. **"500 Internal Server Error"**
   - Check Laravel logs for detailed error
   - Verify OneDrive configuration

4. **"Timeout"**
   - Increase timeout in cron service settings
   - Check if OneDrive file is too large

### **Debug Steps:**

1. **Test manually:**
   ```bash
   curl -v "https://yourdomain.com/admin/import-students"
   ```

2. **Check logs:**
   ```bash
   tail -f storage/logs/laravel.log | grep "HTTP Cron"
   ```

3. **Verify OneDrive URL:**
   - Check `.env` file for `ONEDRIVE_EXCEL_URL`
   - Test OneDrive connection in admin panel

## **Security Notes**

- The endpoint is protected by admin authentication
- Only authorized users can access it
- All requests are logged for monitoring

## **Performance Tips**

- **File Size:** Keep Excel files under 10MB for faster processing
- **Frequency:** 5 minutes is usually sufficient for most use cases
- **Monitoring:** Set up alerts for failed syncs

## **Next Steps**

Once HTTP Cron is working:

1. **Monitor the first few runs** to ensure everything works
2. **Set up notifications** if you want email alerts for new students
3. **Consider upgrading** to Microsoft Graph API webhooks for real-time sync

---

**Need Help?** Check the Auto Sync page in your admin panel for real-time status and logs.

