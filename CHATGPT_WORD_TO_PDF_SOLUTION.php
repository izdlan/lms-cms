<?php
/**
 * CHATGPT'S WORD-TO-PDF SOLUTION
 * 
 * This is the clean, production-ready approach that ChatGPT suggested
 * Replace your convertWordToPdfSimple method with this version
 */

private function convertWordToPdfCpanel($wordPath)
{
    try {
        Log::info('Starting Word-to-PDF conversion using ChatGPT method', [
            'word_path' => $wordPath
        ]);

        // Load Word file
        $phpWord = IOFactory::load($wordPath);

        // Convert to temporary HTML file
        $htmlPath = str_replace('.docx', '.html', $wordPath);
        $htmlWriter = new HTML($phpWord);
        $htmlWriter->save($htmlPath);

        Log::info('HTML file created', [
            'html_path' => $htmlPath,
            'html_size' => file_exists($htmlPath) ? filesize($htmlPath) : 0
        ]);

        // Convert the HTML to PDF using DomPDF
        $pdf = PDF::loadFile($htmlPath)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans'
            ]);

        // Save the PDF
        $pdfPath = str_replace('.docx', '.pdf', $wordPath);
        $pdf->save($pdfPath);

        // Clean up temporary HTML
        if (file_exists($htmlPath)) {
            unlink($htmlPath);
        }

        if (file_exists($pdfPath) && filesize($pdfPath) > 5000) {
            Log::info('PDF generated successfully using ChatGPT method', [
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
// UPDATE your generatePdfCertificate method to use this:
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

        // Convert Word to PDF using ChatGPT's method
        $pdfPath = $this->convertWordToPdfCpanel($wordPath);

        if ($pdfPath && file_exists($pdfPath)) {
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
            Log::error('PDF conversion failed using ChatGPT method');
            
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
