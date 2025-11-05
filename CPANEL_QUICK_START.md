# Quick Start: Deploy Certificate Feature to Serverfreak cPanel

## Summary

This feature generates PDF and Word certificates for ex-students. It works on cPanel without LibreOffice.

## What You Need to Add

### 1. PHP Extensions (cPanel → Select PHP Version)
Enable these extensions:
- ✅ `zip`
- ✅ `gd` (or `imagick`)
- ✅ `xml`
- ✅ `mbstring`
- ✅ `dom`

### 2. Composer Packages (Already in composer.json)
Run this command:
```bash
composer install --no-dev --optimize-autoloader
```

All required packages are already listed in `composer.json`.

### 3. Template Files
Upload to: `storage/app/templates/`
- `certificate_template.docx`
- `certificate_template.pdf`

### 4. File Permissions
```bash
chmod 755 storage/app/templates
chmod 777 storage/app/temp
chmod 644 storage/app/templates/*.docx
chmod 644 storage/app/templates/*.pdf
```

### 5. PHP Settings (cPanel → Select PHP Version → Options)
- `memory_limit`: 256M (or 512M)
- `max_execution_time`: 300
- `upload_max_filesize`: 10M

### 6. Database Migration
```bash
php artisan migrate
```

## How It Works on cPanel

1. **DOCX Template**: Uses PhpWord to convert DOCX to PDF (DomPDF backend)
2. **If DOCX fails**: Automatically falls back to PDF template method
3. **No LibreOffice needed**: System works without it

## Important Notes

⚠️ **LibreOffice is NOT available on cPanel** - The system uses PhpWord PDF conversion instead

⚠️ **Borders in PDFs**: If you see box lines around signatures/QR codes, remove them from the template files manually in Microsoft Word

## Testing

1. Create/edit an ex-student with:
   - Program (Short): "Bachelor of Science"
   - Program (Full): "Bachelor of Science (Hons) Information & Communication Technology"
   - Graduation Day: 10
   - Graduation Month: "February"

2. Download Word certificate - should work
3. Download PDF certificate - should work (may use PDF template fallback)

## Troubleshooting

Check logs: `storage/logs/laravel.log`

Common issues:
- "Template not found" → Check file paths and permissions
- "Permission denied" → Check directory permissions (777 for temp)
- "QR code failed" → Check if `gd` extension is enabled

## Files to Upload

Only upload these to cPanel:
1. All code files (already in your project)
2. `certificate_template.docx` → `storage/app/templates/`
3. `certificate_template.pdf` → `storage/app/templates/`

That's it! The feature should work after these steps.

