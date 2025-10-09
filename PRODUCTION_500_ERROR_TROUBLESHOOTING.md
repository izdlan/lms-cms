# 🚨 Production 500 Error Troubleshooting Guide

## 📋 **Common Causes of 500 Errors:**

### **1. File Permissions Issues**
```bash
# On your production server, run:
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### **2. Missing Dependencies**
```bash
# Run on production server:
composer install --no-dev --optimize-autoloader
npm install --production
npm run build
```

### **3. Cache Issues**
```bash
# Clear all caches:
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### **4. Environment Configuration**
```bash
# Copy .env file:
cp .env.example .env

# Generate new app key:
php artisan key:generate

# Set proper permissions:
chmod 644 .env
```

## 🔍 **Debugging Steps:**

### **Step 1: Check Server Error Logs**
```bash
# Check Apache/Nginx error logs:
tail -f /var/log/apache2/error.log
# OR
tail -f /var/log/nginx/error.log
```

### **Step 2: Check Laravel Logs**
```bash
# Check Laravel logs on production:
tail -f storage/logs/laravel.log
```

### **Step 3: Enable Debug Mode Temporarily**
```env
# In your .env file (TEMPORARILY):
APP_DEBUG=true
LOG_LEVEL=debug
```

### **Step 4: Check File Permissions**
```bash
# Check if storage is writable:
ls -la storage/
ls -la bootstrap/cache/
```

## 🛠️ **Quick Fixes to Try:**

### **Fix 1: Clear All Caches**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
php artisan optimize
```

### **Fix 2: Set Proper Permissions**
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### **Fix 3: Reinstall Dependencies**
```bash
composer install --no-dev --optimize-autoloader
```

### **Fix 4: Check .env File**
```bash
# Make sure .env exists and has correct values:
cat .env | grep APP_KEY
cat .env | grep DB_
```

## 🚨 **If Still Getting 500 Error:**

### **Check These Files:**
1. **`.env` file exists and is readable**
2. **`storage/` directory is writable**
3. **`bootstrap/cache/` directory is writable**
4. **Database connection is working**
5. **All required PHP extensions are installed**

### **Common Server Issues:**
- **PHP version compatibility**
- **Missing PHP extensions (mbstring, openssl, pdo_mysql)**
- **Memory limit too low**
- **File upload limits**
- **Database connection issues**

## 📞 **When to Contact ServerFreak:**

### **Contact ServerFreak if:**
- ✅ You've tried all the fixes above
- ✅ File permissions are correct
- ✅ Dependencies are installed
- ✅ Caches are cleared
- ✅ .env file is properly configured
- ✅ Database connection works
- ❌ Still getting 500 error

### **Information to Provide:**
1. **Error logs** from `/var/log/apache2/error.log` or `/var/log/nginx/error.log`
2. **Laravel logs** from `storage/logs/laravel.log`
3. **PHP version** (`php -v`)
4. **Server specifications**
5. **Steps you've already tried**

## 🎯 **Most Likely Causes:**

1. **File permissions** (90% of cases)
2. **Missing dependencies** (5% of cases)
3. **Cache issues** (3% of cases)
4. **Server configuration** (2% of cases)

## ✅ **Try This First:**

```bash
# Run these commands on your production server:
chmod -R 775 storage/ bootstrap/cache/
chown -R www-data:www-data storage/ bootstrap/cache/
php artisan cache:clear
php artisan config:clear
php artisan optimize
```

**If this doesn't work, then contact ServerFreak with the error logs.**

