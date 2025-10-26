# 🚀 cPanel PDF Generation Solutions
## Alternative Methods Without Composer/FPDI

---

## ❌ **Current Issue**
- **Composer not available** on cPanel shared hosting
- **FPDI package cannot be installed** via Composer
- **Need alternative PDF generation methods**

---

## ✅ **Available Solutions**

### **Solution 1: Use Existing DomPDF Method**
Your system already has DomPDF working. Let's enhance it:

```php
// In CertificateController.php - enhance the existing method
public function generatePdfCertificateCpanel($studentId)
{
    try {
        $exStudent = \App\Models\ExStudent::find($studentId);
        
        if (!$exStudent) {
            return response()->json(['error' => 'Ex-student not found'], 404);
        }

        // Generate QR Code as base64 (no file operations)
        $qrCodeData = [
            'student_id' => $exStudent->student_id,
            'student_name' => $exStudent->name,
            'certificate_number' => $exStudent->certificate_number,
            'verification_url' => url('/certificates/verify/' . $exStudent->certificate_number)
        ];

        $qrCode = QrCode::format('png')
            ->size(150)
            ->generate(json_encode($qrCodeData));

        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCode);

        // Use HTML template with DomPDF (most reliable for cPanel)
        $data = [
            'exStudent' => $exStudent,
            'qrCode' => $qrCodeBase64,
            'qrCodeData' => $qrCodeData
        ];

        $pdf = PDF::loadView('certificate.pdf-template', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Times New Roman'
        ]);

        return $pdf->download('certificate_' . $exStudent->student_id . '.pdf');

    } catch (\Exception $e) {
        Log::error('PDF Certificate generation failed: ' . $e->getMessage());
        return response()->json(['error' => 'PDF Certificate generation failed: ' . $e->getMessage()], 500);
    }
}
```

### **Solution 2: Create HTML Template for PDF**
Create a new view file for PDF generation:

**File: `resources/views/certificate/pdf-template.blade.php`**
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            background: white;
        }
        .certificate {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            position: relative;
            background: white;
            border: 3px solid #gold;
        }
        .certificate-header {
            text-align: center;
            padding: 40px 20px 20px;
            border-bottom: 2px solid #gold;
        }
        .certificate-title {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .certificate-subtitle {
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 30px;
        }
        .certificate-body {
            padding: 40px 60px;
            text-align: center;
        }
        .certificate-text {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 30px;
        }
        .student-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            text-decoration: underline;
            margin: 20px 0;
        }
        .course-info {
            font-size: 18px;
            color: #34495e;
            margin: 20px 0;
        }
        .certificate-footer {
            position: absolute;
            bottom: 40px;
            left: 0;
            right: 0;
            text-align: center;
        }
        .qr-code {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 100px;
            height: 100px;
        }
        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #2c3e50;
            margin-bottom: 10px;
            height: 40px;
        }
        .signature-text {
            font-size: 14px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-header">
            <div class="certificate-title">CERTIFICATE OF COMPLETION</div>
            <div class="certificate-subtitle">Olympia Education</div>
        </div>
        
        <div class="certificate-body">
            <div class="certificate-text">
                This is to certify that
            </div>
            
            <div class="student-name">
                {{ $exStudent->name }}
            </div>
            
            <div class="certificate-text">
                has successfully completed the course
            </div>
            
            <div class="course-info">
                <strong>{{ $exStudent->program ?? 'Executive Master of Business Administration (EMBA)' }}</strong>
            </div>
            
            <div class="certificate-text">
                on {{ \Carbon\Carbon::parse($exStudent->graduation_date)->format('F j, Y') }}
            </div>
            
            <div class="signature-section">
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-text">Director</div>
                </div>
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-text">Date</div>
                </div>
            </div>
        </div>
        
        <div class="certificate-footer">
            <div style="font-size: 12px; color: #7f8c8d;">
                Certificate Number: {{ $exStudent->certificate_number }}
            </div>
        </div>
        
        <img src="{{ $qrCode }}" alt="QR Code" class="qr-code">
    </div>
</body>
</html>
```

### **Solution 3: Manual FPDI Installation**
If you have access to your server files, you can manually install FPDI:

1. **Download FPDI manually:**
   - Go to: https://github.com/Setasign/FPDI/releases
   - Download the latest version
   - Extract the files

2. **Upload to your server:**
   ```bash
   # Upload the extracted files to:
   /home/serimala/lms.olympia-education.com/vendor/setasign/fpdi/
   ```

3. **Update composer.json manually:**
   ```json
   {
       "require": {
           "setasign/fpdi": "^2.3"
       }
   }
   ```

### **Solution 4: Use Existing Word Template Method**
Your current system already has Word template processing. Let's fix the PDF conversion:

```php
// Enhanced Word-to-PDF conversion method
private function convertWordToPdfCpanel($wordPath)
{
    try {
        $pdfPath = str_replace('.docx', '.pdf', $wordPath);

        // Method 1: Try PhpWord HTML conversion (most reliable for cPanel)
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordPath);
            $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
            $htmlContent = $htmlWriter->getContent();
            
            // Save HTML temporarily
            $htmlPath = str_replace('.docx', '.html', $wordPath);
            file_put_contents($htmlPath, $htmlContent);
            
            // Convert HTML to PDF using DomPDF
            $pdf = PDF::loadFile($htmlPath);
            $pdf->setPaper('A4', 'portrait');
            $pdf->save($pdfPath);
            
            // Clean up HTML file
            unlink($htmlPath);
            
            if (file_exists($pdfPath) && filesize($pdfPath) > 10000) {
                Log::info('PDF generated successfully using PhpWord HTML conversion');
                return $pdfPath;
            }
        } catch (\Exception $e) {
            Log::warning('PhpWord HTML conversion failed: ' . $e->getMessage());
        }

        // Method 2: Direct DomPDF from Word content
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordPath);
            $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
            $htmlContent = $htmlWriter->getContent();
            
            $pdf = PDF::loadHTML($htmlContent);
            $pdf->setPaper('A4', 'portrait');
            $pdf->save($pdfPath);
            
            if (file_exists($pdfPath) && filesize($pdfPath) > 10000) {
                Log::info('PDF generated successfully using direct DomPDF');
                return $pdfPath;
            }
        } catch (\Exception $e) {
            Log::warning('Direct DomPDF conversion failed: ' . $e->getMessage());
        }

        return false;
    } catch (\Exception $e) {
        Log::error('Word to PDF conversion failed: ' . $e->getMessage());
        return false;
    }
}
```

---

## 🎯 **Recommended Approach**

### **Immediate Solution (No Composer Required):**
1. **Use Solution 1** - Enhance existing DomPDF method
2. **Create HTML template** (Solution 2)
3. **Test the PDF generation**

### **Steps to Implement:**
1. **Create the HTML template** file
2. **Update the CertificateController** method
3. **Test PDF generation**
4. **Add images manually** to the template

---

## 🔧 **Testing Commands**

After implementing, test with:
```bash
# Test PDF generation
curl -X GET "https://lms.olympia-education.com/generate/pdf-cpanel/STUDENT_ID"
```

---

## 📝 **Next Steps**

1. **Choose your preferred solution** (I recommend Solution 1 + 2)
2. **Create the HTML template** file
3. **Update the controller method**
4. **Test the PDF generation**
5. **Add your images** to the template

Would you like me to help you implement any of these solutions?

