# ConvertAPI Integration Guide

## Overview
ConvertAPI has been integrated as the primary method for converting DOCX certificates to PDF. This is ideal for cPanel/shared hosting environments where LibreOffice is not available.

## What Was Implemented

### 1. Service Class Created
- **File**: `app/Services/CertificateConverter.php`
- **Method**: `docxToPdf($inputPath, $outputPath = null)`
- Converts DOCX files to PDF using ConvertAPI's online service

### 2. Configuration Updated
- **File**: `config/services.php`
- Added ConvertAPI configuration section

### 3. Controller Updated
- **File**: `app/Http/Controllers/CertificateController.php`
- ConvertAPI is now the **first method** tried for PDF conversion
- Falls back to LibreOffice if ConvertAPI fails or is not configured
- Falls back to PhpWord/DomPDF if both fail

## Setup Instructions

### Step 1: Get Your ConvertAPI Secret Key

1. Go to https://www.convertapi.com/a/signup
2. Create a free account
3. Navigate to your dashboard
4. Copy your API secret key (looks like: `6b5b83xxxxxxxxxxxxxx`)

### Step 2: Add to .env File

Add this line to your `.env` file:

```env
CONVERT_API_SECRET=your_api_secret_here
```

Replace `your_api_secret_here` with your actual ConvertAPI API secret key (Bearer token).

**Note:** The implementation uses Bearer token authentication in the header, which is more secure than query parameters.

### Step 3: Clear Config Cache (if needed)

```bash
php artisan config:clear
```

## How It Works

### Conversion Priority Order:

1. **ConvertAPI** (if `CONVERT_API_SECRET` is configured)
   - Uploads DOCX to ConvertAPI
   - Downloads converted PDF
   - Returns PDF path

2. **LibreOffice** (if ConvertAPI fails or not configured)
   - Uses system `soffice` command
   - Only works if LibreOffice is installed

3. **PhpWord/DomPDF** (final fallback)
   - Uses PhpWord's internal PDF converter
   - May have formatting issues with complex templates

## ConvertAPI Pricing

### Free Tier
- **1500 conversion seconds per month**
- Each conversion takes ~1-2 seconds
- **Approx. 700-1000 certificates per month**

### Paid Plans
- Start around **$4.50/month** for heavier use
- More conversion seconds available

## Benefits

✅ **Works on cPanel/shared hosting** (no system binaries needed)  
✅ **More reliable than PhpWord's DomPDF**  
✅ **Preserves exact formatting** from your DOCX template  
✅ **No border issues** (uses Word template as-is)  
✅ **Automatic fallback** to LibreOffice if ConvertAPI fails  

## Testing

After adding your API key:

1. Generate a certificate PDF
2. Check Laravel logs for:
   - `"PDF generated successfully using ConvertAPI"` (success)
   - `"ConvertAPI conversion failed"` (failure, will try fallback)

## Troubleshooting

### ConvertAPI Not Working?

1. **Check API key**: Verify `CONVERT_API_SECRET` is set in `.env`
2. **Check logs**: Look for ConvertAPI errors in `storage/logs/laravel.log`
3. **Check quota**: Verify you haven't exceeded free tier limits
4. **Test manually**: Try uploading a DOCX to ConvertAPI website

### Still Getting Word Files Instead of PDF?

- Check if ConvertAPI secret is configured
- Check logs for ConvertAPI errors
- System will automatically fall back to LibreOffice/PhpWord
- If all methods fail, Word file is returned as fallback

## Logs to Watch

Look for these log messages:
- `"Attempting PDF conversion using ConvertAPI"` - ConvertAPI is being tried
- `"PDF generated successfully using ConvertAPI"` - Success!
- `"ConvertAPI conversion failed"` - Will try LibreOffice next
- `"PDF generated successfully using DOCX template with LibreOffice"` - LibreOffice fallback worked
- `"PhpWord PDF conversion failed"` - Final fallback failed

## Notes

- ConvertAPI requires an active internet connection
- Files are temporarily uploaded to ConvertAPI servers (process is secure)
- The free tier should be sufficient for most testing and light production use
- If you exceed free tier, consider upgrading or using LibreOffice fallback

