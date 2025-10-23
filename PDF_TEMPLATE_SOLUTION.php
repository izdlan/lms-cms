<?php
/**
 * PDF TEMPLATE SOLUTION
 * 
 * This uses a PDF template and fills it with student data
 * No Word-to-PDF conversion needed!
 */

use setasign\Fpdi\Fpdi;

private function generatePdfFromTemplate($exStudent)
{
    try {
        // Check if PDF template exists
        $templatePath = storage_path('app/templates/certificate_template.pdf');
        if (!File::exists($templatePath)) {
            throw new \Exception('PDF template not found. Please upload certificate_template.pdf to storage/app/templates/');
        }

        Log::info('Starting PDF generation from template', [
            'template_path' => $templatePath,
            'student_name' => $exStudent->name
        ]);

        // Create new PDF document
        $pdf = new Fpdi();
        $pdf->AddPage();
        
        // Import the PDF template
        $pdf->setSourceFile($templatePath);
        $tpl = $pdf->importPage(1);
        $pdf->useTemplate($tpl, 0, 0, 210); // A4 width = 210mm

        // Set font for text
        $pdf->SetFont('Helvetica', 'B', 16);
        
        // Fill in student data
        // You'll need to adjust these coordinates based on your PDF template
        $pdf->SetXY(50, 80);  // Adjust X, Y coordinates
        $pdf->Write(0, $exStudent->name);
        
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetXY(50, 100);  // Adjust coordinates
        $pdf->Write(0, $exStudent->program ?? 'Not Specified');
        
        $pdf->SetXY(50, 120);  // Adjust coordinates
        $pdf->Write(0, $exStudent->graduation_date);
        
        $pdf->SetXY(50, 140);  // Adjust coordinates
        $pdf->Write(0, $exStudent->certificate_number);
        
        $pdf->SetXY(50, 160);  // Adjust coordinates
        $pdf->Write(0, $exStudent->student_id);

        // Generate PDF content
        $pdfContent = $pdf->Output('S'); // 'S' = return as string
        
        Log::info('PDF generated successfully from template', [
            'pdf_size' => strlen($pdfContent)
        ]);

        return $pdfContent;

    } catch (\Exception $e) {
        Log::error('PDF template generation failed: ' . $e->getMessage());
        return null;
    }
}

// ============================================================================
// UPDATE generatePdfCertificate method to use PDF template
// ============================================================================

public function generatePdfCertificate($studentId)
{
    try {
        // Get ex-student data
        $exStudent = \App\Models\ExStudent::find($studentId);
        
        if (!$exStudent) {
            return response()->json(['error' => 'Ex-student not found'], 404);
        }

        Log::info('Starting PDF certificate generation for student: ' . $exStudent->name);

        // Generate PDF from template
        $pdfContent = $this->generatePdfFromTemplate($exStudent);

        if ($pdfContent) {
            $fileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.pdf';
            
            Log::info('PDF certificate generated successfully', [
                'file_name' => $fileName,
                'pdf_size' => strlen($pdfContent)
            ]);
            
            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        } else {
            Log::error('PDF generation failed from template');
            return response()->json([
                'error' => 'PDF generation failed. Please check server logs.'
            ], 500);
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
// KEEP the Word generation method for Word downloads
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
