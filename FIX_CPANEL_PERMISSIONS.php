<?php
/**
 * FIX CPANEL PERMISSIONS ISSUE
 * 
 * The error shows permission denied on the temp directory
 * This fixes the file permissions and uses a different approach
 */

private function convertWordToPdfCpanel($wordPath)
{
    try {
        Log::info('Starting Word-to-PDF conversion with permission fix', [
            'word_path' => $wordPath
        ]);

        // Load Word file
        $phpWord = IOFactory::load($wordPath);

        // Use a different temp directory with proper permissions
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Create HTML file in a different location
        $htmlFileName = 'temp_' . time() . '_' . rand(1000, 9999) . '.html';
        $htmlPath = $tempDir . '/' . $htmlFileName;
        
        // Ensure directory is writable
        if (!is_writable($tempDir)) {
            chmod($tempDir, 0755);
        }

        $htmlWriter = new HTML($phpWord);
        $htmlWriter->save($htmlPath);

        Log::info('HTML file created with permission fix', [
            'html_path' => $htmlPath,
            'html_size' => file_exists($htmlPath) ? filesize($htmlPath) : 0,
            'is_writable' => is_writable($tempDir)
        ]);

        // Try to read the HTML content directly instead of using loadFile
        $htmlContent = file_get_contents($htmlPath);
        
        if (!$htmlContent) {
            throw new \Exception('Could not read HTML file content');
        }

        Log::info('HTML content read successfully', [
            'content_length' => strlen($htmlContent)
        ]);

        // Convert HTML content to PDF using DomPDF
        $pdf = PDF::loadHTML($htmlContent)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'isPhpEnabled' => false,
                'isJavascriptEnabled' => false
            ]);

        // Save the PDF in the same temp directory
        $pdfFileName = 'certificate_' . time() . '_' . rand(1000, 9999) . '.pdf';
        $pdfPath = $tempDir . '/' . $pdfFileName;
        
        $pdf->save($pdfPath);

        // Clean up temporary HTML
        if (file_exists($htmlPath)) {
            unlink($htmlPath);
        }

        if (file_exists($pdfPath) && filesize($pdfPath) > 5000) {
            Log::info('PDF generated successfully with permission fix', [
                'pdf_path' => $pdfPath,
                'pdf_size' => filesize($pdfPath)
            ]);
            return $pdfPath;
        } else {
            Log::error('PDF generation failed - file too small or missing', [
                'pdf_path' => $pdfPath,
                'pdf_exists' => file_exists($pdfPath),
                'pdf_size' => file_exists($pdfPath) ? filesize($pdfPath) : 0
            ]);
            return null;
        }

    } catch (\Exception $e) {
        Log::error('Word to PDF conversion failed: ' . $e->getMessage());
        return null;
    }
}

// ============================================================================
// ALTERNATIVE METHOD - Direct HTML generation without file operations
// ============================================================================

private function convertWordToPdfDirect($wordPath)
{
    try {
        Log::info('Starting direct Word-to-PDF conversion (no temp files)', [
            'word_path' => $wordPath
        ]);

        // Load Word file
        $phpWord = IOFactory::load($wordPath);

        // Generate HTML content directly in memory
        $htmlWriter = new HTML($phpWord);
        
        // Capture HTML output directly
        ob_start();
        $htmlWriter->save('php://output');
        $htmlContent = ob_get_clean();

        Log::info('HTML content generated in memory', [
            'content_length' => strlen($htmlContent)
        ]);

        // Convert HTML content to PDF using DomPDF
        $pdf = PDF::loadHTML($htmlContent)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'isPhpEnabled' => false,
                'isJavascriptEnabled' => false
            ]);

        // Generate PDF content directly
        $pdfContent = $pdf->output();
        
        if (strlen($pdfContent) > 5000) {
            Log::info('PDF generated successfully in memory', [
                'pdf_size' => strlen($pdfContent)
            ]);
            
            // Return the PDF content directly
            return $pdfContent;
        } else {
            Log::error('PDF generation failed - content too small', [
                'pdf_size' => strlen($pdfContent)
            ]);
            return null;
        }

    } catch (\Exception $e) {
        Log::error('Direct Word to PDF conversion failed: ' . $e->getMessage());
        return null;
    }
}

// ============================================================================
// UPDATE generatePdfCertificate to handle both methods
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
        
        // Ensure temp directory exists with proper permissions
        if (!is_dir(dirname($wordPath))) {
            mkdir(dirname($wordPath), 0755, true);
        }
        
        $templateProcessor->saveAs($wordPath);

        Log::info('Word document created successfully', [
            'word_path' => $wordPath,
            'word_size' => filesize($wordPath)
        ]);

        // Try method 1: File-based conversion
        $pdfPath = $this->convertWordToPdfCpanel($wordPath);

        if ($pdfPath && file_exists($pdfPath)) {
            $fileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.pdf';
            
            Log::info('PDF generated successfully using file method', [
                'pdf_path' => $pdfPath,
                'pdf_size' => filesize($pdfPath)
            ]);
            
            // Clean up temporary Word file
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            
            return response()->download($pdfPath, $fileName);
        }

        // Try method 2: Direct conversion (no temp files)
        Log::info('Trying direct conversion method');
        $pdfContent = $this->convertWordToPdfDirect($wordPath);

        if ($pdfContent) {
            $fileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.pdf';
            
            Log::info('PDF generated successfully using direct method', [
                'pdf_size' => strlen($pdfContent)
            ]);
            
            // Clean up temporary Word file
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            
            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        }

        Log::error('Both PDF conversion methods failed');
        
        // Fallback: return Word document if PDF conversion fails
        return response()->download($wordPath, $wordFileName);

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
