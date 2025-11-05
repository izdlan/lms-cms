# QR Code Not Appearing in Word Certificates - Troubleshooting Guide

## Problem
The QR code placeholder `$(QR_CODE)` appears as text instead of an image in downloaded Word certificates.

## Root Causes

### 1. **QR Code Generation Failed**
- PNG generation failed (GD extension not working)
- SVG fallback also failed
- No PNG file was created

### 2. **Image Placeholder Format Mismatch**
- The template has text placeholder `$(QR_CODE)` instead of an **image placeholder**
- PhpWord's `setImageValue()` requires an image placeholder, not text

### 3. **File Permissions**
- QR code file cannot be written to `storage/app/temp/`
- QR code file cannot be read by PhpWord

### 4. **PHP Extensions Missing**
- `gd` extension not enabled (required for PNG generation)
- `imagick` extension not enabled (optional, for SVG conversion)

## Solutions

### Solution 1: Fix Template Placeholder (MOST IMPORTANT)

The QR code placeholder in your Word template **MUST be an image placeholder**, not text.

**Steps:**
1. Open `certificate_template.docx` in Microsoft Word
2. **Delete** any text placeholder like `$(QR_CODE)` or `{{QR_CODE}}`
3. **Insert an image placeholder:**
   - Go to **Insert** → **Pictures** → **This Device**
   - Select any small image (or create a 1x1 pixel transparent PNG)
   - Resize it to about 2cm x 2cm where you want the QR code
   - Right-click the image → **Format Picture** → **Alt Text**
   - Set Alt Text to: `QR_CODE` (exactly this, no brackets or dollar signs)
   - Save the template

**OR use PhpWord's recommended method:**
1. Insert a shape (rectangle) where you want the QR code
2. Right-click → **Format Shape** → **Alt Text**
3. Set Alt Text to: `QR_CODE`
4. Save the template

### Solution 2: Enable PHP Extensions

**In cPanel:**
1. Go to **Select PHP Version**
2. Click **Extensions**
3. Enable:
   - ✅ `gd` (required for PNG generation)
   - ✅ `imagick` (REQUIRED for PNG QR codes - SimpleSoftwareIO QrCode library needs it)

**IMPORTANT:** The SimpleSoftwareIO QrCode library requires `imagick` extension for PNG generation. Without it, PNG generation will fail and fall back to SVG, which cannot be inserted into Word templates.

**Check if enabled:**
```php
<?php
echo extension_loaded('gd') ? 'GD: Enabled' : 'GD: Disabled';
echo extension_loaded('imagick') ? 'Imagick: Enabled' : 'Imagick: Disabled';
?>
```

**If imagick is not available:**
- Contact your hosting provider (Serverfreak) to enable the `imagick` extension
- This is a server-level configuration that cannot be changed via cPanel in some cases
- Alternative: Use a different QR code library that supports GD for PNG generation

### Solution 3: Check File Permissions

**Via SSH or cPanel File Manager:**
```bash
chmod 777 storage/app/temp
chmod 755 storage/app/templates
```

### Solution 4: Check Logs

Check `storage/logs/laravel.log` for QR code errors:

```bash
grep -i "qr code" storage/logs/laravel.log
```

Look for:
- `QR Code PNG generation failed`
- `QR Code image replacement failed`
- `QR Code not inserted - missing requirements`

## Testing

### Test QR Code Generation
Create a test file `test_qr.php`:

```php
<?php
require 'vendor/autoload.php';

use SimpleSoftwareIO\QrCode\Facades\QrCode;

$data = 'test';
try {
    $qr = QrCode::format('png')->size(200)->generate($data);
    file_put_contents('test_qr.png', $qr);
    echo "QR code generated successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

### Test Image Placeholder
1. Download a Word certificate
2. Open it in Word
3. Check if QR code appears as an image
4. If you see `$(QR_CODE)` text, the placeholder format is wrong

## Common Error Messages

### "QR Code PNG generation failed" or "You need to install the imagick extension"
- **Fix:** Enable `imagick` extension in PHP (required by SimpleSoftwareIO QrCode library for PNG)
- **Note:** `gd` alone is not sufficient - the library specifically requires `imagick` for PNG format
- **Action:** Contact Serverfreak support to enable `imagick` extension if it's not available in cPanel

### "QR Code image replacement failed - tried all placeholder formats"
- **Fix:** Change template placeholder to image placeholder (see Solution 1)

### "QR code file is too small"
- **Fix:** QR code generation is failing, check GD extension

### "QR Code not inserted - missing requirements"
- **Fix:** Check file permissions and ensure PNG file was created

## Verification Checklist

- [ ] `gd` extension is enabled in PHP
- [ ] Template has **image placeholder** with Alt Text = `QR_CODE` (not text placeholder)
- [ ] `storage/app/temp/` directory exists and is writable (777)
- [ ] QR code PNG file is created in `storage/app/temp/`
- [ ] Logs show "QR Code image inserted successfully"
- [ ] Downloaded Word certificate shows QR code as image (not text)

## Still Not Working?

1. **Check logs:** `storage/logs/laravel.log`
2. **Verify template:** Open template in Word, check placeholder format
3. **Test QR generation:** Use test script above
4. **Check permissions:** Ensure temp directory is writable
5. **Contact support:** Provide logs and template file for debugging

