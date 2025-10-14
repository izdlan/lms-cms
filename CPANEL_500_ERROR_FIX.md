# 🚨 cPanel 500 Error Fix Guide

## 📋 **cPanel-Specific Fixes:**

### **1. Check Error Logs in cPanel**
1. **Login to cPanel**
2. **Go to "Error Logs"** (in the "Metrics" section)
3. **Look for recent errors** - this will show you the exact cause

### **2. File Manager Permissions**
1. **Open File Manager** in cPanel
2. **Navigate to your Laravel project folder**
3. **Right-click on `storage` folder** → **Permissions** → **Set to 755**
4. **Right-click on `bootstrap/cache` folder** → **Permissions** → **Set to 755**

### **3. Check .env File**
1. **In File Manager**, make sure `.env` file exists
2. **Right-click on `.env`** → **Permissions** → **Set to 644**
3. **Edit the file** to ensure it has your production values

### **4. Clear Laravel Caches via Terminal**
1. **Go to "Terminal"** in cPanel
2. **Navigate to your project**: `cd public_html/your-project-folder`
3. **Run these commands**:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### **5. Check PHP Version**
1. **Go to "Select PHP Version"** in cPanel
2. **Make sure you're using PHP 8.1 or 8.2**
3. **Enable required extensions**:
   - ✅ `mbstring`
   - ✅ `openssl`
   - ✅ `pdo_mysql`
   - ✅ `fileinfo`
   - ✅ `curl`

## 🔧 **Quick cPanel Fixes:**

### **Fix 1: File Permissions via File Manager**
```
storage/ → 755
bootstrap/cache/ → 755
.env → 644
```

### **Fix 2: Check Error Logs**
1. **cPanel → Error Logs**
2. **Look for the exact error message**
3. **This will tell you what's wrong**

### **Fix 3: PHP Settings**
1. **cPanel → Select PHP Version**
2. **Set to PHP 8.1 or 8.2**
3. **Enable all required extensions**

### **Fix 4: Database Connection**
1. **cPanel → MySQL Databases**
2. **Make sure your database exists**
3. **Check database credentials in .env**

## 🚨 **Most Common cPanel Issues:**

### **1. File Permissions (90% of cases)**
- Storage folder not writable
- Bootstrap cache not writable

### **2. PHP Version (5% of cases)**
- Wrong PHP version
- Missing extensions

### **3. .env File Issues (3% of cases)**
- Missing .env file
- Wrong permissions
- Incorrect values

### **4. Database Issues (2% of cases)**
- Database doesn't exist
- Wrong credentials
- Connection timeout

## 📞 **cPanel Support vs ServerFreak:**

### **Contact cPanel Support if:**
- ✅ You've tried all the fixes above
- ✅ File permissions are correct
- ✅ PHP version is correct
- ✅ Extensions are enabled
- ❌ Still getting 500 error

### **Contact ServerFreak if:**
- ❌ You don't have cPanel access
- ❌ cPanel support can't help
- ❌ It's a server-level issue

## 🎯 **Step-by-Step cPanel Fix:**

### **Step 1: Check Error Logs**
1. Login to cPanel
2. Go to "Error Logs"
3. Look for recent errors
4. **This will show you the exact problem**

### **Step 2: Fix File Permissions**
1. File Manager → your project folder
2. Right-click `storage` → Permissions → 755
3. Right-click `bootstrap/cache` → Permissions → 755
4. Right-click `.env` → Permissions → 644

### **Step 3: Check PHP Version**
1. "Select PHP Version" in cPanel
2. Choose PHP 8.1 or 8.2
3. Enable all required extensions

### **Step 4: Clear Caches**
1. "Terminal" in cPanel
2. Navigate to your project
3. Run the cache clearing commands

## ✅ **Most Likely Solution:**

**90% of cPanel 500 errors are fixed by:**
1. **Setting correct file permissions**
2. **Checking the error logs**
3. **Clearing Laravel caches**

**Try these first before contacting support!**



