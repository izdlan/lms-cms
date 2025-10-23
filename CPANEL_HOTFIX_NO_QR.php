<?php
/**
 * CPANEL HOTFIX - Remove QR Code Generation
 * 
 * Replace the generateCertificate and generatePdfCertificate methods
 * in your cPanel CertificateController.php with these versions
 */

// ============================================================================
// REPLACE generateCertificate method with this:
// ============================================================================

public function generateCertificate($studentId)
{
    try {
        // Get ex-student data
        $exStudent = \App\Models\ExStudent::find($studentId);
        
        if (!$exStudent) {
            return response()->json(['error' => 'Ex-student not found'], 404);
        }

        // Check if template exists
        $templatePath = storage_path('app/templates/certificate_template.docx');
        if (!File::exists($templatePath)) {
            return response()->json(['error' => 'Certificate template not found. Please upload the template to storage/app/templates/certificate_template.docx'], 404);
        }

        // Process Word template
        $templateProcessor = new TemplateProcessor($templatePath);

        // Replace placeholders (NO QR CODE)
        $templateProcessor->setValue('STUDENT_NAME', $exStudent->name);
        $templateProcessor->setValue('COURSE_NAME', $exStudent->program ?? 'Not Specified');
        $templateProcessor->setValue('GRADUATION_DATE', $exStudent->graduation_date);
        $templateProcessor->setValue('CERTIFICATE_NUMBER', $exStudent->certificate_number);
        $templateProcessor->setValue('STUDENT_ID', $exStudent->student_id);

        // Generate final certificate
        $certificateFileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.docx';
        $certificatePath = storage_path('app/certificates/' . $certificateFileName);
        
        // Ensure certificates directory exists
        if (!is_dir(dirname($certificatePath))) {
            mkdir(dirname($certificatePath), 0755, true);
        }
        
        $templateProcessor->saveAs($certificatePath);

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

// ============================================================================
// REPLACE generatePdfCertificate method with this:
// ============================================================================

public function generatePdfCertificate($studentId)
{
    try {
        // Get ex-student data
        $exStudent = \App\Models\ExStudent::find($studentId);
        
        if (!$exStudent) {
            return response()->json(['error' => 'Ex-student not found'], 404);
        }

        // Check if template exists
        $templatePath = storage_path('app/templates/certificate_template.docx');
        if (!File::exists($templatePath)) {
            return response()->json(['error' => 'Certificate template not found. Please upload the template to storage/app/templates/certificate_template.docx'], 404);
        }

        // Process Word template
        $templateProcessor = new TemplateProcessor($templatePath);

        // Replace placeholders (NO QR CODE)
        $templateProcessor->setValue('STUDENT_NAME', $exStudent->name);
        $templateProcessor->setValue('COURSE_NAME', $exStudent->program ?? 'Not Specified');
        $templateProcessor->setValue('GRADUATION_DATE', $exStudent->graduation_date);
        $templateProcessor->setValue('CERTIFICATE_NUMBER', $exStudent->certificate_number);
        $templateProcessor->setValue('STUDENT_ID', $exStudent->student_id);

        // Generate Word document
        $wordFileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.docx';
        $wordPath = storage_path('app/temp/' . $wordFileName);
        
        // Ensure temp directory exists
        if (!is_dir(dirname($wordPath))) {
            mkdir(dirname($wordPath), 0755, true);
        }
        
        $templateProcessor->saveAs($wordPath);

        // Convert Word to PDF using simple method
        $pdfPath = $this->convertWordToPdfSimple($wordPath);

        if ($pdfPath && file_exists($pdfPath)) {
            $fileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.pdf';
            
            // Clean up temporary Word file
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            
            return response()->download($pdfPath, $fileName);
        } else {
            // Fallback: return Word document if PDF conversion fails
            return response()->download($wordPath, $wordFileName);
        }

    } catch (\Exception $e) {
        Log::error('PDF Certificate generation failed', [
            'error' => $e->getMessage(),
            'student_id' => $studentId,
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => 'PDF Certificate generation failed: ' . $e->getMessage()
        ], 500);
    }
}

// ============================================================================
// ADD this new method to your CertificateController class:
// ============================================================================

private function convertWordToPdfSimple($wordPath)
{
    try {
        $pdfPath = str_replace('.docx', '.pdf', $wordPath);

        // Try PhpWord PDF writer
        try {
            \PhpOffice\PhpWord\Settings::setPdfRendererName(\PhpOffice\PhpWord\Settings::PDF_RENDERER_DOMPDF);
            \PhpOffice\PhpWord\Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));
            
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordPath);
            $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
            $pdfWriter->save($pdfPath);
            
            if (file_exists($pdfPath) && filesize($pdfPath) > 10000) {
                Log::info('PDF generated successfully using PhpWord');
                return $pdfPath;
            }
        } catch (\Exception $e) {
            Log::warning('PhpWord PDF conversion failed: ' . $e->getMessage());
        }

        // Try HTML conversion as fallback
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordPath);
            $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
            
            $htmlPath = str_replace('.docx', '.html', $wordPath);
            $htmlWriter->save($htmlPath);
            
            // Convert HTML to PDF
            $pdf = PDF::loadFile($htmlPath);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans'
            ]);
            
            $pdf->save($pdfPath);
            
            // Clean up HTML file
            if (file_exists($htmlPath)) {
                unlink($htmlPath);
            }
            
            if (file_exists($pdfPath) && filesize($pdfPath) > 10000) {
                Log::info('PDF generated successfully using HTML conversion');
                return $pdfPath;
            }
        } catch (\Exception $e) {
            Log::warning('HTML to PDF conversion failed: ' . $e->getMessage());
        }

        Log::error('All PDF conversion methods failed');
        return null;

    } catch (\Exception $e) {
        Log::error('Word to PDF conversion failed: ' . $e->getMessage());
        return null;
    }
}

// ============================================================================
// INSTRUCTIONS:
// ============================================================================

/*
1. Copy the generateCertificate method above and replace your current one
2. Copy the generatePdfCertificate method above and replace your current one  
3. Add the convertWordToPdfSimple method to your class
4. Upload the modified CertificateController.php to cPanel
5. Test the URLs:
   - Word: https://lms.olympia-education.com/certificates/generate/3
   - PDF: https://lms.olympia-education.com/certificates/generate/pdf/3

This will work because:
- No QR code generation (eliminates SVG error)
- Simple PDF conversion using PhpWord + DomPDF
- Focuses on getting certificates working first
- Can add QR codes back later once basic functionality works
*/
