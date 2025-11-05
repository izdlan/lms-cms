# Certificate Generation - cPanel Deployment Guide

This guide explains how to deploy the certificate generation feature to a Serverfreak cPanel hosting environment.

## Prerequisites

### 1. PHP Extensions Required

Ensure these PHP extensions are enabled in cPanel (usually available by default):

- `zip` - For DOCX file handling (ZIP archives)
- `gd` or `imagick` - For QR code image generation
- `xml` - For XML processing (DOCX files)
- `mbstring` - For string handling
- `dom` - For DOMDocument operations

**How to check/enable in cPanel:**
1. Go to **cPanel → Select PHP Version** (or **MultiPHP INI Editor**)
2. Ensure these extensions are checked/enabled
3. If not available, contact Serverfreak support to enable them

### 2. Composer Dependencies

All required packages should already be in `composer.json`. Run this command in cPanel terminal or via SSH:

```bash
cd /home/username/public_html/lms-cms  # Replace with your actual path
composer install --no-dev --optimize-autoloader
```

**Required packages:**
- `phpoffice/phpword` - For Word template processing
- `setasign/fpdi` - For PDF template manipulation
- `tecnickcom/tcpdf` - For PDF generation
- `simplesoftwareio/simple-qrcode` - For QR code generation
- `barryvdh/laravel-dompdf` - For HTML to PDF conversion (fallback)

### 3. File Permissions

Set proper permissions for directories (via cPanel File Manager or SSH):

```bash
# Storage directories
chmod 755 storage/app/templates
chmod 755 storage/app/temp
chmod 755 storage/app/certificates
chmod 755 storage/app/public

# Make sure temp directory is writable
chmod 777 storage/app/temp  # Temporary files need write access
```

### 4. Template Files

Upload your certificate templates to:
```
storage/app/templates/certificate_template.docx
storage/app/templates/certificate_template.pdf
```

**Via cPanel File Manager:**
1. Navigate to `storage/app/templates/`
2. Upload `certificate_template.docx` and `certificate_template.pdf`
3. Set file permissions to 644 (readable)

### 5. Important: LibreOffice Not Available on cPanel

**LibreOffice is NOT available on shared hosting cPanel servers.** The system will automatically use fallback methods:

1. **Primary Method (DOCX):** Uses PhpWord's PDF writer (DomPDF backend)
2. **Fallback Method (PDF):** Uses FPDI + TCPDF to overlay content on PDF template

## Configuration Changes for cPanel

### Update `.env` File

No special configuration needed. The system automatically detects available methods.

### Database Migration

Ensure the database migration has been run:
```bash
php artisan migrate
```

This adds the required fields:
- `program_short` - Short program name (e.g., "Bachelor of Science")
- `program_full` - Full program name (e.g., "Bachelor of Science (Hons) Information & Communication Technology")
- `graduation_day` - Day of month (1-31)

### PHP Configuration Settings

In cPanel → **Select PHP Version** → **Options** (or **MultiPHP INI Editor**):

Set these values:
- `memory_limit` = **256M** (minimum, 512M recommended)
- `max_execution_time` = **300** (5 minutes for PDF generation)
- `upload_max_filesize` = **10M** (for template uploads)
- `post_max_size` = **10M**

### Create Required Directories

Via cPanel File Manager or SSH:
```bash
mkdir -p storage/app/templates
mkdir -p storage/app/temp
mkdir -p storage/app/certificates
```

Set permissions:
```bash
chmod 755 storage/app/templates
chmod 777 storage/app/temp  # Needs write access for temporary files
chmod 755 storage/app/certificates
```

## Testing the Deployment

### 1. Test Template Upload

Verify templates are accessible:
- Check file permissions (should be 644)
- Verify files exist in `storage/app/templates/`

### 2. Test Certificate Generation

1. Log in to admin panel
2. Navigate to ex-student management
3. Create or edit a student record with:
   - Program (Short): e.g., "Bachelor of Science"
   - Program (Full): e.g., "Bachelor of Science (Hons) Information & Communication Technology"
   - Graduation Day: e.g., 10
   - Graduation Month: e.g., "February"
4. Try downloading Word certificate
5. Try downloading PDF certificate

### 3. Check Logs

Monitor logs for errors:
```bash
tail -f storage/logs/laravel.log
```

Common issues:
- **"Certificate template not found"** → Check template file paths
- **"Permission denied"** → Check directory permissions (755 for dirs, 644 for files)
- **"PhpWord PDF conversion failed"** → This is normal for special characters, system will use PDF template fallback
- **"QR code generation failed"** → Check if `gd` or `imagick` extension is enabled

## Troubleshooting

### Issue: PDF Generation Fails

**Solution:** The system will automatically fall back to PDF template method if DOCX conversion fails. This is expected behavior on cPanel.

### Issue: "Permission denied" errors

**Solution:**
```bash
chmod 755 storage/app/templates
chmod 777 storage/app/temp
chmod 755 storage/app/certificates
```

### Issue: QR Code Not Generating

**Solution:**
1. Check if `gd` extension is enabled in PHP
2. Check if `imagick` extension is enabled (alternative)
3. Check file permissions on `storage/app/temp/`

### Issue: Templates Not Found

**Solution:**
1. Verify template files are in `storage/app/templates/`
2. Check file names are exactly:
   - `certificate_template.docx`
   - `certificate_template.pdf`
3. Check file permissions (644)

## Production Checklist

- [ ] All PHP extensions enabled
- [ ] Composer dependencies installed
- [ ] Template files uploaded to `storage/app/templates/`
- [ ] Directory permissions set (755 for dirs, 777 for temp)
- [ ] Database migration run
- [ ] Test Word certificate download
- [ ] Test PDF certificate download
- [ ] Check logs for errors
- [ ] Verify QR codes are generating

## Notes

1. **LibreOffice is NOT available** on cPanel shared hosting - the system uses PhpWord PDF conversion or PDF template fallback
2. **Border removal** - If borders appear in PDFs, they must be removed from the template files manually (see main documentation)
3. **Performance** - PDF generation may be slightly slower on shared hosting due to resource limitations
4. **Memory** - Ensure PHP memory limit is at least 256MB (check in cPanel → Select PHP Version)

## Support

If issues persist:
1. Check `storage/logs/laravel.log` for detailed error messages
2. Verify all file permissions are correct
3. Ensure all Composer packages are installed
4. Contact Serverfreak support if PHP extensions need to be enabled

