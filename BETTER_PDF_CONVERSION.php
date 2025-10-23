<?php
/**
 * BETTER PDF CONVERSION FOR CPANEL
 * 
 * Replace the convertWordToPdfSimple method in your CertificateController.php
 * with this improved version
 */

private function convertWordToPdfSimple($wordPath)
{
    try {
        $pdfPath = str_replace('.docx', '.pdf', $wordPath);
        
        Log::info('Starting PDF conversion', [
            'word_path' => $wordPath,
            'pdf_path' => $pdfPath,
            'word_exists' => file_exists($wordPath),
            'word_size' => file_exists($wordPath) ? filesize($wordPath) : 0
        ]);

        // Method 1: Try PhpWord PDF writer with better error handling
        try {
            Log::info('Attempting PhpWord PDF conversion');
            
            \PhpOffice\PhpWord\Settings::setPdfRendererName(\PhpOffice\PhpWord\Settings::PDF_RENDERER_DOMPDF);
            \PhpOffice\PhpWord\Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));
            
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordPath);
            $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
            $pdfWriter->save($pdfPath);
            
            if (file_exists($pdfPath) && filesize($pdfPath) > 10000) {
                Log::info('PDF generated successfully using PhpWord', [
                    'pdf_size' => filesize($pdfPath)
                ]);
                return $pdfPath;
            } else {
                Log::warning('PhpWord PDF conversion failed - file too small or missing', [
                    'pdf_exists' => file_exists($pdfPath),
                    'pdf_size' => file_exists($pdfPath) ? filesize($pdfPath) : 0
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('PhpWord PDF conversion failed: ' . $e->getMessage());
        }

        // Method 2: Try HTML conversion with better settings
        try {
            Log::info('Attempting HTML to PDF conversion');
            
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordPath);
            $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
            
            $htmlPath = str_replace('.docx', '.html', $wordPath);
            $htmlWriter->save($htmlPath);
            
            Log::info('HTML file created', [
                'html_path' => $htmlPath,
                'html_size' => file_exists($htmlPath) ? filesize($htmlPath) : 0
            ]);
            
            // Convert HTML to PDF with better settings
            $pdf = PDF::loadFile($htmlPath);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'isPhpEnabled' => false,
                'isJavascriptEnabled' => false
            ]);
            
            $pdf->save($pdfPath);
            
            // Clean up HTML file
            if (file_exists($htmlPath)) {
                unlink($htmlPath);
            }
            
            if (file_exists($pdfPath) && filesize($pdfPath) > 10000) {
                Log::info('PDF generated successfully using HTML conversion', [
                    'pdf_size' => filesize($pdfPath)
                ]);
                return $pdfPath;
            } else {
                Log::warning('HTML to PDF conversion failed - file too small or missing', [
                    'pdf_exists' => file_exists($pdfPath),
                    'pdf_size' => file_exists($pdfPath) ? filesize($pdfPath) : 0
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('HTML to PDF conversion failed: ' . $e->getMessage());
        }

        // Method 3: Try direct DomPDF with HTML content
        try {
            Log::info('Attempting direct DomPDF conversion');
            
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordPath);
            $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
            
            // Get HTML content directly
            ob_start();
            $htmlWriter->save('php://output');
            $htmlContent = ob_get_clean();
            
            Log::info('HTML content generated', [
                'html_length' => strlen($htmlContent)
            ]);
            
            // Create PDF directly from HTML content
            $pdf = PDF::loadHTML($htmlContent);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'isPhpEnabled' => false,
                'isJavascriptEnabled' => false
            ]);
            
            $pdf->save($pdfPath);
            
            if (file_exists($pdfPath) && filesize($pdfPath) > 10000) {
                Log::info('PDF generated successfully using direct DomPDF', [
                    'pdf_size' => filesize($pdfPath)
                ]);
                return $pdfPath;
            } else {
                Log::warning('Direct DomPDF conversion failed - file too small or missing', [
                    'pdf_exists' => file_exists($pdfPath),
                    'pdf_size' => file_exists($pdfPath) ? filesize($pdfPath) : 0
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Direct DomPDF conversion failed: ' . $e->getMessage());
        }

        Log::error('All PDF conversion methods failed');
        return null;

    } catch (\Exception $e) {
        Log::error('Word to PDF conversion failed: ' . $e->getMessage());
        return null;
    }
}

// ============================================================================
// ALSO UPDATE the generatePdfCertificate method to handle failures better:
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

        Log::info('Word document created successfully', [
            'word_path' => $wordPath,
            'word_size' => filesize($wordPath)
        ]);

        // Convert Word to PDF using improved method
        $pdfPath = $this->convertWordToPdfSimple($wordPath);

        if ($pdfPath && file_exists($pdfPath) && filesize($pdfPath) > 10000) {
            $fileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.pdf';
            
            Log::info('PDF generated successfully', [
                'pdf_path' => $pdfPath,
                'pdf_size' => filesize($pdfPath)
            ]);
            
            // Clean up temporary Word file
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            
            return response()->download($pdfPath, $fileName);
        } else {
            Log::warning('PDF conversion failed, returning Word document as fallback');
            
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
