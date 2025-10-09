# 🎓 Certificate Generation System Guide

## Overview
This system allows you to generate professional Word documents and PDF certificates for ex-students, exactly matching the template you provided. The system includes:

- **Dynamic placeholders** (red numbers 1-8 from your image)
- **QR code integration** for verification
- **Both physical and online certificates**
- **Professional formatting** matching your template

## 🎯 Template Mapping

Based on your certificate image, here's how the placeholders work:

| Red Number | Field | Description |
|------------|-------|-------------|
| 1 | Student Name | `{{ $exStudent->name }}` |
| 2 | Student ID | `{{ $exStudent->student_id }}` |
| 3 | Degree (Malay) | `{{ $exStudent->program }}` |
| 4 | Degree (English) | Bachelor Executive in Business Administration |
| 5 | Graduation Date | `{{ $exStudent->getFormattedGraduationDate() }}` |
| 6 | QR Code 1 | Verification QR code |
| 7 | QR Code 2 | Certificate QR code |
| 8 | Certificate Number | `{{ $exStudent->certificate_number }}` |

## 🚀 How to Use

### 1. Generate Sample Certificate
```bash
# Visit this URL to download a sample certificate
http://your-domain.com/certificates/template
```

### 2. Generate Certificate for Ex-Student
```bash
# Via API
POST /certificates/generate/word
{
    "student_id": "SAMPLE001"
}

# Via URL
GET /ex-student/certificate-preview?student_id=SAMPLE001
```

### 3. Available Routes

| Route | Method | Description |
|-------|--------|-------------|
| `/certificates/template` | GET | Download sample certificate |
| `/certificates/generate/word` | POST | Generate Word certificate |
| `/certificates/generate/pdf` | POST | Generate PDF certificate |
| `/certificates/preview` | GET | Preview certificate |
| `/ex-student/certificate-preview` | GET | Ex-student preview page |

## 📁 File Structure

```
app/
├── Services/
│   ├── CertificateService.php      # Main certificate generation
│   └── QrCodeService.php          # QR code generation (existing)
├── Http/Controllers/
│   └── CertificateController.php   # API endpoints
└── Models/
    └── ExStudent.php              # Student data model

resources/views/
└── ex-student/
    └── certificate-preview.blade.php  # Preview page

storage/app/public/certificates/    # Generated certificates
```

## 🎨 Certificate Features

### Header Section
- ✅ University logo (if available)
- ✅ "OLYMPIA UNIVERSITY" title
- ✅ "OLYMPIA EDUCATION" subtitle

### Body Section
- ✅ Malay declaration text
- ✅ English declaration text
- ✅ Student name (placeholder 1)
- ✅ Student ID (placeholder 2)
- ✅ Degree in Malay (placeholder 3)
- ✅ Degree in English (placeholder 4)
- ✅ Graduation date (placeholder 5)

### Footer Section
- ✅ Director signature
- ✅ Chairman signature
- ✅ QR Code 1 (placeholder 6)
- ✅ QR Code 2 (placeholder 7)
- ✅ Certificate number (placeholder 8)
- ✅ Accreditation logos (MQA, CMI, CTH, Ministry)

## 🔧 Customization

### 1. Modify Certificate Template
Edit `app/Services/CertificateService.php`:

```php
// Change degree program
$degreeText = $exStudent->program ?? 'YOUR_DEGREE_PROGRAM';

// Modify graduation date format
private function formatGraduationDate(ExStudent $exStudent): string
{
    // Your custom date formatting
}
```

### 2. Add Custom Fields
Add new fields to the ExStudent model:

```php
// In database migration
$table->string('custom_field')->nullable();

// In certificate service
$section->addText($exStudent->custom_field, [
    'name' => 'Times New Roman',
    'size' => 14,
    'bold' => true,
]);
```

### 3. Change Logo/Images
Update paths in `CertificateService.php`:

```php
$logoPath = public_path('store/1/logo/YOUR_LOGO.png');
```

## 📱 QR Code Integration

The system generates two types of QR codes:

1. **Verification QR Code**: Links to student verification page
2. **Certificate QR Code**: Contains student data in JSON format

### QR Code Data Format
```json
{
    "student_id": "SAMPLE001",
    "certificate_number": "CERT-20250829-0001",
    "name": "ASMAWI BIN ASA",
    "program": "SARJANA MUDA EKSEKUTIF PENTADBIRAN PERNIAGAAN",
    "graduation_date": "29th of August 2025"
}
```

## 🖨️ Physical vs Online Certificates

### Physical Certificates
- Generated as Word documents (.docx)
- High-quality formatting for printing
- QR codes for verification
- Professional layout

### Online Certificates
- Web-based preview
- Downloadable in multiple formats
- Mobile-responsive design
- Real-time verification

## 🧪 Testing

### 1. Test Certificate Generation
```bash
php test_certificate_generation.php
```

### 2. Test with Real Data
```php
$exStudent = ExStudent::findByStudentId('REAL_STUDENT_ID');
$certificateService = new CertificateService(new QrCodeService());
$filename = $certificateService->generateWordCertificate($exStudent);
```

### 3. Test QR Codes
```php
$qrCodeService = new QrCodeService();
$qrPath = $qrCodeService->generateCertificateQrCode($exStudent);
```

## 🔒 Security Features

- ✅ Student ID validation
- ✅ Certificate number generation
- ✅ QR code verification
- ✅ Access logging
- ✅ File cleanup after download

## 📊 Bulk Operations

Generate multiple certificates at once:

```bash
POST /certificates/bulk-generate
{
    "student_ids": ["S001", "S002", "S003"],
    "format": "word"
}
```

## 🎯 Next Steps

1. **Test the system**: Visit `/certificates/template` to download a sample
2. **Customize the template**: Modify `CertificateService.php` as needed
3. **Add your logos**: Place logo files in `public/store/1/logo/`
4. **Test with real data**: Use actual ex-student records
5. **Deploy to production**: Ensure all dependencies are installed

## 🆘 Troubleshooting

### Common Issues

1. **PHPWord not found**: Run `composer install`
2. **QR codes not generating**: Check SimpleSoftwareIO\QrCode package
3. **File permissions**: Ensure `storage/app/public/certificates/` is writable
4. **Memory issues**: Increase PHP memory limit for large documents

### Debug Mode
Enable Laravel debug mode to see detailed error messages:

```php
// In .env
APP_DEBUG=true
LOG_LEVEL=debug
```

## 📞 Support

If you need help customizing the certificate template or adding new features, the system is designed to be easily extensible. All the placeholder logic is in `CertificateService.php` and can be modified to match your exact requirements.

---

**🎉 Your certificate generation system is now ready to use!**
