# Quick Fix: Imagick Not Working in cPanel

## The Problem
When you run `php check_imagick.php`, you get no output, which means imagick is likely not loaded.

## Quick Fix Steps

### Step 1: Upload the Simple Check Script
1. Upload `check_imagick_simple.php` to your server
2. Run: `php check_imagick_simple.php`

### Step 2: Enable Imagick in PHP Selector

**Important**: You must use **PHP Selector** (not just MultiPHP Manager)

1. **Go to cPanel**
2. **Click "Select PHP Version"**
3. **Find your domain**: `lms.olympia-education.com`
4. **Click the blue "Use PHP Selector" button** (next to your domain)
5. **Scroll down to find "imagick"** (under "I" section)
6. **Check the box** next to "imagick"
7. **Click "Apply"** or "Save" button at the bottom

### Step 3: Verify It Works

Run the check script again:
```bash
cd /home/serimala/lms.olympia-education.com
php check_imagick_simple.php
```

You should see:
```
========================================
IMAGICK EXTENSION CHECK
========================================

Extension Loaded: YES
Class Exists: YES
Can Create Imagick: YES
Can Create PNG: YES

========================================
SUCCESS: Imagick is working!
========================================
```

### Step 4: Test QR Code Generation

1. Go to admin page
2. Download a Word certificate
3. Check logs: `storage/logs/laravel.log`
4. Should see: `"imagick_loaded":true` and `"QR Code generated successfully as PNG"`

## If Still Not Working

### Check PHP Version
Make sure you're enabling imagick for the **correct PHP version**:
```bash
php -v
```

Your domain is using: `ea-php82` (PHP 8.2)
Make sure imagick is enabled for **PHP 8.2**, not PHP 8.3.

### Check via PHP Info
Create a file `phpinfo.php`:
```php
<?php phpinfo(); ?>
```

Upload and visit: `http://lms.olympia-education.com/phpinfo.php`
Search for "imagick" - if it's not listed, it's not enabled.

### Contact Serverfreak Support
If imagick is not available in PHP Selector, contact Serverfreak support and ask:
> "Please enable the PHP imagick extension for my domain lms.olympia-education.com. It's required for QR code generation in my Laravel application."

## Alternative: Use Browser Method

1. Upload `check_imagick_simple.php` to `public_html/` or your Laravel root
2. Visit: `http://lms.olympia-education.com/check_imagick_simple.php`
3. You'll see the results in your browser

