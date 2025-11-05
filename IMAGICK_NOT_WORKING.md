# Imagick Not Working - Fix Guide

## Problem
The logs show `imagick_loaded":false` even though you enabled it in cPanel. This means imagick is NOT actually loaded by PHP.

## Why This Happens

1. **PHP Version Mismatch**: You enabled imagick for one PHP version, but your site is using a different version
2. **Extension Not Activated**: The extension is checked but not actually loaded
3. **PHP Needs Restart**: Changes require a PHP/Apache restart
4. **CloudLinux PHP Selector**: The extension might be enabled in one selector but not the active one

## How to Fix

### Step 1: Verify Current PHP Version
1. Go to cPanel → **Select PHP Version**
2. Find your domain: `lms.olympia-education.com`
3. Note the PHP version shown (e.g., `ea-php82`)

### Step 2: Enable Imagick for That Specific Version
1. Click **"Use PHP Selector"** button for your domain
2. You'll see a list of extensions
3. Scroll to **"I"** section
4. Find **"imagick"** and **check the box**
5. Click **"Apply"** or **"Save"**

### Step 3: Verify It's Working
Run this command in SSH or create a PHP file:
```php
<?php
echo extension_loaded('imagick') ? 'Imagick: Enabled ✅' : 'Imagick: Disabled ❌';
phpinfo();
?>
```

Or run the diagnostic script:
```bash
php check_imagick.php
```

### Step 4: Check Logs After Regenerating Certificate
After fixing imagick, regenerate a certificate and check logs:
- ✅ Should see: `"QR Code generated successfully as PNG"`
- ❌ Should NOT see: `"imagick_loaded":false`

## Common Issues

### Issue: "Extension enabled but still not working"
**Solution**: 
- Make sure you're using **PHP Selector** (not just MultiPHP Manager)
- Restart PHP/Apache if possible
- Check if you're using CloudLinux - extensions must be enabled per domain

### Issue: "Different PHP versions"
**Solution**:
- Your domain might be using a different PHP version than where you enabled imagick
- Check the exact PHP version for `lms.olympia-education.com`
- Enable imagick for that specific version

### Issue: "Can't find imagick in extensions list"
**Solution**:
- Contact Serverfreak support
- They may need to install imagick at the server level
- Some shared hosts don't provide imagick

## Quick Test

After enabling imagick, test immediately:
1. Go to admin page
2. Download a Word certificate
3. Check logs: `storage/logs/laravel.log`
4. Look for: `"imagick_loaded":true` and `"QR Code generated successfully as PNG"`

## Alternative: Use a Different QR Code Library

If imagick cannot be enabled, we can switch to a different QR code library that works with GD only. However, this requires code changes.

