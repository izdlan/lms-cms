<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpWord\TemplateProcessor;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
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
            \Log::error('Certificate generation failed: ' . $e->getMessage());
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
            
            \Log::info('LibreOffice conversion attempt', [
                'command' => 'cmd /c "' . $batchFile . '"',
                'return_code' => $returnCode,
                'output' => $output,
                'target_pdf_path' => $fullPdfPath,
                'file_exists' => file_exists($fullPdfPath),
                'file_size' => file_exists($fullPdfPath) ? filesize($fullPdfPath) : 0
            ]);
            
            if ($returnCode === 0 && file_exists($fullPdfPath) && filesize($fullPdfPath) > 50000) {
                \Log::info('PDF generated successfully using LibreOffice', [
                    'pdf_size' => filesize($fullPdfPath),
                    'pdf_path' => $fullPdfPath
                ]);
                
                // Clean up temporary files
                Storage::disk('public')->delete($qrCodePath);
                unlink($fullCertificatePath);
                
                return response()->download($fullPdfPath, $pdfFileName);
            } else {
                \Log::warning('LibreOffice conversion failed', [
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
                    \Log::info('PDF generated successfully using PhpWord');
                    
                    // Clean up temporary files
                    Storage::disk('public')->delete($qrCodePath);
                    unlink($fullCertificatePath);
                    
                    return response()->download($fullPdfPath, $pdfFileName);
                }
            } catch (\Exception $e) {
                \Log::warning('PhpWord PDF conversion failed: ' . $e->getMessage());
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
                    \Log::info('PDF generated successfully using Microsoft Word COM');
                    
                    // Clean up temporary files
                    Storage::disk('public')->delete($qrCodePath);
                    unlink($fullCertificatePath);
                    
                    return response()->download($fullPdfPath, $pdfFileName);
                }
            } catch (\Exception $e) {
                \Log::warning('Microsoft Word COM conversion failed: ' . $e->getMessage());
            }
            
            // Method 4: Fallback to HTML template (only if all Word-to-PDF methods fail)
            \Log::warning('All Word-to-PDF conversion methods failed, using HTML fallback');
            
            $qrCodeBase64 = 'data:image/' . $qrCodeExtension . ';base64,' . base64_encode($qrCode);
            $data = [
                'exStudent' => $exStudent,
                'qrCode' => $qrCodeBase64,
                'qrCodeData' => $qrCodeData
            ];

            $pdf = Pdf::loadView('certificate.pdf-template', $data);
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
            \Log::error('PDF Certificate generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'PDF Certificate generation failed: ' . $e->getMessage()], 500);
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
}