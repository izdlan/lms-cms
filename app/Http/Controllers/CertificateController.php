<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\HTML;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use PhpOffice\PhpWord\Settings;
use App\Services\CustomTemplateProcessor;

class CertificateController extends Controller
{
    /**
     * Generate certificate for ex-student
     */
    public function generateCertificate($studentId)
    {
        try {
            // Get ex-student data
            $exStudent = \App\Models\ExStudent::find($studentId);
            
            if (!$exStudent) {
                return response()->json(['error' => 'Ex-student not found'], 404);
            }

            // Generate QR Code data for verification (compatible with verification system)
            $qrCodeData = [
                'student_id' => $exStudent->student_id,
                'student_name' => $exStudent->name,
                'certificate_number' => $exStudent->certificate_number,
                'course' => $exStudent->program ?? 'Not Specified',
                'graduation_date' => $exStudent->graduation_date,
                'verification_url' => url('/certificates/verify/' . $exStudent->certificate_number),
                'certificate_download_url' => url('/certificates/generate/' . $exStudent->id),
                'generated_at' => now()->toISOString()
            ];

            // Generate QR Code (try PNG first, fallback to SVG)
            // Encode data as base64 JSON for verification system compatibility
            $encodedQrData = base64_encode(json_encode($qrCodeData));
            
            try {
                $qrCode = QrCode::format('png')
                    ->size(600)
                    ->margin(12)
                    ->generate($encodedQrData);
            } catch (\Exception $e) {
                // Fallback to SVG if PNG fails (no ImageMagick)
                $qrCode = QrCode::format('svg')
                    ->size(600)
                    ->margin(12)
                    ->generate($encodedQrData);
            }

            // Save QR Code temporarily
            $qrCodeExtension = strpos($qrCode, '<svg') !== false ? 'svg' : 'png';
            $qrCodePath = 'temp/qr_' . $exStudent->id . '_' . time() . '.' . $qrCodeExtension;
            Storage::disk('public')->put($qrCodePath, $qrCode);

            // Check if template exists (use original template)
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

            // Replace QR Code image
            $templateProcessor->setImageValue('QR_CODE', [
                'path' => storage_path('app/public/' . $qrCodePath),
                'width' => 100,
                'height' => 100
            ]);

            // Generate final certificate
            $certificateFileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.docx';
            $certificatePath = 'certificates/' . $certificateFileName;
            $fullCertificatePath = storage_path('app/public/' . $certificatePath);
            
            // Ensure certificates directory exists
            if (!File::exists(dirname($fullCertificatePath))) {
                File::makeDirectory(dirname($fullCertificatePath), 0755, true);
            }

            $templateProcessor->saveAs($fullCertificatePath);

            // Clean up temporary QR code
            Storage::disk('public')->delete($qrCodePath);

            return response()->download($fullCertificatePath, $certificateFileName);

        } catch (\Exception $e) {
            Log::error('Certificate generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Certificate generation failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Verify certificate
     */
    public function verifyCertificate($certificateNumber)
    {
        $exStudent = \App\Models\ExStudent::where('certificate_number', $certificateNumber)->first();
            
            if (!$exStudent) {
            return view('certificate.invalid', ['certificateNumber' => $certificateNumber]);
        }

        return view('certificate.verify', ['student' => $exStudent]);
    }

    /**
     * List all certificates
     */
    public function index()
    {
        $exStudents = \App\Models\ExStudent::orderBy('created_at', 'desc')->paginate(20);

        return view('certificate.index', ['students' => $exStudents]);
    }

    /**
     * Download certificate by ID
     */
    public function download($studentId)
    {
        return $this->generateCertificate($studentId);
    }

    /**
     * Generate PDF certificate for ex-student using Word template
     */
    public function generatePdfCertificate($studentId)
    {
        try {
            // Get ex-student data
            $exStudent = \App\Models\ExStudent::find($studentId);
            
            if (!$exStudent) {
                return response()->json(['error' => 'Ex-student not found'], 404);
            }

            // Generate QR Code data for verification (compatible with verification system)
            $qrCodeData = [
                'student_id' => $exStudent->student_id,
                'student_name' => $exStudent->name,
                'certificate_number' => $exStudent->certificate_number,
                'course' => $exStudent->program ?? 'Not Specified',
                'graduation_date' => $exStudent->graduation_date,
                'verification_url' => url('/certificates/verify/' . $exStudent->certificate_number),
                'certificate_download_url' => url('/certificates/generate/' . $exStudent->id),
                'generated_at' => now()->toISOString()
            ];

            // Generate QR Code (try PNG first, fallback to SVG)
            $encodedQrData = base64_encode(json_encode($qrCodeData));
            
            try {
                $qrCode = QrCode::format('png')
                    ->size(600)
                    ->margin(12)
                    ->generate($encodedQrData);
            } catch (\Exception $e) {
                // Fallback to SVG if PNG fails (no ImageMagick)
                $qrCode = QrCode::format('svg')
                    ->size(600)
                    ->margin(12)
                    ->generate($encodedQrData);
            }

            // Save QR Code temporarily
            $qrCodeExtension = strpos($qrCode, '<svg') !== false ? 'svg' : 'png';
            $qrCodePath = 'temp/qr_' . $exStudent->id . '_' . time() . '.' . $qrCodeExtension;
            Storage::disk('public')->put($qrCodePath, $qrCode);

            // Check if template exists (use original template)
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

            // Replace QR Code image
            $templateProcessor->setImageValue('QR_CODE', [
                'path' => storage_path('app/public/' . $qrCodePath),
                'width' => 100,
                'height' => 100
            ]);

            // Generate Word certificate first
            $certificateFileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.docx';
            $certificatePath = 'certificates/' . $certificateFileName;
            $fullCertificatePath = storage_path('app/public/' . $certificatePath);
            
            // Ensure certificates directory exists
            if (!File::exists(dirname($fullCertificatePath))) {
                File::makeDirectory(dirname($fullCertificatePath), 0755, true);
            }

            $templateProcessor->saveAs($fullCertificatePath);

            // Convert Word document to PDF using multiple methods
            $pdfFileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.pdf';
            $pdfPath = 'certificates/' . $pdfFileName;
            $fullPdfPath = storage_path('app/public/' . $pdfPath);
            
            // Method 1: Try LibreOffice conversion with batch file approach (most reliable for web)
            $batchFile = storage_path('app/temp/convert_' . time() . '.bat');
            $batchContent = '@echo off' . PHP_EOL;
            $batchContent .= 'cd /d "' . dirname($fullPdfPath) . '"' . PHP_EOL;
            $batchContent .= '"C:\Program Files\LibreOffice\program\soffice.exe" --headless --convert-to pdf --outdir "' . dirname($fullPdfPath) . '" "' . $fullCertificatePath . '"' . PHP_EOL;
            $batchContent .= 'echo Conversion completed' . PHP_EOL;
            
            file_put_contents($batchFile, $batchContent);
            
            $output = [];
            $returnCode = 0;
            exec('cmd /c "' . $batchFile . '"', $output, $returnCode);
            
            // Clean up batch file
            if (file_exists($batchFile)) {
                unlink($batchFile);
            }
            
            Log::info('LibreOffice conversion attempt', [
                'command' => 'cmd /c "' . $batchFile . '"',
                'return_code' => $returnCode,
                'output' => $output,
                'target_pdf_path' => $fullPdfPath,
                'file_exists' => file_exists($fullPdfPath),
                'file_size' => file_exists($fullPdfPath) ? filesize($fullPdfPath) : 0
            ]);
            
            if ($returnCode === 0 && file_exists($fullPdfPath) && filesize($fullPdfPath) > 50000) {
                Log::info('PDF generated successfully using LibreOffice', [
                    'pdf_size' => filesize($fullPdfPath),
                    'pdf_path' => $fullPdfPath
                ]);
                
                // Clean up temporary files
                Storage::disk('public')->delete($qrCodePath);
                unlink($fullCertificatePath);
                
                return response()->download($fullPdfPath, $pdfFileName);
            } else {
                Log::warning('LibreOffice conversion failed', [
                    'return_code' => $returnCode,
                    'output' => $output,
                    'target_path' => $fullPdfPath,
                    'file_exists' => file_exists($fullPdfPath),
                    'file_size' => file_exists($fullPdfPath) ? filesize($fullPdfPath) : 0
                ]);
            }
            
            // Method 2: Try PhpWord PDF writer
            try {
                Settings::setPdfRendererName(\PhpOffice\PhpWord\Settings::PDF_RENDERER_DOMPDF);
                Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));
                
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($fullCertificatePath);
                $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
                $pdfWriter->save($fullPdfPath);
                
                if (file_exists($fullPdfPath) && filesize($fullPdfPath) > 10000) {
                    Log::info('PDF generated successfully using PhpWord');
                    
                    // Clean up temporary files
                    Storage::disk('public')->delete($qrCodePath);
                    unlink($fullCertificatePath);
                    
                    return response()->download($fullPdfPath, $pdfFileName);
                }
            } catch (\Exception $e) {
                Log::warning('PhpWord PDF conversion failed: ' . $e->getMessage());
            }
            
            // Method 3: Use Windows built-in Word to PDF conversion
            try {
                $wordApp = new \COM("Word.Application");
                $wordApp->Visible = false;
                $wordApp->DisplayAlerts = false;
                
                $doc = $wordApp->Documents->Open($fullCertificatePath);
                $doc->ExportAsFixedFormat($fullPdfPath, 17); // 17 = PDF format
                $doc->Close();
                $wordApp->Quit();
                
                if (file_exists($fullPdfPath) && filesize($fullPdfPath) > 50000) {
                    Log::info('PDF generated successfully using Microsoft Word COM');
                    
                    // Clean up temporary files
                    Storage::disk('public')->delete($qrCodePath);
                    unlink($fullCertificatePath);
                    
                    return response()->download($fullPdfPath, $pdfFileName);
                }
            } catch (\Exception $e) {
                Log::warning('Microsoft Word COM conversion failed: ' . $e->getMessage());
            }
            
            // Method 4: Fallback to HTML template (only if all Word-to-PDF methods fail)
            Log::warning('All Word-to-PDF conversion methods failed, using HTML fallback');
            
            $qrCodeBase64 = 'data:image/' . $qrCodeExtension . ';base64,' . base64_encode($qrCode);
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

            // Clean up temporary files
            Storage::disk('public')->delete($qrCodePath);
            unlink($fullCertificatePath);

            return $pdf->download($pdfFileName);

        } catch (\Exception $e) {
            Log::error('PDF Certificate generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'PDF Certificate generation failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Static PDF preview for iframe display
     */
    public function previewCertificate($id)
    {
        Log::info('previewCertificate called with ID: ' . $id);
        try {
            $student = \App\Models\ExStudent::findByStudentId($id);
            if (!$student) {
                Log::error('Student not found for ID: ' . $id);
                abort(404, 'Student not found');
            }
            Log::info('Student found: ' . $student->name);

            // Use the same Word template conversion as the download function
            $templatePath = storage_path('app/templates/certificate_template.docx');
            
            if (!File::exists($templatePath)) {
                Log::error('Word template not found: ' . $templatePath);
                abort(500, 'Certificate template not found');
            }

            // Generate QR Code data
            $qrCodeData = [
                'student_id' => $student->student_id,
                'student_name' => $student->name,
                'certificate_number' => $student->certificate_number,
                'course' => $student->program ?? 'Not Specified',
                'graduation_date' => $student->graduation_date,
                'verification_url' => url('/certificates/verify/' . $student->certificate_number),
                'certificate_download_url' => url('/certificates/generate/' . $student->id),
                'generated_at' => now()->toISOString()
            ];

            // Generate QR Code
            $encodedQrData = base64_encode(json_encode($qrCodeData));
            
            try {
                $qrCode = QrCode::format('png')
                    ->size(600)
                    ->margin(12)
                    ->generate($encodedQrData);
            } catch (\Exception $e) {
                $qrCode = QrCode::format('svg')
                    ->size(600)
                    ->margin(12)
                    ->generate($encodedQrData);
            }

            // Save QR Code temporarily
            $qrCodeExtension = strpos($qrCode, '<svg') !== false ? 'svg' : 'png';
            $qrCodePath = 'temp/qr_preview_' . $student->id . '_' . time() . '.' . $qrCodeExtension;
            Storage::disk('public')->put($qrCodePath, $qrCode);

            // Create a copy of the template for this preview
            $tempWordPath = 'temp/temp_certificate_preview_' . $student->id . '_' . time() . '.docx';
            $fullTempWordPath = storage_path('app/' . $tempWordPath);
            
            // Copy template to temp location
            File::copy($templatePath, $fullTempWordPath);

            // Process the Word document with student data
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($fullTempWordPath);
            
            // Replace placeholders in the Word template
            $templateProcessor->setValue('STUDENT_NAME', $student->name);
            $templateProcessor->setValue('STUDENT_ID', $student->student_id);
            $templateProcessor->setValue('PROGRAM', $student->program ?? 'Bachelor of Science');
            $templateProcessor->setValue('GRADUATION_DATE', $student->graduation_date);
            $templateProcessor->setValue('CERTIFICATE_NUMBER', $student->certificate_number);
            $templateProcessor->setValue('CGPA', $student->formatted_cgpa);
            $templateProcessor->setValue('CURRENT_DATE', now()->format('F j, Y'));
            
            // Save the processed Word document
            $templateProcessor->saveAs($fullTempWordPath);

            // Convert Word to PDF using LibreOffice
            $pdfPath = $this->convertWordToPdfWithLibreOffice($fullTempWordPath);
            
            if (!$pdfPath || !File::exists($pdfPath)) {
                Log::error('PDF conversion failed for preview');
                abort(500, 'Failed to convert certificate to PDF');
            }

            Log::info('PDF preview generated successfully: ' . $pdfPath);

            // Clean up temporary files
            if (File::exists($fullTempWordPath)) {
                unlink($fullTempWordPath);
            }
            Storage::disk('public')->delete($qrCodePath);

            // Return the PDF file
            return response()->file($pdfPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="certificate_preview.pdf"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'X-Frame-Options' => 'SAMEORIGIN'
            ]);

        } catch (\Exception $e) {
            Log::error('Word template PDF preview failed: ' . $e->getMessage());
            abort(500, 'Failed to generate certificate preview: ' . $e->getMessage());
        }
    }

    /**
     * Test PDF generation (simple test endpoint)
     */
    public function testPdfGeneration($studentId)
    {
        try {
            $exStudent = \App\Models\ExStudent::find($studentId);
            
            if (!$exStudent) {
                return response()->json(['error' => 'Ex-student not found'], 404);
            }

            // Simple test - just return student info
            return response()->json([
                'success' => true,
                'student' => [
                    'id' => $exStudent->id,
                    'name' => $exStudent->name,
                    'student_id' => $exStudent->student_id,
                    'program' => $exStudent->program,
                    'graduation_date' => $exStudent->graduation_date,
                    'certificate_number' => $exStudent->certificate_number
                ],
                'message' => 'Test endpoint working - student data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Test failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate Word certificate for ex-student
     */
    public function generateWordCertificate(Request $request)
    {
        $studentId = $request->input('student_id');
        return $this->generateCertificate($studentId);
    }

    /**
     * Generate PDF preview for certificate using Word template with LibreOffice (returns PDF stream for preview)
     */
    public function generatePdfPreview(Request $request, $studentId = null)
    {
        // Handle both POST (form data) and GET (URL parameter) requests
        $studentId = $studentId ?: $request->input('student_id');
        
        Log::info('PDF Preview request received', [
            'method' => $request->method(),
            'student_id' => $studentId,
            'url' => $request->fullUrl(),
            'user_agent' => $request->header('user-agent'),
            'referer' => $request->header('referer')
        ]);
        
        try {
            // Get ex-student data
            $exStudent = \App\Models\ExStudent::findByStudentId($studentId);
            
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
                'certificate_download_url' => url('/certificates/generate/' . $exStudent->id),
                'generated_at' => now()->toISOString()
            ];

            // Generate QR Code
            $encodedQrData = base64_encode(json_encode($qrCodeData));
            
            try {
                $qrCode = QrCode::format('png')
                    ->size(600)
                    ->margin(12)
                    ->generate($encodedQrData);
            } catch (\Exception $e) {
                $qrCode = QrCode::format('svg')
                    ->size(600)
                    ->margin(12)
                    ->generate($encodedQrData);
            }

            // Save QR Code temporarily
            $qrCodeExtension = strpos($qrCode, '<svg') !== false ? 'svg' : 'png';
            $qrCodePath = 'temp/qr_preview_' . $exStudent->id . '_' . time() . '.' . $qrCodeExtension;
            Storage::disk('public')->put($qrCodePath, $qrCode);

            // Check if Word template exists
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

            // Replace QR Code image
            $templateProcessor->setImageValue('QR_CODE', [
                'path' => storage_path('app/public/' . $qrCodePath),
                'width' => 100,
                'height' => 100
            ]);

            // Generate Word certificate temporarily
            $tempWordFileName = 'temp_certificate_' . $exStudent->id . '_' . time() . '.docx';
            $tempWordPath = storage_path('app/temp/' . $tempWordFileName);
            
            // Ensure temp directory exists
            if (!File::exists(dirname($tempWordPath))) {
                File::makeDirectory(dirname($tempWordPath), 0755, true);
            }

            $templateProcessor->saveAs($tempWordPath);

            // Convert Word to PDF using LibreOffice
            $pdfPath = $this->convertWordToPdfWithLibreOffice($tempWordPath);

            // Clean up temporary Word file and QR code
            Storage::disk('public')->delete($qrCodePath);
            if (File::exists($tempWordPath)) {
                unlink($tempWordPath);
            }

            if (!$pdfPath || !File::exists($pdfPath) || filesize($pdfPath) == 0) {
                Log::warning('LibreOffice conversion failed, using DomPDF fallback');
                
                // Fallback: Use DomPDF directly with HTML template
                $data = [
                    'exStudent' => $exStudent,
                    'qrCodePath' => storage_path('app/public/' . $qrCodePath),
                    'qrCodeData' => $qrCodeData
                ];

                $pdf = PDF::loadView('certificate.pdf-template', $data);
                $pdf->setPaper('A4', 'portrait');
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'Times New Roman'
                ]);

                // Clean up QR code
                Storage::disk('public')->delete($qrCodePath);

                return response($pdf->output(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="certificate_preview.pdf"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);
            }

            // Log PDF file details for debugging
            Log::info('Serving PDF file: ' . $pdfPath . ' (size: ' . filesize($pdfPath) . ' bytes)');

            // Copy PDF to public directory for PDF.js access
            $publicPdfPath = 'certificates/preview_' . $exStudent->id . '_' . time() . '.pdf';
            $publicPdfFullPath = public_path($publicPdfPath);
            
            // Ensure directory exists
            if (!File::exists(dirname($publicPdfFullPath))) {
                File::makeDirectory(dirname($publicPdfFullPath), 0755, true);
            }
            
            // Copy PDF to public directory
            File::copy($pdfPath, $publicPdfFullPath);
            
            // Schedule cleanup of both files after response is sent
            register_shutdown_function(function() use ($pdfPath, $publicPdfFullPath) {
                if (File::exists($pdfPath)) {
                    unlink($pdfPath);
                }
                if (File::exists($publicPdfFullPath)) {
                    unlink($publicPdfFullPath);
                }
            });

            // Return PDF content directly
            $pdfContent = File::get($pdfPath);
            Log::info('PDF content length: ' . strlen($pdfContent) . ' bytes');
            
            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="certificate_preview.pdf"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'Content-Length' => strlen($pdfContent)
            ]);

        } catch (\Exception $e) {
            Log::error('PDF Preview generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'PDF Preview generation failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Convert Word document to PDF using LibreOffice
     */
    private function convertWordToPdfWithLibreOffice($wordPath)
    {
        try {
            $outputDir = dirname($wordPath);
            $libreOfficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"';
            
            // Escape the paths for the command
            $wordPathEscaped = '"' . $wordPath . '"';
            $outputDirEscaped = '"' . $outputDir . '"';
            
            // Build the LibreOffice command
            $command = "{$libreOfficePath} --headless --convert-to pdf --outdir {$outputDirEscaped} {$wordPathEscaped} 2>&1";
            
            Log::info('Running LibreOffice command: ' . $command);
            
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            Log::info('LibreOffice output: ' . implode("\n", $output));
            Log::info('LibreOffice return code: ' . $returnCode);
            
            if ($returnCode === 0) {
                $pdfPath = str_replace('.docx', '.pdf', $wordPath);
                if (File::exists($pdfPath) && filesize($pdfPath) > 0) {
                    Log::info('LibreOffice conversion successful: ' . $pdfPath . ' (size: ' . filesize($pdfPath) . ' bytes)');
                    return $pdfPath;
                } else {
                    Log::error('PDF file not found or empty after LibreOffice conversion');
                }
            } else {
                Log::error('LibreOffice conversion failed with return code: ' . $returnCode);
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('LibreOffice conversion failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Convert Word document to PDF (legacy method)
     */
    private function convertWordToPdf($wordPath)
    {
        try {
            // Try LibreOffice conversion first (if available)
            $outputDir = dirname($wordPath);
            $command = "\"C:\\Program Files\\LibreOffice\\program\\soffice.exe\" --headless --convert-to pdf --outdir \"$outputDir\" \"$wordPath\" 2>&1";
            
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                $pdfPath = str_replace('.docx', '.pdf', $wordPath);
                if (File::exists($pdfPath)) {
                    Log::info('LibreOffice conversion successful: ' . $pdfPath);
                    return $pdfPath;
                }
            }
            
            Log::info('LibreOffice not available or failed, trying fallback method');
            
            // Fallback: Use PhpWord to convert to HTML, then to PDF
            $phpWord = IOFactory::load($wordPath);
            $htmlWriter = new HTML($phpWord);
            
            $htmlPath = str_replace('.docx', '.html', $wordPath);
            $htmlPath = str_replace('\\', '/', $htmlPath); // Normalize path separators
            
            try {
                $htmlWriter->save($htmlPath);
                
                // Convert HTML to PDF
                $pdf = PDF::loadFile($htmlPath);
                $pdf->setPaper('A4', 'portrait');
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'Times New Roman'
                ]);
                
                $pdfPath = str_replace('.docx', '.pdf', $wordPath);
                $pdfPath = str_replace('\\', '/', $pdfPath); // Normalize path separators
                $pdf->save($pdfPath);
                
                // Clean up HTML file
                if (File::exists($htmlPath)) {
                    unlink($htmlPath);
                }
                
            } catch (\Exception $e) {
                Log::error('HTML conversion failed: ' . $e->getMessage());
                // Try alternative approach - direct PDF generation from Word
                return $this->convertWordToPdfAlternative($wordPath);
            }
            
            Log::info('Fallback conversion successful: ' . $pdfPath);
            return $pdfPath;
            
        } catch (\Exception $e) {
            Log::error('Word to PDF conversion failed: ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Alternative Word to PDF conversion method
     */
    private function convertWordToPdfAlternative($wordPath)
    {
        try {
            // Use the existing Word generation method but save as PDF
            $exStudent = \App\Models\ExStudent::find(3); // Get a sample student for testing
            
            if (!$exStudent) {
                Log::error('No ex-student found for alternative conversion');
                return null;
            }

            // Generate QR Code data
            $qrCodeData = [
                'student_id' => $exStudent->student_id,
                'student_name' => $exStudent->name,
                'certificate_number' => $exStudent->certificate_number,
                'course' => $exStudent->program ?? 'Not Specified',
                'graduation_date' => $exStudent->graduation_date,
                'verification_url' => url('/certificates/verify/' . $exStudent->certificate_number),
                'certificate_download_url' => url('/certificates/generate/' . $exStudent->id),
                'generated_at' => now()->toISOString()
            ];

            $encodedQrData = base64_encode(json_encode($qrCodeData));
            
            try {
                $qrCode = QrCode::format('png')
                    ->size(600)
                    ->margin(12)
                    ->generate($encodedQrData);
            } catch (\Exception $e) {
                $qrCode = QrCode::format('svg')
                    ->size(600)
                    ->margin(12)
                    ->generate($encodedQrData);
            }

            // Save QR Code temporarily
            $qrCodeExtension = strpos($qrCode, '<svg') !== false ? 'svg' : 'png';
            $qrCodePath = 'temp/qr_alt_' . $exStudent->id . '_' . time() . '.' . $qrCodeExtension;
            Storage::disk('public')->put($qrCodePath, $qrCode);

            // Use the existing PDF template approach
            $data = [
                'exStudent' => $exStudent,
                'qrCodePath' => storage_path('app/public/' . $qrCodePath),
                'qrCodeData' => $qrCodeData
            ];

            $pdf = PDF::loadView('certificate.pdf-template', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Times New Roman'
            ]);

            $pdfPath = str_replace('.docx', '.pdf', $wordPath);
            $pdfPath = str_replace('\\', '/', $pdfPath);
            $pdf->save($pdfPath);

            // Clean up QR code
            Storage::disk('public')->delete($qrCodePath);

            Log::info('Alternative conversion successful: ' . $pdfPath);
            return $pdfPath;

        } catch (\Exception $e) {
            Log::error('Alternative Word to PDF conversion failed: ' . $e->getMessage());
            return null;
        }
    }
}