# How to Run check_imagick.php in cPanel

## Method 1: Via Browser (Easiest) ✅

### Step 1: Upload the File
1. Log in to **cPanel**
2. Open **File Manager**
3. Navigate to your website root:
   - Usually: `public_html`
   - Or: `public_html/lms-cms` (if your Laravel app is in a subdirectory)
4. Click **Upload** button (top menu)
5. Select `check_imagick.php` from your computer
6. Wait for upload to complete

### Step 2: Access via Browser
1. Open your web browser
2. Go to one of these URLs:
   ```
   http://lms.olympia-education.com/check_imagick.php
   ```
   OR if your Laravel app is in a subdirectory:
   ```
   http://lms.olympia-education.com/lms-cms/check_imagick.php
   ```
3. You should see the diagnostic output

### Step 3: Check the Results
- ✅ **All green checkmarks** = Imagick is working!
- ❌ **Any red X** = Imagick needs to be fixed (follow the instructions shown)

---

## Method 2: Via cPanel Terminal/SSH (Advanced)

### Step 1: Upload the File
Same as Method 1, Step 1

### Step 2: Open Terminal
1. In cPanel, find **Terminal** or **SSH Access**
2. Click to open the terminal

### Step 3: Navigate and Run
```bash
cd public_html/lms-cms  # or wherever your Laravel app is
php check_imagick.php
```

### Step 4: View Results
The output will show in the terminal window

---

## Method 3: Via Laravel Artisan (If Terminal Available)

If you have SSH access, you can also run:
```bash
cd /home/serimala/lms.olympia-education.com  # Your actual path
php check_imagick.php
```

---

## What to Look For

### ✅ Success Output:
```
=== Imagick Extension Check ===

1. Extension Loaded: ✅ YES
2. Imagick Class Available: ✅ YES
3. Can Instantiate Imagick: ✅ YES
   Version: ImageMagick 7.x.x
4. Can Create PNG Image: ✅ YES

✅ SUCCESS: Imagick is fully functional!
```

### ❌ Failure Output:
```
=== Imagick Extension Check ===

1. Extension Loaded: ❌ NO

❌ ERROR: Imagick extension is NOT loaded!
```

---

## After Running the Script

### If Imagick is Working:
1. ✅ Your QR codes will be real and scannable
2. Regenerate a certificate to test
3. Check logs for: `"QR Code generated successfully as PNG"`

### If Imagick is NOT Working:
1. Follow the instructions shown in the script output
2. Go to cPanel → Select PHP Version → Use PHP Selector
3. Enable imagick extension
4. Run the script again to verify

---

## Troubleshooting

### "File not found" error
- Make sure you uploaded the file to the correct directory
- Check the file path matches your Laravel app location

### "Permission denied" error
- Check file permissions (should be 644)
- Make sure PHP can read the file

### "Cannot access" error
- Make sure the file is in a web-accessible directory
- Check if your domain is pointing to the correct folder

---

## Quick Access

Once uploaded, bookmark this URL for easy access:
```
http://lms.olympia-education.com/check_imagick.php
```

