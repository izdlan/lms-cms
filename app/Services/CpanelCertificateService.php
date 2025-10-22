<?php

namespace App\Services;

use App\Models\ExStudent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class CpanelCertificateService
{
    /**
     * Generate PDF certificate from Word template (cPanel compatible)
     */
    public function generatePdfCertificate(ExStudent $exStudent)
    {
        try {
            Log::info('Starting cPanel PDF certificate generation from Word template', [
                'student_id' => $exStudent->student_id,
                'certificate_number' => $exStudent->certificate_number
            ]);

            // Check if Word template exists
            $templatePath = storage_path('app/templates/certificate_template.docx');
            if (!file_exists($templatePath)) {
                Log::error('Word template not found', ['path' => $templatePath]);
                return response()->json([
                    'error' => 'Certificate template not found. Please upload the template to storage/app/templates/certificate_template.docx'
                ], 404);
            }

            // Generate QR Code data
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
            $qrCodeBase64 = $this->generateQrCodeBase64($qrCodeData);
            
            // Convert base64 to temporary file for Word template
            $qrCodePath = $this->saveQrCodeToTemp($qrCodeBase64, $exStudent->id);

            // Process Word template
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

            // Replace placeholders in Word template
            $templateProcessor->setValue('STUDENT_NAME', $exStudent->name);
            $templateProcessor->setValue('COURSE_NAME', $exStudent->program ?? 'Not Specified');
            $templateProcessor->setValue('GRADUATION_DATE', $exStudent->graduation_date);
            $templateProcessor->setValue('CERTIFICATE_NUMBER', $exStudent->certificate_number);
            $templateProcessor->setValue('STUDENT_ID', $exStudent->student_id);

            // Replace QR Code image in Word template
            if ($qrCodePath && file_exists($qrCodePath)) {
                $templateProcessor->setImageValue('QR_CODE', [
                    'path' => $qrCodePath,
                    'width' => 100,
                    'height' => 100
                ]);
            }

            // Generate Word document
            $wordFileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.docx';
            $wordPath = storage_path('app/temp/' . $wordFileName);
            
            // Ensure temp directory exists
            if (!is_dir(dirname($wordPath))) {
                mkdir(dirname($wordPath), 0755, true);
            }
            
            $templateProcessor->saveAs($wordPath);

            // Convert Word to PDF using cPanel-compatible method
            $pdfPath = $this->convertWordToPdfCpanel($wordPath);

            if ($pdfPath && file_exists($pdfPath)) {
                $fileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.pdf';
                
                // Clean up temporary files
                $this->cleanupTempFiles([$qrCodePath, $wordPath]);
                
                Log::info('PDF generated successfully from Word template', [
                    'student_id' => $exStudent->student_id,
                    'file_name' => $fileName
                ]);

                return response()->download($pdfPath, $fileName);
            } else {
                throw new \Exception('Failed to convert Word document to PDF');
            }

        } catch (\Exception $e) {
            Log::error('cPanel PDF generation from Word template failed', [
                'error' => $e->getMessage(),
                'student_id' => $exStudent->student_id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'PDF Certificate generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate QR Code as base64 data URI (no file operations)
     */
    private function generateQrCodeBase64(array $qrCodeData): string
    {
        try {
            $encodedQrData = base64_encode(json_encode($qrCodeData));
            
            // Try PNG first (if ImageMagick is available)
            try {
                $qrCode = QrCode::format('png')
                    ->size(200)
                    ->margin(2)
                    ->errorCorrection('M')
                    ->generate($encodedQrData);
                
                return 'data:image/png;base64,' . base64_encode($qrCode);
                
            } catch (\Exception $e) {
                Log::info('PNG QR generation failed, using SVG fallback', ['error' => $e->getMessage()]);
                
                // Fallback to SVG
                $qrCode = QrCode::format('svg')
                    ->size(200)
                    ->margin(2)
                    ->errorCorrection('M')
                    ->generate($encodedQrData);
                
                return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
            }
            
        } catch (\Exception $e) {
            Log::error('QR Code generation failed completely', ['error' => $e->getMessage()]);
            
            // Ultimate fallback - return a simple text-based QR representation
            return $this->generateTextBasedQr($qrCodeData);
        }
    }

    /**
     * Generate a text-based QR representation as fallback
     */
    private function generateTextBasedQr(array $qrCodeData): string
    {
        $qrText = "QR Code Data:\n";
        $qrText .= "Student ID: " . $qrCodeData['student_id'] . "\n";
        $qrText .= "Name: " . $qrCodeData['student_name'] . "\n";
        $qrText .= "Certificate: " . $qrCodeData['certificate_number'] . "\n";
        $qrText .= "Course: " . $qrCodeData['course'] . "\n";
        $qrText .= "Date: " . $qrCodeData['graduation_date'] . "\n";
        $qrText .= "Verify: " . $qrCodeData['verification_url'];
        
        return 'data:text/plain;base64,' . base64_encode($qrText);
    }

    /**
     * Save QR code base64 to temporary file for Word template
     */
    private function saveQrCodeToTemp(string $qrCodeBase64, int $studentId): ?string
    {
        try {
            // Extract image data from base64
            if (strpos($qrCodeBase64, 'data:image/png;base64,') === 0) {
                $imageData = base64_decode(substr($qrCodeBase64, 22));
                $extension = 'png';
            } elseif (strpos($qrCodeBase64, 'data:image/svg+xml;base64,') === 0) {
                $imageData = base64_decode(substr($qrCodeBase64, 26));
                $extension = 'svg';
            } else {
                // Text fallback - create a simple text file
                $imageData = $qrCodeBase64;
                $extension = 'txt';
            }

            // Create temp directory if it doesn't exist
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Save to temporary file
            $tempPath = $tempDir . '/qr_' . $studentId . '_' . time() . '.' . $extension;
            file_put_contents($tempPath, $imageData);

            return $tempPath;

        } catch (\Exception $e) {
            Log::error('Failed to save QR code to temp file', [
                'error' => $e->getMessage(),
                'student_id' => $studentId
            ]);
            return null;
        }
    }

    /**
     * Convert Word document to PDF (cPanel compatible)
     */
    private function convertWordToPdfCpanel(string $wordPath): ?string
    {
        try {
            $pdfPath = str_replace('.docx', '.pdf', $wordPath);

            // Method 1: Try PhpWord PDF writer (most compatible with cPanel)
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

            // Method 2: Convert to HTML then to PDF (fallback)
            try {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordPath);
                $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
                
                $htmlPath = str_replace('.docx', '.html', $wordPath);
                $htmlWriter->save($htmlPath);
                
                // Convert HTML to PDF
                $pdf = Pdf::loadFile($htmlPath);
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

            Log::error('All Word to PDF conversion methods failed');
            return null;

        } catch (\Exception $e) {
            Log::error('Word to PDF conversion failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Clean up temporary files
     */
    private function cleanupTempFiles(array $filePaths): void
    {
        foreach ($filePaths as $filePath) {
            if ($filePath && file_exists($filePath)) {
                try {
                    unlink($filePath);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete temp file: ' . $filePath);
                }
            }
        }
    }

    /**
     * Generate a simple verification URL QR code
     */
    public function generateSimpleQrCode(ExStudent $exStudent): string
    {
        try {
            $verificationUrl = url('/certificates/verify/' . $exStudent->certificate_number);
            
            // Try PNG first
            try {
                $qrCode = QrCode::format('png')
                    ->size(150)
                    ->margin(1)
                    ->errorCorrection('L')
                    ->generate($verificationUrl);
                
                return 'data:image/png;base64,' . base64_encode($qrCode);
                
            } catch (\Exception $e) {
                // Fallback to SVG
                $qrCode = QrCode::format('svg')
                    ->size(150)
                    ->margin(1)
                    ->errorCorrection('L')
                    ->generate($verificationUrl);
                
                return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
            }
            
        } catch (\Exception $e) {
            Log::error('Simple QR generation failed', ['error' => $e->getMessage()]);
            
            // Return verification URL as text
            return 'data:text/plain;base64,' . base64_encode($verificationUrl);
        }
    }

    /**
     * Check if ImageMagick is available on the server
     */
    public function isImageMagickAvailable(): bool
    {
        try {
            if (extension_loaded('imagick')) {
                $imagick = new \Imagick();
                $version = $imagick->getVersion();
                return !empty($version['versionString']);
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get server capabilities for debugging
     */
    public function getServerCapabilities(): array
    {
        return [
            'imagick_loaded' => extension_loaded('imagick'),
            'gd_loaded' => extension_loaded('gd'),
            'curl_loaded' => extension_loaded('curl'),
            'openssl_loaded' => extension_loaded('openssl'),
            'temp_dir' => sys_get_temp_dir(),
            'temp_writable' => is_writable(sys_get_temp_dir()),
            'storage_writable' => is_writable(storage_path()),
            'public_writable' => is_writable(public_path()),
            'php_version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size')
        ];
    }
}
