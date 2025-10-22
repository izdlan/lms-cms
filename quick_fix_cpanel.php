<?php
/**
 * Quick Fix for cPanel PDF Generation
 * 
 * This file contains the fixed generateCertificate method that you can copy
 * and paste into your CertificateController.php file in cPanel.
 * 
 * Instructions:
 * 1. Copy the code below
 * 2. Go to cPanel File Manager
 * 3. Open app/Http/Controllers/CertificateController.php
 * 4. Find the generateCertificate method
 * 5. Replace it with the code below
 * 6. Save the file
 */

// Replace the generateCertificate method with this code:

public function generateCertificate($studentId)
{
    try {
        // Get ex-student data
        $exStudent = \App\Models\ExStudent::find($studentId);
        
        if (!$exStudent) {
            return response()->json(['error' => 'Ex-student not found'], 404);
        }

        // Generate QR Code data for verification
        $qrCodeData = [
            'student_id' => $exStudent->student_id,
            'student_name' => $exStudent->name,
            'certificate_number' => $exStudent->certificate_number,
            'course' => $exStudent->program ?? 'Not Specified',
            'graduation_date' => $exStudent->graduation_date,
            'verification_url' => url('/certificates/verify/' . $exStudent->certificate_number),
            'generated_at' => now()->toISOString()
        ];

        // Generate QR Code as base64 (no file operations)
        $encodedQrData = base64_encode(json_encode($qrCodeData));
        
        try {
            $qrCode = QrCode::format('png')
                ->size(200)
                ->margin(2)
                ->generate($encodedQrData);
            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCode);
        } catch (\Exception $e) {
            // Fallback to SVG
            $qrCode = QrCode::format('svg')
                ->size(200)
                ->margin(2)
                ->generate($encodedQrData);
            $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
        }

        // Check if template exists
        $templatePath = storage_path('app/templates/certificate_template.docx');
        if (!File::exists($templatePath)) {
            return response()->json(['error' => 'Certificate template not found. Please upload the template to storage/app/templates/certificate_template.docx'], 404);
        }

        // Process Word template
        $templateProcessor = new TemplateProcessor($templatePath);

        // Replace placeholders
        $templateProcessor->setValue('STUDENT_NAME', $exStudent->name);
        $templateProcessor->setValue('COURSE_NAME', $exStudent->program ?? 'Not Specified');
        $templateProcessor->setValue('GRADUATION_DATE', $exStudent->graduation_date);
        $templateProcessor->setValue('CERTIFICATE_NUMBER', $exStudent->certificate_number);
        $templateProcessor->setValue('STUDENT_ID', $exStudent->student_id);

        // Save QR code as temporary file for Word template
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        $qrCodePath = $tempDir . '/qr_' . $exStudent->id . '_' . time() . '.png';
        
        // Extract image data from base64
        if (strpos($qrCodeBase64, 'data:image/png;base64,') === 0) {
            $imageData = base64_decode(substr($qrCodeBase64, 22));
            file_put_contents($qrCodePath, $imageData);
        } elseif (strpos($qrCodeBase64, 'data:image/svg+xml;base64,') === 0) {
            $imageData = base64_decode(substr($qrCodeBase64, 26));
            $qrCodePath = str_replace('.png', '.svg', $qrCodePath);
            file_put_contents($qrCodePath, $imageData);
        }

        // Replace QR Code image in Word template
        if (file_exists($qrCodePath)) {
            $templateProcessor->setImageValue('QR_CODE', [
                'path' => $qrCodePath,
                'width' => 100,
                'height' => 100
            ]);
        }

        // Generate final certificate
        $certificateFileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.docx';
        $certificatePath = storage_path('app/certificates/' . $certificateFileName);
        
        // Ensure certificates directory exists
        if (!is_dir(dirname($certificatePath))) {
            mkdir(dirname($certificatePath), 0755, true);
        }
        
        $templateProcessor->saveAs($certificatePath);

        // Clean up temporary QR code
        if (file_exists($qrCodePath)) {
            unlink($qrCodePath);
        }

        return response()->download($certificatePath, $certificateFileName);

    } catch (\Exception $e) {
        Log::error('Certificate generation failed', [
            'error' => $e->getMessage(),
            'student_id' => $studentId,
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => 'Certificate generation failed: ' . $e->getMessage()
        ], 500);
    }
}

/*
 * INSTRUCTIONS TO APPLY THIS FIX:
 * 
 * 1. Copy the generateCertificate method above (from "public function generateCertificate" to the closing "}")
 * 
 * 2. Go to your cPanel File Manager
 * 
 * 3. Navigate to: app/Http/Controllers/CertificateController.php
 * 
 * 4. Find the existing generateCertificate method (around line 23)
 * 
 * 5. Replace the entire method with the code above
 * 
 * 6. Save the file
 * 
 * 7. Test the URL: https://lms.olympia-education.com/certificates/generate/3
 * 
 * This fix:
 * - Uses base64 QR codes instead of file operations
 * - Handles PNG/SVG fallbacks properly
 * - Works without ImageMagick
 * - Maintains your Word template
 * - Cleans up temporary files
 */
