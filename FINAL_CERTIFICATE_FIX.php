<?php
/**
 * FINAL FIX for Certificate Generation
 * 
 * This completely removes QR code dependency and focuses on getting
 * the certificate generation working first
 */

// Replace BOTH generateCertificate and generatePdfCertificate methods
// in app/Http/Controllers/CertificateController.php with these versions:

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

        // Replace placeholders (NO QR CODE FOR NOW)
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

        // Replace placeholders (NO QR CODE FOR NOW)
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

// Add this simple PDF conversion method
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
                return $pdfPath;
            }
        } catch (\Exception $e) {
            Log::warning('Simple PDF conversion failed: ' . $e->getMessage());
        }

        return null;

    } catch (\Exception $e) {
        Log::error('Simple Word to PDF conversion failed: ' . $e->getMessage());
        return null;
    }
}
