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
use App\Services\CpanelCertificateService;
use setasign\Fpdi\Tcpdf\Fpdi as TcpdfFpdi;

class CertificateController extends Controller
{
    /**
     * Process Word template with student data (shared method for both Word and PDF)
     */
    private function processWordTemplate($templatePath, $exStudent, $qrCodePath = null, $qrCodeExtension = 'png')
    {
        try {
            // Get program names and formatted date (using new format)
            $shortProgramName = $exStudent->program_short ?? $exStudent->program ?? 'Not Specified';
            $fullProgramName = $exStudent->program_full ?? $exStudent->program_short ?? $exStudent->program ?? 'Not Specified';
            
            // Ensure graduation_day is set (default to 1 if missing)
            if (!$exStudent->graduation_day || $exStudent->graduation_day < 1 || $exStudent->graduation_day > 31) {
                $exStudent->graduation_day = 1;
                $exStudent->save();
            }
            
            // Ensure graduation_month is set (required for formatted date)
            if (!$exStudent->graduation_month) {
                $exStudent->graduation_month = date('m');
                $exStudent->save();
            }
            
            try {
                $formattedGraduationDate = $exStudent->formatted_graduation_date;
            } catch (\Exception $e) {
                $formattedGraduationDate = $exStudent->graduation_date ?? $exStudent->graduation_year ?? 'Unknown';
            }
            
            // Process Word template
            $templateProcessor = new TemplateProcessor($templatePath);

            // Sanitize values to prevent Word XML corruption
            $sanitize = function($value) {
                if ($value === null) {
                    return '';
                }
                $value = trim((string)$value);
                $value = str_replace("\0", '', $value);
                // Escape ampersands that are not already part of XML entities
                $value = preg_replace('/&(?!(?:amp|lt|gt|quot|apos|#\d+|#x[0-9a-fA-F]+);)/i', '&amp;', $value);
                return $value;
            };
            
            // Replace placeholders - use new format
            $studentName = $sanitize($exStudent->name);
            $templateProcessor->setValue('STUDENT_NAME', $studentName);
            $templateProcessor->setValue('${STUDENT_NAME}', $studentName);
            
            $shortProgramNameSanitized = $sanitize($shortProgramName);
            $templateProcessor->setValue('COURSE_NAME', $shortProgramNameSanitized);
            $templateProcessor->setValue('${COURSE_NAME}', $shortProgramNameSanitized);
            
            $fullProgramNameSanitized = $sanitize($fullProgramName);
            $templateProcessor->setValue('PROGRAM_NAME', $fullProgramNameSanitized);
            $templateProcessor->setValue('${PROGRAM_NAME}', $fullProgramNameSanitized);
            $templateProcessor->setValue('FULL_PROGRAM_NAME', $fullProgramNameSanitized);
            $templateProcessor->setValue('${FULL_PROGRAM_NAME}', $fullProgramNameSanitized);
            
            $formattedDateSanitized = $sanitize($formattedGraduationDate);
            $templateProcessor->setValue('GRADUATION_DATE', $formattedDateSanitized);
            $templateProcessor->setValue('${GRADUATION_DATE}', $formattedDateSanitized);
            
            $certNumber = $sanitize($exStudent->certificate_number);
            $templateProcessor->setValue('CERTIFICATE_NUMBER', $certNumber);
            $templateProcessor->setValue('${CERTIFICATE_NUMBER}', $certNumber);
            
            $studentIdSanitized = $sanitize($exStudent->student_id);
            $templateProcessor->setValue('STUDENT_ID', $studentIdSanitized);
            $templateProcessor->setValue('${STUDENT_ID}', $studentIdSanitized);

            // Replace QR Code image in Word template (only if PNG)
            if ($qrCodePath && file_exists($qrCodePath) && $qrCodeExtension === 'png') {
                try {
                    $templateProcessor->setImageValue('QR_CODE', [
                        'path' => $qrCodePath,
                        'width' => 100,
                        'height' => 100
                    ]);
                } catch (\Exception $e) {
                    try {
                        $templateProcessor->setImageValue('${QR_CODE}', [
                            'path' => $qrCodePath,
                            'width' => 100,
                            'height' => 100
                        ]);
                    } catch (\Exception $e2) {
                        Log::warning('QR Code image replacement failed: ' . $e2->getMessage());
                    }
                }
            }
            
            return $templateProcessor;
        } catch (\Exception $e) {
            Log::error('Failed to process Word template', [
                'error' => $e->getMessage(),
                'student_id' => $exStudent->id
            ]);
            throw $e;
        }
    }

    /**
     * Generate certificate for ex-student (cPanel compatible)
     */
    public function generateCertificate($studentId)
    {
        try {
            // Get ex-student data
            $exStudent = \App\Models\ExStudent::find($studentId);
            
            if (!$exStudent) {
                return response()->json(['error' => 'Ex-student not found'], 404);
            }

            // Get program names and formatted date (using new format)
            // Add validation and fallbacks for new students
            $shortProgramName = $exStudent->program_short ?? $exStudent->program ?? 'Not Specified';
            $fullProgramName = $exStudent->program_full ?? $exStudent->program_short ?? $exStudent->program ?? 'Not Specified';
            
            // Ensure graduation_day is set (default to 1 if missing)
            if (!$exStudent->graduation_day || $exStudent->graduation_day < 1 || $exStudent->graduation_day > 31) {
                $exStudent->graduation_day = 1;
                $exStudent->save();
            }
            
            // Ensure graduation_month is set (required for formatted date)
            if (!$exStudent->graduation_month) {
                Log::warning('Graduation month missing for student', [
                    'student_id' => $exStudent->student_id,
                    'name' => $exStudent->name
                ]);
                // Use current month as fallback
                $exStudent->graduation_month = date('m');
                $exStudent->save();
            }
            
            try {
                $formattedGraduationDate = $exStudent->formatted_graduation_date;
            } catch (\Exception $e) {
                Log::error('Failed to format graduation date', [
                    'student_id' => $exStudent->student_id,
                    'error' => $e->getMessage(),
                    'graduation_year' => $exStudent->graduation_year,
                    'graduation_month' => $exStudent->graduation_month,
                    'graduation_day' => $exStudent->graduation_day
                ]);
                // Fallback to old format
                $formattedGraduationDate = $exStudent->graduation_date ?? $exStudent->graduation_year ?? 'Unknown';
            }
            
            // Log the values being used for debugging
            Log::info('Certificate generation - Student data', [
                'student_id' => $exStudent->student_id,
                'name' => $exStudent->name,
                'program_short' => $shortProgramName,
                'program_full' => $fullProgramName,
                'formatted_graduation_date' => $formattedGraduationDate,
                'graduation_year' => $exStudent->graduation_year,
                'graduation_month' => $exStudent->graduation_month,
                'graduation_day' => $exStudent->graduation_day
            ]);
            
            // Validate critical fields
            if (empty($shortProgramName) || $shortProgramName === 'Not Specified') {
                Log::warning('Short program name is missing or invalid', [
                    'student_id' => $exStudent->student_id,
                    'program_short' => $exStudent->program_short,
                    'program' => $exStudent->program
                ]);
            }
            
            if (empty($fullProgramName) || $fullProgramName === 'Not Specified') {
                Log::warning('Full program name is missing or invalid', [
                    'student_id' => $exStudent->student_id,
                    'program_full' => $exStudent->program_full,
                    'program_short' => $exStudent->program_short,
                    'program' => $exStudent->program
                ]);
            }

            // Generate QR Code data for verification
            $qrCodeData = [
                'student_id' => $exStudent->student_id,
                'student_name' => $exStudent->name,
                'certificate_number' => $exStudent->certificate_number,
                'course' => $fullProgramName,
                'graduation_date' => $formattedGraduationDate,
                'verification_url' => url('/certificates/verify/' . $exStudent->certificate_number),
                'generated_at' => now()->toISOString()
            ];

            // Generate QR Code as PNG
            $encodedQrData = base64_encode(json_encode($qrCodeData));
            
            $qrCode = null;
            $qrCodeType = 'png';
            
            try {
                $qrCode = QrCode::format('png')
                    ->size(200)
                    ->margin(2)
                    ->generate($encodedQrData);
                
                // Ensure it's a string (SimpleSoftwareIO QrCode might return different types)
                if (!is_string($qrCode)) {
                    // Try to convert to string if it's a resource or object
                    if (is_resource($qrCode)) {
                        $qrCode = stream_get_contents($qrCode);
                    } elseif (is_object($qrCode) && method_exists($qrCode, '__toString')) {
                        $qrCode = (string)$qrCode;
                    } else {
                        throw new \Exception('QR code is not a string or convertible to string');
                    }
                }
                
                $qrCodeType = 'png';
            } catch (\Exception $e) {
                Log::warning('QR Code PNG generation failed, trying SVG: ' . $e->getMessage());
                try {
                $qrCode = QrCode::format('svg')
                    ->size(200)
                    ->margin(2)
                    ->generate($encodedQrData);
                    
                    // Ensure it's a string
                    if (!is_string($qrCode)) {
                        if (is_resource($qrCode)) {
                            $qrCode = stream_get_contents($qrCode);
                        } elseif (is_object($qrCode) && method_exists($qrCode, '__toString')) {
                            $qrCode = (string)$qrCode;
                        } else {
                            $qrCode = null;
                        }
                    }
                    
                    $qrCodeType = 'svg';
                } catch (\Exception $e2) {
                    Log::error('QR Code generation completely failed: ' . $e2->getMessage());
                    $qrCode = null;
                }
            }

            // Check if template exists
            $templatePath = storage_path('app/templates/certificate_template.docx');
            if (!File::exists($templatePath)) {
                return response()->json(['error' => 'Certificate template not found. Please upload the template to storage/app/templates/certificate_template.docx'], 404);
            }

            // Validate template file is readable
            if (!is_readable($templatePath)) {
                Log::error('Certificate template is not readable', ['path' => $templatePath]);
                return response()->json(['error' => 'Certificate template file is not readable. Please check file permissions.'], 500);
            }

            // Process Word template
            try {
            $templateProcessor = new TemplateProcessor($templatePath);
            } catch (\Exception $e) {
                Log::error('Failed to load Word template', [
                    'path' => $templatePath,
                    'error' => $e->getMessage()
                ]);
                return response()->json(['error' => 'Failed to load certificate template: ' . $e->getMessage()], 500);
            }

            // Save QR code as temporary file for Word template
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }
            
            // Ensure directory exists and is writable (Windows-compatible)
            if (!is_dir($tempDir) || !is_writable($tempDir)) {
                Log::error('Temp directory is not writable', ['path' => $tempDir]);
                $tempDir = sys_get_temp_dir(); // Fallback to system temp directory
            }
            
            $qrCodeExtension = ($qrCodeType === 'svg' || (is_string($qrCode) && strpos($qrCode, '<svg') !== false)) ? 'svg' : 'png';
            $qrCodePath = null;
            
            // Save QR code image
            if ($qrCode !== null && is_string($qrCode) && strlen($qrCode) > 0) {
                $qrCodePath = $tempDir . DIRECTORY_SEPARATOR . 'qr_' . $exStudent->id . '_' . time() . '.' . $qrCodeExtension;
                $bytesWritten = @file_put_contents($qrCodePath, $qrCode);
                if ($bytesWritten === false || $bytesWritten === 0) {
                    Log::warning('Failed to save QR code to file', [
                        'path' => $qrCodePath,
                        'bytes_written' => $bytesWritten,
                        'qr_code_length' => strlen($qrCode)
                    ]);
                    $qrCodePath = null;
                } else {
                    // Ensure file is readable (Windows might need this)
                    @chmod($qrCodePath, 0644);
                    Log::info('QR code saved successfully', [
                        'path' => $qrCodePath,
                        'size' => $bytesWritten,
                        'type' => $qrCodeExtension
                    ]);
                }
            } else {
                Log::warning('QR code is not a valid string', [
                    'is_null' => $qrCode === null,
                    'is_string' => is_string($qrCode),
                    'length' => is_string($qrCode) ? strlen($qrCode) : 0,
                    'type' => gettype($qrCode)
                ]);
                $qrCodePath = null;
            }

            // Sanitize values to prevent Word XML corruption
            // Word XML is sensitive to certain characters like &, <, >, etc.
            // PhpWord should handle this, but we need to ensure proper encoding
            // IMPORTANT: PhpWord's setValue() doesn't always escape ampersands correctly
            // So we need to escape them ourselves before passing to PhpWord
            $sanitize = function($value) {
                if ($value === null) {
                    return '';
                }
                // Convert to string and trim
                $value = trim((string)$value);
                // Replace NULL bytes (can corrupt files)
                $value = str_replace("\0", '', $value);
                // Escape ampersands that are not already part of XML entities
                // This is critical - unescaped & breaks Word XML parsing
                // Pattern: & that is NOT followed by amp;, lt;, gt;, quot;, apos;, or # (for numeric entities)
                $value = preg_replace('/&(?!(?:amp|lt|gt|quot|apos|#\d+|#x[0-9a-fA-F]+);)/i', '&amp;', $value);
                return $value;
            };

            // Replace placeholders - use new format
            try {
                // Student name
                $studentName = $sanitize($exStudent->name);
                $templateProcessor->setValue('STUDENT_NAME', $studentName);
                $templateProcessor->setValue('${STUDENT_NAME}', $studentName);
                
                // Course name (short program - e.g., "Bachelor of Science")
                $shortProgramNameSanitized = $sanitize($shortProgramName);
                $templateProcessor->setValue('COURSE_NAME', $shortProgramNameSanitized);
                $templateProcessor->setValue('${COURSE_NAME}', $shortProgramNameSanitized);
                
                // Full program name (e.g., "Bachelor of Science (Hons) in Information & Communication Technology")
                $fullProgramNameSanitized = $sanitize($fullProgramName);
                $templateProcessor->setValue('PROGRAM_NAME', $fullProgramNameSanitized);
                $templateProcessor->setValue('${PROGRAM_NAME}', $fullProgramNameSanitized);
                $templateProcessor->setValue('FULL_PROGRAM_NAME', $fullProgramNameSanitized);
                $templateProcessor->setValue('${FULL_PROGRAM_NAME}', $fullProgramNameSanitized);
                
                // Graduation date (formatted as "Tenth day of June 2011")
                $formattedDateSanitized = $sanitize($formattedGraduationDate);
                $templateProcessor->setValue('GRADUATION_DATE', $formattedDateSanitized);
                $templateProcessor->setValue('${GRADUATION_DATE}', $formattedDateSanitized);
                
                // Certificate number
                $certNumber = $sanitize($exStudent->certificate_number);
                $templateProcessor->setValue('CERTIFICATE_NUMBER', $certNumber);
                $templateProcessor->setValue('${CERTIFICATE_NUMBER}', $certNumber);
                
                // Student ID
                $studentIdSanitized = $sanitize($exStudent->student_id);
                $templateProcessor->setValue('STUDENT_ID', $studentIdSanitized);
                $templateProcessor->setValue('${STUDENT_ID}', $studentIdSanitized);

                // Replace QR Code image in Word template (only if PNG)
                if ($qrCodePath && file_exists($qrCodePath) && $qrCodeExtension === 'png') {
                    try {
                $templateProcessor->setImageValue('QR_CODE', [
                    'path' => $qrCodePath,
                    'width' => 100,
                    'height' => 100
                ]);
                    } catch (\Exception $e) {
                        // Try with ${} format
                        try {
                            $templateProcessor->setImageValue('${QR_CODE}', [
                                'path' => $qrCodePath,
                                'width' => 100,
                                'height' => 100
                            ]);
                        } catch (\Exception $e2) {
                            Log::warning('QR Code image replacement failed: ' . $e2->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to replace placeholders in template', [
                    'error' => $e->getMessage(),
                    'student_id' => $exStudent->id
                ]);
                // Continue anyway - some placeholders might not exist
            }

            // Generate final certificate
            $certificateFileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.docx';
            $certificatesDir = storage_path('app' . DIRECTORY_SEPARATOR . 'certificates');
            
            // Ensure certificates directory exists (Windows-compatible)
            if (!is_dir($certificatesDir)) {
                @mkdir($certificatesDir, 0755, true);
            }
            
            // Check if directory is writable
            if (!is_writable($certificatesDir)) {
                Log::error('Certificates directory is not writable', ['path' => $certificatesDir]);
                // Try to set permissions (Windows may ignore this)
                @chmod($certificatesDir, 0755);
            }
            
            $certificatePath = $certificatesDir . DIRECTORY_SEPARATOR . $certificateFileName;

            // Save the template
            try {
                // Close any open file handles before saving (Windows file locking)
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
                
                // Save the template - use absolute path for Windows
            $templateProcessor->saveAs($certificatePath);
                
                // Unset the processor to free resources
                unset($templateProcessor);
                
                // Wait a moment for Windows to release file lock
                usleep(200000); // 200ms - increased for Windows
                
                // Flush any pending writes
                if (function_exists('fflush')) {
                    clearstatcache(true, $certificatePath);
                }
                
                // Verify file was created and is valid
                if (!file_exists($certificatePath)) {
                    throw new \Exception('Certificate file was not created');
                }
                
                // Check file size (should be at least a few KB for a valid DOCX)
                $fileSize = filesize($certificatePath);
                if ($fileSize < 5000) { // Less than 5KB is suspicious
                    Log::warning('Generated certificate file is suspiciously small', [
                        'file_size' => $fileSize,
                        'path' => $certificatePath
                    ]);
                }
                
                // Verify it's a valid ZIP file (DOCX is a ZIP archive)
                $zip = new \ZipArchive();
                $zipResult = $zip->open($certificatePath, \ZipArchive::CHECKCONS);
                if ($zipResult !== true) {
                    $zip->close();
                    throw new \Exception('Generated certificate file is not a valid DOCX (ZIP) file. Error code: ' . $zipResult);
                }
                
                // Validate and repair XML inside the DOCX (Word requires valid XML)
                try {
                    $documentXml = $zip->getFromName('word/document.xml');
                    if ($documentXml !== false) {
                        // Check if XML is valid
                        libxml_use_internal_errors(true);
                        $dom = new \DOMDocument();
                        $loaded = @$dom->loadXML($documentXml);
                        
                        if (!$loaded) {
                            Log::warning('Document XML is invalid, attempting repair', [
                                'errors' => libxml_get_errors()
                            ]);
                            
                            // Try to repair common XML issues
                            // Fix unescaped ampersands (but not XML entities)
                            $documentXml = preg_replace('/&(?!(?:amp|lt|gt|quot|apos);)/', '&amp;', $documentXml);
                            
                            // Try loading again
                            $loaded = @$dom->loadXML($documentXml);
                            if ($loaded) {
                                // Save repaired XML back to DOCX
                                $zip->deleteName('word/document.xml');
                                $zip->addFromString('word/document.xml', $dom->saveXML());
                                Log::info('Document XML repaired successfully');
                            } else {
                                Log::error('Could not repair document XML', [
                                    'errors' => libxml_get_errors()
                                ]);
                            }
                        }
                        libxml_clear_errors();
                    }
                } catch (\Exception $xmlError) {
                    Log::warning('XML validation/repair failed (non-critical): ' . $xmlError->getMessage());
                    // Continue anyway - the file might still work
                }
                
                $zip->close();
                
                // Ensure file is readable (Windows permissions)
                @chmod($certificatePath, 0644);
                
                Log::info('Certificate Word file generated successfully', [
                    'path' => $certificatePath,
                    'size' => $fileSize,
                    'student_id' => $exStudent->student_id
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to save certificate file', [
                    'error' => $e->getMessage(),
                    'path' => $certificatePath,
                    'student_id' => $exStudent->id,
                    'trace' => $e->getTraceAsString()
                ]);

            // Clean up temporary QR code
                if ($qrCodePath && file_exists($qrCodePath)) {
                    @unlink($qrCodePath);
                }
                
                // Clean up certificate file if it was partially created
                if (file_exists($certificatePath)) {
                    @unlink($certificatePath);
                }
                
                return response()->json([
                    'error' => 'Failed to generate certificate file: ' . $e->getMessage() . 
                              '. Please check file permissions and ensure the template file is valid.'
                ], 500);
            }

            // Clean up temporary QR code
            if ($qrCodePath && file_exists($qrCodePath)) {
                @unlink($qrCodePath);
            }

            // Return the file for download
            // Use absolute path for Windows compatibility
            $absolutePath = realpath($certificatePath);
            if ($absolutePath === false) {
                $absolutePath = $certificatePath;
            }
            
            return response()->download($absolutePath, $certificateFileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);

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
     * Generate PDF certificate for ex-student (cPanel compatible)
     */
    public function generatePdfCertificateCpanel($studentId)
    {
        try {
            // Get ex-student data
            $exStudent = \App\Models\ExStudent::find($studentId);
            
            if (!$exStudent) {
                return response()->json(['error' => 'Ex-student not found'], 404);
            }

            // Use cPanel-compatible service
            $cpanelService = new CpanelCertificateService();
            return $cpanelService->generatePdfCertificate($exStudent);

        } catch (\Exception $e) {
            Log::error('cPanel PDF certificate generation failed', [
                'error' => $e->getMessage(),
                'student_id' => $studentId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'PDF Certificate generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF certificate for ex-student using PDF template
     */
    public function generatePdfCertificate($studentId)
    {
        try {
            // Get ex-student data
            $exStudent = \App\Models\ExStudent::find($studentId);
            
            if (!$exStudent) {
                return response()->json(['error' => 'Ex-student not found'], 404);
            }

            // Get program names and formatted date (using new format) - SAME AS Word generation
            // Add validation and fallbacks for new students
            $shortProgramName = $exStudent->program_short ?? $exStudent->program ?? 'Not Specified';
            $fullProgramName = $exStudent->program_full ?? $exStudent->program_short ?? $exStudent->program ?? 'Not Specified';
            
            // Ensure graduation_day is set (default to 1 if missing)
            if (!$exStudent->graduation_day || $exStudent->graduation_day < 1 || $exStudent->graduation_day > 31) {
                $exStudent->graduation_day = 1;
                $exStudent->save();
            }
            
            // Ensure graduation_month is set (required for formatted date)
            if (!$exStudent->graduation_month) {
                Log::warning('Graduation month missing for student in PDF generation', [
                    'student_id' => $exStudent->student_id,
                    'name' => $exStudent->name
                ]);
                // Use current month as fallback
                $exStudent->graduation_month = date('m');
                $exStudent->save();
            }
            
            try {
                $formattedGraduationDate = $exStudent->formatted_graduation_date;
            } catch (\Exception $e) {
                Log::error('Failed to format graduation date in PDF generation', [
                    'student_id' => $exStudent->student_id,
                    'error' => $e->getMessage(),
                    'graduation_year' => $exStudent->graduation_year,
                    'graduation_month' => $exStudent->graduation_month,
                    'graduation_day' => $exStudent->graduation_day
                ]);
                // Fallback to old format
                $formattedGraduationDate = $exStudent->graduation_date ?? $exStudent->graduation_year ?? 'Unknown';
            }

            // Check if PDF or DOCX template exists
            $pdfTemplatePath = storage_path('app/templates/certificate_template.pdf');
            $docxTemplatePath = storage_path('app/templates/certificate_template.docx');
            
            $usePdfTemplate = File::exists($pdfTemplatePath);
            $useDocxTemplate = File::exists($docxTemplatePath);
            
            if (!$usePdfTemplate && !$useDocxTemplate) {
                return response()->json([
                    'error' => 'Template not found. Please upload either certificate_template.pdf or certificate_template.docx to storage/app/templates/'
                ], 404);
            }

            Log::info('Starting PDF certificate generation from template', [
                'pdf_template_exists' => $usePdfTemplate,
                'docx_template_exists' => $useDocxTemplate,
                'student_name' => $exStudent->name,
                'student_id' => $exStudent->id,
                'short_program_name' => $shortProgramName,
                'full_program_name' => $fullProgramName,
                'formatted_graduation_date' => $formattedGraduationDate
            ]);

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

            // Generate QR Code as PNG
            $encodedQrData = base64_encode(json_encode($qrCodeData));
            
            try {
                $qrCode = QrCode::format('png')
                    ->size(150)
                    ->margin(2)
                    ->generate($encodedQrData);
            } catch (\Exception $e) {
                Log::warning('QR Code PNG generation failed, trying SVG: ' . $e->getMessage());
                $qrCode = QrCode::format('svg')
                    ->size(150)
                    ->margin(2)
                    ->generate($encodedQrData);
            }

            // Save QR code as temporary file
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $qrCodeExtension = (is_string($qrCode) && strpos($qrCode, '<svg') !== false) ? 'svg' : 'png';
            $qrCodePath = $tempDir . '/qr_' . $exStudent->id . '_' . time() . '.' . $qrCodeExtension;
            file_put_contents($qrCodePath, $qrCode);

            $pdfContent = null;
            $pdfFileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.pdf';
            
            // Method 1: Prefer DOCX template (better placeholder replacement, no box lines if template is clean)
            // DOCX template properly replaces all placeholders, avoiding leftover placeholder text
            if ($useDocxTemplate) {
                try {
                    Log::info('Using DOCX template for certificate generation (preferred method - replaces all placeholders)');
                    
                    // Use shared template processing method (same as Word generation)
                    $templateProcessor = $this->processWordTemplate($docxTemplatePath, $exStudent, $qrCodePath, $qrCodeExtension);

                    // Generate Word certificate temporarily
                    $wordFileName = 'certificate_' . $exStudent->student_id . '_' . time() . '.docx';
                    $wordPath = $tempDir . DIRECTORY_SEPARATOR . $wordFileName;
                    
                    // Save the template (all placeholders are already replaced above)
                    $templateProcessor->saveAs($wordPath);
                    
                    // Unset processor to free resources (Windows file locking)
                    unset($templateProcessor);
                    
                    // Wait for file to be written (Windows file locking)
                    usleep(200000); // 200ms
                    clearstatcache(true, $wordPath);
                    
                    // Verify Word file was created and is valid
                    if (!file_exists($wordPath)) {
                        throw new \Exception('Word certificate file was not created');
                    }
                    
                    $wordFileSize = filesize($wordPath);
                    if ($wordFileSize < 5000) {
                        Log::warning('Generated Word file is suspiciously small', [
                            'file_size' => $wordFileSize,
                            'path' => $wordPath
                        ]);
                    }
                    
                    Log::info('Word certificate prepared for PDF conversion', [
                        'path' => $wordPath,
                        'size' => $wordFileSize,
                        'student_name' => $exStudent->name,
                        'short_program' => $shortProgramName,
                        'full_program' => $fullProgramName
                    ]);

                    // Convert Word to PDF using LibreOffice (if available)
                    $pdfPath = $this->convertWordToPdfWithLibreOffice($wordPath);
                    
                    if ($pdfPath && file_exists($pdfPath)) {
                        // NOTE: Border removal is disabled because borders come from the Word template itself
                        // The best solution is to remove borders directly in the Word template:
                        // 1. Open certificate_template.docx in Microsoft Word
                        // 2. Select each text box/image that has borders
                        // 3. Right-click → Format Shape/Picture → Line → No Line
                        // 4. Save the template
                        // This will permanently remove borders from future certificates
                        
                        // For now, use the PDF as-is (borders will be visible but content is correct)
                        $pdfContent = file_get_contents($pdfPath);
                        @unlink($pdfPath);
                        Log::info('PDF generated successfully using DOCX template with LibreOffice');
            } else {
                        // Try PhpWord PDF writer as fallback
                        // Note: This often fails with XML parsing errors due to special characters
                        // The FPDI + TCPDF fallback below is more reliable
            try {
                Settings::setPdfRendererName(\PhpOffice\PhpWord\Settings::PDF_RENDERER_DOMPDF);
                Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));
                
                            // Load the Word document
                            $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordPath);
                            
                            // Create PDF writer
                $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
                            
                            $pdfPath = $tempDir . DIRECTORY_SEPARATOR . $wordFileName . '.pdf';
                            $pdfWriter->save($pdfPath);
                            
                            if (file_exists($pdfPath) && filesize($pdfPath) > 10000) {
                                // Use PhpWord PDF directly - no TCPDF extraction needed
                                $pdfContent = file_get_contents($pdfPath);
                                @unlink($pdfPath);
                                Log::info('PDF generated successfully using DOCX template with PhpWord');
                            } else {
                                Log::warning('PhpWord PDF conversion produced invalid file', [
                                    'exists' => file_exists($pdfPath),
                                    'size' => file_exists($pdfPath) ? filesize($pdfPath) : 0
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::warning('PhpWord PDF conversion failed (this is expected with special characters): ' . $e->getMessage());
                            // This is expected - PhpWord has issues with XML entities like & in "Information & Communication Technology"
                            // The FPDI + TCPDF method below will handle this better
                        }
                    }
                    
                    // Clean up Word file
                    if (file_exists($wordPath)) {
                        unlink($wordPath);
                }
            } catch (\Exception $e) {
                    Log::error('DOCX template processing failed: ' . $e->getMessage());
                }
            }
            
            // Method 3: Final fallback - return error if all methods failed
            if (!$pdfContent) {
                return response()->json([
                    'error' => 'Failed to generate PDF certificate. Please check server logs for details.'
                ], 500);
            }

            // Clean up temporary QR code file
            if (file_exists($qrCodePath)) {
                unlink($qrCodePath);
            }

            Log::info('PDF certificate generated successfully', [
                'file_name' => $pdfFileName,
                'pdf_size' => strlen($pdfContent)
            ]);

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $pdfFileName . '"',
                'Content-Length' => strlen($pdfContent)
            ]);

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

            // Check if DOCX template exists (preferred - same as download function)
            $docxTemplatePath = storage_path('app/templates/certificate_template.docx');
            $pdfTemplatePath = storage_path('app/templates/certificate_template.pdf');
            
            $useDocxTemplate = File::exists($docxTemplatePath);
            $usePdfTemplate = File::exists($pdfTemplatePath);
            
            if (!$useDocxTemplate && !$usePdfTemplate) {
                Log::error('No template found');
                abort(500, 'Certificate template not found. Please upload certificate_template.docx or certificate_template.pdf to storage/app/templates/');
            }

            // Generate QR Code data for verification
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

            // Generate QR Code as PNG
            $encodedQrData = base64_encode(json_encode($qrCodeData));
            
            try {
                $qrCode = QrCode::format('png')
                    ->size(150)
                    ->margin(2)
                    ->generate($encodedQrData);
            } catch (\Exception $e) {
                Log::warning('QR Code PNG generation failed, trying SVG: ' . $e->getMessage());
                $qrCode = QrCode::format('svg')
                    ->size(150)
                    ->margin(2)
                    ->generate($encodedQrData);
            }

            // Save QR Code temporarily
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $qrCodeExtension = (is_string($qrCode) && strpos($qrCode, '<svg') !== false) ? 'svg' : 'png';
            $qrCodePath = $tempDir . '/qr_preview_' . $student->id . '_' . time() . '.' . $qrCodeExtension;
            
            // Ensure QR code is a string before saving
            if (is_resource($qrCode)) {
                $qrCode = stream_get_contents($qrCode);
            } elseif (is_object($qrCode) && method_exists($qrCode, '__toString')) {
                $qrCode = $qrCode->__toString();
            } elseif (!is_string($qrCode)) {
                Log::warning('QR code is not a string, converting...');
                $qrCode = (string)$qrCode;
            }
            
            file_put_contents($qrCodePath, $qrCode);

            // Method 1: Use DOCX template (preferred - same as download function)
            if ($useDocxTemplate) {
                try {
                    // Use shared template processing method (same as download function)
                    $templateProcessor = $this->processWordTemplate($docxTemplatePath, $student, $qrCodePath, $qrCodeExtension);

                    // Generate Word certificate temporarily
                    $wordFileName = 'certificate_preview_' . $student->student_id . '_' . time() . '.docx';
                    $wordPath = $tempDir . DIRECTORY_SEPARATOR . $wordFileName;
                    
                    // Save the template (all placeholders are already replaced)
                    $templateProcessor->saveAs($wordPath);
                    
                    // Unset processor to free resources (Windows file locking)
                    unset($templateProcessor);
                    
                    // Wait for file to be written (Windows file locking)
                    usleep(200000); // 200ms
                    clearstatcache(true, $wordPath);

                    // Convert Word to PDF using LibreOffice
                    $pdfPath = $this->convertWordToPdfWithLibreOffice($wordPath);
                    
                    // Clean up Word file
                    if (file_exists($wordPath)) {
                        @unlink($wordPath);
                    }
            
                    if ($pdfPath && file_exists($pdfPath) && filesize($pdfPath) > 0) {
                        Log::info('PDF preview generated successfully using DOCX template: ' . $pdfPath);
                        
                        // Clean up QR code
                        if (file_exists($qrCodePath)) {
                            @unlink($qrCodePath);
                        }
                        
                        // Return the PDF file
                        return response()->file($pdfPath, [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'inline; filename="certificate_preview.pdf"',
                            'Cache-Control' => 'no-cache, no-store, must-revalidate',
                            'Pragma' => 'no-cache',
                            'Expires' => '0',
                            'X-Frame-Options' => 'SAMEORIGIN'
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('DOCX template preview failed: ' . $e->getMessage());
                }
            }
            
            // Method 2: Fallback to PDF template (if DOCX failed or doesn't exist)
            if ($usePdfTemplate && class_exists('TCPDF')) {
                try {
                    // Use FPDI with TCPDF (same as download function)
                    $pdf = new TcpdfFpdi();
                    $pdf->SetAutoPageBreak(false, 0);
                    
                    $pdf->setSourceFile($pdfTemplatePath);
                    $templateId = $pdf->importPage(1);
                    $size = $pdf->getTemplateSize($templateId);
                    $width = $size['width'];
                    $height = $size['height'];
                    
                    $orientation = $width > $height ? 'L' : 'P';
                    $pdf->AddPage($orientation, [$width, $height]);
                    $pdf->useTemplate($templateId, 0, 0, $width, $height, true);

                    // Get program names and formatted date (same as download function)
                    $shortProgramName = $student->program_short ?? $student->program ?? 'Not Specified';
                    $fullProgramName = $student->program_full ?? $student->program_short ?? $student->program ?? 'Not Specified';
                    $formattedGraduationDate = $student->formatted_graduation_date;
                    
                    // Sanitize values for PDF
                    $shortProgramName = htmlspecialchars_decode($shortProgramName, ENT_QUOTES);
                    $fullProgramName = htmlspecialchars_decode($fullProgramName, ENT_QUOTES);
                    $formattedGraduationDate = htmlspecialchars_decode($formattedGraduationDate, ENT_QUOTES);

                    // Disable all borders globally
                    $pdf->SetLineWidth(0.001);
                    $pdf->SetDrawColor(255, 255, 255);
                    $pdf->SetFillColor(255, 255, 255);
                    
                    // Cover placeholder areas with white rectangles
                    $pdf->Rect($width * 0.05, $height * 0.10, $width * 0.9, $height * 0.15, 'F');
                    $pdf->Rect($width * 0.05, $height * 0.20, $width * 0.9, $height * 0.15, 'F');
                    $pdf->Rect($width * 0.05, $height * 0.30, $width * 0.9, $height * 0.12, 'F');
                    $pdf->Rect($width * 0.05, $height * 0.40, $width * 0.9, $height * 0.15, 'F');
                    $pdf->Rect($width * 0.60, $height * 0.80, $width * 0.35, $height * 0.12, 'F');
                    $pdf->Rect($width * 0.75, $height * 0.75, $width * 0.20, $height * 0.20, 'F');

                    // Add text (NO BORDERS)
                    $pdf->SetFont('helvetica', 'B', 32);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetXY($width * 0.1, $height * 0.15);
                    $pdf->MultiCell($width * 0.8, 15, $shortProgramName, 0, 'C');
                    
                    $pdf->SetFont('helvetica', 'B', 28);
                    $pdf->SetXY($width * 0.1, $height * 0.25);
                    $pdf->MultiCell($width * 0.8, 15, strtoupper($student->name), 0, 'C');
                    
                    $pdf->SetFont('helvetica', '', 16);
                    $pdf->SetXY($width * 0.1, $height * 0.35);
                    $pdf->MultiCell($width * 0.8, 12, $fullProgramName, 0, 'C');
                    
                    $pdf->SetFont('helvetica', '', 12);
                    $pdf->SetXY($width * 0.1, $height * 0.48);
                    $pdf->MultiCell($width * 0.8, 10, $formattedGraduationDate, 0, 'C');
                    
                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->SetTextColor(139, 0, 0);
                    $pdf->SetXY($width * 0.65, $height * 0.88);
                    $pdf->Cell($width * 0.25, 8, $student->certificate_number, 0, 0, 'R');

                    // Add QR Code (NO BORDER)
                    if ($qrCodePath && file_exists($qrCodePath) && $qrCodeExtension === 'png') {
                        $qrSize = min($width * 0.10, $height * 0.10, 100);
                        $qrX = $width * 0.88;
                        $qrY = $height * 0.85;
                        
                        try {
                            $pdf->Image($qrCodePath, $qrX, $qrY, $qrSize, $qrSize, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
                        } catch (\Exception $imgError) {
                            Log::warning('QR Code image insertion failed: ' . $imgError->getMessage());
                        }
                    }

                    // Generate PDF to temporary file
                    $tempPdfPath = $tempDir . '/preview_' . $student->id . '_' . time() . '.pdf';
                    $pdf->Output($tempPdfPath, 'F');
                    
                    // Clean up QR code
                    if (file_exists($qrCodePath)) {
                        @unlink($qrCodePath);
                    }
                    
                    if (file_exists($tempPdfPath) && filesize($tempPdfPath) > 0) {
                        Log::info('PDF preview generated successfully using PDF template: ' . $tempPdfPath);
                        
                        return response()->file($tempPdfPath, [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'inline; filename="certificate_preview.pdf"',
                            'Cache-Control' => 'no-cache, no-store, must-revalidate',
                            'Pragma' => 'no-cache',
                            'Expires' => '0',
                            'X-Frame-Options' => 'SAMEORIGIN'
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('PDF template preview failed: ' . $e->getMessage());
                }
            }

            // Clean up QR code if not already cleaned
            if (file_exists($qrCodePath)) {
                @unlink($qrCodePath);
            }

            Log::error('PDF preview generation failed - all methods failed');
            abort(500, 'Failed to generate certificate preview. Please check server logs.');

        } catch (\Exception $e) {
            Log::error('Certificate preview failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
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

    // TCPDF extraction methods removed - using LibreOffice conversion directly
    // TCPDF/FPDI is only used for PDF template fallback (Method 2)

    /**
     * Remove borders/box lines from PDF by covering them with white rectangles
     */
    private function removeBordersFromPdf($pdfPath)
    {
        try {
            if (!class_exists('TCPDF')) {
                return null;
            }

            // Create output path for cleaned PDF
            $cleanedPdfPath = str_replace('.pdf', '_cleaned.pdf', $pdfPath);
            
            // Use FPDI with TCPDF to load and process the PDF
            $pdf = new TcpdfFpdi();
            $pdf->SetAutoPageBreak(false, 0);
            
            // Disable all borders globally
            $pdf->SetLineWidth(0.001);
            $pdf->SetDrawColor(255, 255, 255);
            
            // Load source PDF
            $pageCount = $pdf->setSourceFile($pdfPath);
            
            // Process each page
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $width = $size['width'];
                $height = $size['height'];
                
                // Add page
                $orientation = $width > $height ? 'L' : 'P';
                $pdf->AddPage($orientation, [$width, $height]);
                
                // Import the template (this includes all content including borders)
                $pdf->useTemplate($templateId, 0, 0, $width, $height, true);
                
                // AGGRESSIVE border removal: Cover entire areas with white rectangles
                // Since borders are part of the template, we need to cover the entire bordered areas
                // and then re-add the content on top
                $pdf->SetFillColor(255, 255, 255);
                
                // Cover ALL signature areas completely - left side (entire signature blocks)
                $pdf->Rect($width * 0.03, $height * 0.68, $width * 0.28, $height * 0.18, 'F');
                $pdf->Rect($width * 0.03, $height * 0.72, $width * 0.28, $height * 0.16, 'F');
                
                // Cover ALL "Registrar" and signature areas - right side (entire text blocks)
                $pdf->Rect($width * 0.69, $height * 0.68, $width * 0.28, $height * 0.18, 'F');
                $pdf->Rect($width * 0.69, $height * 0.72, $width * 0.28, $height * 0.16, 'F');
                
                // Cover QR code areas completely - bottom right (entire QR code boxes)
                // Cover multiple QR code positions (there might be 2 stacked)
                $pdf->Rect($width * 0.73, $height * 0.78, $width * 0.24, $height * 0.20, 'F');
                $pdf->Rect($width * 0.76, $height * 0.80, $width * 0.19, $height * 0.17, 'F');
                // Cover second QR code if it exists
                $pdf->Rect($width * 0.76, $height * 0.75, $width * 0.19, $height * 0.17, 'F');
                
                // Cover certificate number area completely - bottom right
                $pdf->Rect($width * 0.64, $height * 0.87, $width * 0.33, $height * 0.11, 'F');
                
                // Cover entire bottom-right area (QR codes + Registrar + Certificate number)
                $pdf->Rect($width * 0.63, $height * 0.75, $width * 0.35, $height * 0.23, 'F');
                
                // Cover any other signature areas - bottom left
                $pdf->Rect($width * 0.03, $height * 0.85, $width * 0.32, $height * 0.12, 'F');
                
                // Cover entire bottom section where borders typically appear
                $pdf->Rect($width * 0.02, $height * 0.75, $width * 0.96, $height * 0.23, 'F');
            }
            
            // Save cleaned PDF
            $pdf->Output($cleanedPdfPath, 'F');
            
            if (file_exists($cleanedPdfPath) && filesize($cleanedPdfPath) > 0) {
                Log::info('Borders removed from PDF successfully', [
                    'original' => $pdfPath,
                    'cleaned' => $cleanedPdfPath,
                    'size' => filesize($cleanedPdfPath)
                ]);
                return $cleanedPdfPath;
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Failed to remove borders from PDF: ' . $e->getMessage());
            return null;
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

    /**
     * Convert Word document to PDF using cPanel-compatible method
     */
    private function convertWordToPdfCpanel($wordPath, $exStudent)
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

            Log::error('All Word to PDF conversion methods failed');
            return null;

        } catch (\Exception $e) {
            Log::error('Word to PDF conversion failed: ' . $e->getMessage());
            return null;
        }
    }
}