# How to Enable Imagick Extension in cPanel

## Problem
Imagick works in CLI (command line) but NOT in web requests. This causes QR code PNG generation to fail.

## Solution: Enable Imagick for Web Requests

### Step 1: Access PHP Selector
1. Log in to cPanel
2. Look for **"Select PHP Version"** or **"MultiPHP Manager"** in the Software section
3. Click on it

### Step 2: Select Your Domain
1. You should see a list of domains/directories
2. Select your domain (e.g., `lms.olympia-education.com`)
3. Make sure PHP 8.2 is selected (or the version you're using)

### Step 3: Enable Imagick Extension
1. Scroll down to the **Extensions** section
2. Look for **`imagick`** in the list
3. **Check the box** next to `imagick` to enable it
4. Click **"Save"** or **"Apply"** button

### Step 4: Restart PHP-FPM
1. After saving, you may need to restart PHP-FPM
2. Some cPanel versions do this automatically
3. If not, contact your hosting provider or use:
   ```
   /scripts/restartsrv_php-fpm
   ```

### Step 5: Verify
1. Access `check_imagick_web.php` in your browser:
   ```
   https://lms.olympia-education.com/check_imagick_web.php
   ```
2. It should show:
   ```
   Extension Loaded: YES
   Can Create Imagick: YES
   ```

## Alternative: Contact Hosting Provider

If you don't have access to "Select PHP Version" or can't enable extensions:

1. Contact your hosting provider (Serverfreak support)
2. Ask them to:
   - Enable `imagick` extension for PHP 8.2
   - For domain: `lms.olympia-education.com`
   - Restart PHP-FPM after enabling

## Why This Happens

- **CLI PHP** (command line) and **Web PHP** (LiteSpeed/Apache) use **different configurations**
- CLI might have imagick enabled, but web PHP doesn't
- Both need to have imagick enabled for the system to work properly

## After Enabling Imagick

Once enabled, QR codes will generate as proper PNG files (scannable), not SVG or basic patterns.

