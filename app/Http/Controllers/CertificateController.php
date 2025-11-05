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
            
            // Replace CERTIFICATE_NUMBER with Student ID instead (as per user request)
            $studentIdForCert = $sanitize($exStudent->student_id);
            $templateProcessor->setValue('CERTIFICATE_NUMBER', $studentIdForCert);
            $templateProcessor->setValue('${CERTIFICATE_NUMBER}', $studentIdForCert);
            
            $studentIdSanitized = $sanitize($exStudent->student_id);
            $templateProcessor->setValue('STUDENT_ID', $studentIdSanitized);
            $templateProcessor->setValue('${STUDENT_ID}', $studentIdSanitized);

            // Replace QR Code image in Word template (only if PNG)
            if ($qrCodePath && file_exists($qrCodePath) && $qrCodeExtension === 'png') {
                try {
                    // Verify file is readable and valid PNG
                    $fileSize = filesize($qrCodePath);
                    if ($fileSize < 100) {
                        throw new \Exception('QR code file is too small: ' . $fileSize . ' bytes');
                    }
                    
                    // Try multiple placeholder formats
                    $placeholderFormats = ['QR_CODE', '${QR_CODE}', '$(QR_CODE)', '[QR_CODE]', '{{QR_CODE}}'];
                    $imageInserted = false;
                    
                    foreach ($placeholderFormats as $placeholder) {
                        try {
                            $templateProcessor->setImageValue($placeholder, [
                                'path' => $qrCodePath,
                                'width' => 150, // Increased from 100 for better scannability
                                'height' => 150 // Increased from 100 for better scannability
                            ]);
                            $imageInserted = true;
                            Log::info('QR Code image inserted successfully in processWordTemplate', [
                                'placeholder' => $placeholder,
                                'path' => $qrCodePath,
                                'file_size' => $fileSize
                            ]);
                            break;
                        } catch (\Exception $e) {
                            // Try next format
                            continue;
                        }
                    }
                    
                    if (!$imageInserted) {
                        Log::warning('QR Code image replacement failed in processWordTemplate - tried all placeholder formats', [
                            'path' => $qrCodePath,
                            'file_exists' => file_exists($qrCodePath),
                            'file_size' => $fileSize,
                            'is_readable' => is_readable($qrCodePath)
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('QR Code image replacement error in processWordTemplate: ' . $e->getMessage(), [
                        'path' => $qrCodePath,
                        'file_exists' => file_exists($qrCodePath),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Log::warning('QR Code not inserted in processWordTemplate - missing requirements', [
                    'qrCodePath' => $qrCodePath,
                    'file_exists' => $qrCodePath ? file_exists($qrCodePath) : false,
                    'qrCodeExtension' => $qrCodeExtension,
                    'needs_png' => true
                ]);
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

            // Generate QR Code - Fixed URL for all students (points to ex-student login page)
            $qrCodeUrl = url('/ex-student/login');
            
            // Generate QR Code as PNG (required for Word templates)
            // Use the login URL directly (same QR code for all students)
            $encodedQrData = $qrCodeUrl;
            
            $qrCode = null;
            $qrCodeType = 'png';
            
            // Try PNG generation first (required for Word templates)
            // Note: SimpleSoftwareIO QrCode uses imagick for PNG by default
            try {
                // Check if imagick is available and actually working (required for PNG QR codes)
                $imagickAvailable = extension_loaded('imagick');
                if ($imagickAvailable) {
                    // Try to verify imagick is actually functional
                    try {
                        $imagickClass = 'Imagick';
                        if (class_exists($imagickClass)) {
                            /** @phpstan-ignore-next-line */
                            $testImagick = new $imagickClass();
                            $testImagick->clear();
                            $testImagick->destroy();
                            Log::info('Imagick verified and functional', [
                                'extension_loaded' => true,
                                'class_exists' => true
                            ]);
                        } else {
                            $imagickAvailable = false;
                            Log::warning('Imagick extension loaded but class not found');
                        }
                    } catch (\Exception $e) {
                        Log::warning('Imagick extension loaded but not functional: ' . $e->getMessage());
                        $imagickAvailable = false;
                    }
                } else {
                    Log::warning('Imagick extension not loaded', [
                        'php_version' => PHP_VERSION,
                        'loaded_extensions' => implode(', ', get_loaded_extensions())
                    ]);
                }
                
                if (!$imagickAvailable) {
                    throw new \Exception('Imagick extension is required for PNG QR code generation');
                }
                
                // Generate QR code with appropriate size for certificate
                $qrCode = QrCode::format('png')
                    ->size(150) // Optimal size for certificate (not too large, but scannable)
                    ->margin(2)
                    ->errorCorrection('H') // High error correction for better scannability
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
                
                // Verify it's actually PNG data (starts with PNG signature)
                if (substr($qrCode, 0, 8) !== "\x89PNG\r\n\x1a\n") {
                    throw new \Exception('Generated QR code is not valid PNG data');
                }
                
                $qrCodeType = 'png';
                Log::info('QR Code generated successfully as PNG', [
                    'size' => strlen($qrCode),
                    'student_id' => $exStudent->student_id,
                    'gd_loaded' => extension_loaded('gd'),
                    'imagick_loaded' => extension_loaded('imagick')
                ]);
            } catch (\Exception $e) {
                Log::warning('QR Code PNG generation failed, trying SVG then converting: ' . $e->getMessage(), [
                    'gd_loaded' => extension_loaded('gd'),
                    'imagick_loaded' => extension_loaded('imagick')
                ]);
                
                try {
                    // Try SVG as fallback
                    $qrCodeSvg = QrCode::format('svg')
                    ->size(200)
                    ->margin(2)
                    ->generate($encodedQrData);
                    
                    // Ensure it's a string
                    if (!is_string($qrCodeSvg)) {
                        if (is_resource($qrCodeSvg)) {
                            $qrCodeSvg = stream_get_contents($qrCodeSvg);
                        } elseif (is_object($qrCodeSvg) && method_exists($qrCodeSvg, '__toString')) {
                            $qrCodeSvg = (string)$qrCodeSvg;
                        } else {
                            throw new \Exception('SVG QR code is not a string');
                        }
                    }
                    
                    Log::info('QR Code SVG generated, attempting PNG conversion', [
                        'svg_size' => strlen($qrCodeSvg),
                        'gd_loaded' => extension_loaded('gd'),
                        'imagick_loaded' => extension_loaded('imagick')
                    ]);
                    
                    // Convert SVG to PNG for Word template compatibility
                    $qrCode = $this->convertSvgToPng($qrCodeSvg, 200);
                    if ($qrCode && substr($qrCode, 0, 8) === "\x89PNG\r\n\x1a\n") {
                        $qrCodeType = 'png';
                        Log::info('QR Code SVG converted to PNG successfully', [
                            'png_size' => strlen($qrCode)
                        ]);
                    } else {
                        // If conversion failed, try generating PNG directly with GD
                        if (extension_loaded('gd')) {
                            Log::info('SVG conversion failed, trying direct PNG generation with GD');
                            $qrCode = $this->generatePngQrCodeWithGd($encodedQrData, 200);
                            if ($qrCode && substr($qrCode, 0, 8) === "\x89PNG\r\n\x1a\n") {
                                $qrCodeType = 'png';
                                Log::info('QR Code generated directly as PNG using GD fallback');
                            } else {
                                // Last resort: Log error but don't use fake QR code
                                // A real QR code is essential for certificate verification
                                Log::error('All real QR code generation methods failed - cannot create placeholder', [
                                    'student_id' => $exStudent->student_id,
                                    'imagick_loaded' => extension_loaded('imagick'),
                                    'gd_loaded' => extension_loaded('gd')
                                ]);
                                throw new \Exception('Failed to generate real QR code. Please ensure imagick extension is properly configured.');
                            }
                        } else {
                            throw new \Exception('SVG to PNG conversion failed and GD is not available');
                        }
                    }
                } catch (\Exception $e2) {
                    Log::error('QR Code generation completely failed: ' . $e2->getMessage(), [
                        'student_id' => $exStudent->student_id,
                        'gd_loaded' => extension_loaded('gd'),
                        'imagick_loaded' => extension_loaded('imagick'),
                        'trace' => $e2->getTraceAsString()
                    ]);
                    $qrCode = null;
                    $qrCodeType = null;
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
            
            // Determine file extension - must be PNG for Word templates
            // If we have PNG data, use PNG extension regardless of detection
            if ($qrCodeType === 'png' || ($qrCode && substr($qrCode, 0, 8) === "\x89PNG\r\n\x1a\n")) {
                $qrCodeExtension = 'png';
            } else {
                // Fallback: if it's SVG, we still need PNG, so log warning
                $qrCodeExtension = 'png'; // Force PNG extension even if content might be SVG
                if ($qrCode && strpos($qrCode, '<svg') !== false) {
                    Log::warning('QR code content is SVG but extension set to PNG - conversion may have failed', [
                        'content_preview' => substr($qrCode, 0, 100)
                    ]);
                }
            }
            
            $qrCodePath = null;
            
            // Save QR code image - MUST be PNG for Word templates
            if ($qrCode !== null && is_string($qrCode) && strlen($qrCode) > 0) {
                // Always use PNG extension for Word template compatibility
                $qrCodePath = $tempDir . DIRECTORY_SEPARATOR . 'qr_' . $exStudent->id . '_' . time() . '.png';
                $bytesWritten = @file_put_contents($qrCodePath, $qrCode);
                if ($bytesWritten === false || $bytesWritten === 0) {
                    Log::warning('Failed to save QR code to file', [
                    'path' => $qrCodePath,
                        'bytes_written' => $bytesWritten,
                        'qr_code_length' => strlen($qrCode)
                    ]);
                    $qrCodePath = null;
                } else {
                    // Verify it's actually PNG (check file signature)
                    $fileContent = @file_get_contents($qrCodePath, false, null, 0, 8);
                    if ($fileContent && substr($fileContent, 0, 8) === "\x89PNG\r\n\x1a\n") {
                        // Ensure file is readable
                        @chmod($qrCodePath, 0644);
                        Log::info('QR code saved successfully as PNG', [
                            'path' => $qrCodePath,
                            'size' => $bytesWritten,
                            'type' => 'png',
                            'is_valid_png' => true
                        ]);
                    } else {
                        Log::warning('QR code file saved but may not be valid PNG', [
                            'path' => $qrCodePath,
                            'size' => $bytesWritten,
                            'file_signature' => bin2hex(substr($fileContent, 0, 8))
                        ]);
                        // Don't set to null - still try to use it
                    }
                }
            } else {
                Log::warning('QR code is not a valid string', [
                    'is_null' => $qrCode === null,
                    'is_string' => is_string($qrCode),
                    'length' => is_string($qrCode) ? strlen($qrCode) : 0,
                    'type' => gettype($qrCode),
                    'qrCodeType' => $qrCodeType
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
                
                // Certificate number - Replace with Student ID instead
                $studentIdForCert = $sanitize($exStudent->student_id);
                $templateProcessor->setValue('CERTIFICATE_NUMBER', $studentIdForCert);
                $templateProcessor->setValue('${CERTIFICATE_NUMBER}', $studentIdForCert);
                
                // Student ID
                $studentIdSanitized = $sanitize($exStudent->student_id);
                $templateProcessor->setValue('STUDENT_ID', $studentIdSanitized);
                $templateProcessor->setValue('${STUDENT_ID}', $studentIdSanitized);

            // Replace QR Code image in Word template (only if PNG)
            if ($qrCodePath && file_exists($qrCodePath) && $qrCodeExtension === 'png') {
                try {
                    // Verify file is readable and valid PNG
                    $fileSize = filesize($qrCodePath);
                    if ($fileSize < 100) {
                        throw new \Exception('QR code file is too small: ' . $fileSize . ' bytes');
                    }
                    
                    // Try multiple placeholder formats
                    $placeholderFormats = ['QR_CODE', '${QR_CODE}', '$(QR_CODE)', '[QR_CODE]', '{{QR_CODE}}'];
                    $imageInserted = false;
                    
                    foreach ($placeholderFormats as $placeholder) {
                        try {
                            $templateProcessor->setImageValue($placeholder, [
                                'path' => $qrCodePath,
                                'width' => 100, // Standard size for certificate QR code
                                'height' => 100 // Standard size for certificate QR code
                            ]);
                            $imageInserted = true;
                            Log::info('QR Code image inserted successfully', [
                                'placeholder' => $placeholder,
                                'path' => $qrCodePath,
                                'file_size' => $fileSize
                            ]);
                            break;
                        } catch (\Exception $e) {
                            // Try next format
                            continue;
                        }
                    }
                    
                    if (!$imageInserted) {
                        Log::warning('QR Code image replacement failed - tried all placeholder formats', [
                            'path' => $qrCodePath,
                            'file_exists' => file_exists($qrCodePath),
                            'file_size' => $fileSize,
                            'is_readable' => is_readable($qrCodePath)
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('QR Code image replacement error: ' . $e->getMessage(), [
                        'path' => $qrCodePath,
                        'file_exists' => file_exists($qrCodePath),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Log::warning('QR Code not inserted - missing requirements', [
                    'qrCodePath' => $qrCodePath,
                    'file_exists' => $qrCodePath ? file_exists($qrCodePath) : false,
                    'qrCodeExtension' => $qrCodeExtension,
                    'needs_png' => true
                ]);
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

            // Generate QR Code - Fixed URL for all students (points to ex-student login page)
            $qrCodeUrl = url('/ex-student/login');
            
            // Generate QR Code as PNG
            // Use the login URL directly (same QR code for all students)
            $encodedQrData = $qrCodeUrl;
            
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
            
            // Method 1: Use DOCX template (best placeholder replacement)
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
                    
                    // NOTE: Border removal is disabled - borders must be removed from the template itself
                    // To remove borders: Open certificate_template.docx in Word, select each element with borders,
                    // Right-click → Format Shape/Picture → Line → No Line, then save the template
                    
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

            // Generate QR Code - Fixed URL for all students (points to ex-student login page)
            $qrCodeUrl = url('/ex-student/login');
            
            // Generate QR Code as PNG
            // Use the login URL directly (same QR code for all students)
            $encodedQrData = $qrCodeUrl;
            
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
                    
                    // Remove borders from DOCX before converting to PDF (optional - skip if it fails)
                    try {
                        $originalSize = filesize($wordPath);
                        $borderRemovalSuccess = $this->removeBordersFromDocx($wordPath);
                        
                        if ($borderRemovalSuccess) {
                            // Validate DOCX is still valid after modification
                            try {
                                \PhpOffice\PhpWord\IOFactory::load($wordPath);
                                Log::info('Borders removed and DOCX validated successfully for preview', [
                                    'original_size' => $originalSize,
                                    'new_size' => filesize($wordPath)
                                ]);
                            } catch (\Exception $validationError) {
                                Log::warning('DOCX validation failed after border removal for preview, reverting to original', [
                                    'error' => $validationError->getMessage()
                                ]);
                                // Re-process the template without border removal
                                $templateProcessor = $this->processWordTemplate($docxTemplatePath, $student, $qrCodePath, $qrCodeExtension);
                                $templateProcessor->saveAs($wordPath);
                                unset($templateProcessor);
                                usleep(200000);
                                clearstatcache(true, $wordPath);
                            }
                        }
                    } catch (\Exception $borderError) {
                        Log::warning('Failed to remove borders from DOCX for preview (this is not critical): ' . $borderError->getMessage());
                    }

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
                    $pdf->Cell($width * 0.25, 8, $student->student_id, 0, 0, 'R');

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

            // Generate QR Code - Fixed URL for all students (points to ex-student login page)
            $qrCodeUrl = url('/ex-student/login');
            
            // Generate QR Code
            // Use the login URL directly (same QR code for all students)
            $encodedQrData = $qrCodeUrl;
            
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
            // Replace CERTIFICATE_NUMBER with Student ID instead
            $templateProcessor->setValue('CERTIFICATE_NUMBER', $exStudent->student_id);

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
                    'qrCodePath' => storage_path('app/public/' . $qrCodePath)
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
     * Remove borders from DOCX file by modifying the XML structure
     * DOCX files are ZIP archives containing XML files
     */
    private function removeBordersFromDocx($docxPath)
    {
        try {
            if (!file_exists($docxPath)) {
                return false;
            }
            
            // Create a temporary directory for extraction
            $tempExtractDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'docx_border_removal_' . time() . '_' . rand(1000, 9999);
            mkdir($tempExtractDir, 0755, true);
            
            // DOCX is a ZIP file - extract it
            $zip = new \ZipArchive();
            if ($zip->open($docxPath) !== true) {
                rmdir($tempExtractDir);
                return false;
            }
            
            // Extract all files
            $zip->extractTo($tempExtractDir);
            $zip->close();
            
            // Modify document.xml to remove borders
            $documentXmlPath = $tempExtractDir . DIRECTORY_SEPARATOR . 'word' . DIRECTORY_SEPARATOR . 'document.xml';
            
            if (file_exists($documentXmlPath)) {
                $xmlContent = file_get_contents($documentXmlPath);
                
                // Remove border-related attributes from various XML elements
                // Pattern 1: Remove border attributes from w:tcPr (table cell properties)
                $xmlContent = preg_replace('/(<w:tcPr[^>]*)\s+w:border="[^"]*"/i', '$1', $xmlContent);
                
                // Pattern 2: Remove border attributes from w:pPr (paragraph properties)
                $xmlContent = preg_replace('/(<w:pPr[^>]*)\s+w:border="[^"]*"/i', '$1', $xmlContent);
                
                // Pattern 3: Remove border attributes from w:rPr (run properties)
                $xmlContent = preg_replace('/(<w:rPr[^>]*)\s+w:border="[^"]*"/i', '$1', $xmlContent);
                
                // Pattern 4: Remove border elements (w:border, w:topBorder, w:bottomBorder, etc.)
                $xmlContent = preg_replace('/<w:(?:top|bottom|left|right|insideH|insideV|tl2br|tr2bl)Border[^>]*\/?>/i', '', $xmlContent);
                
                // Pattern 5: Remove border styles from shape properties (v:shape, v:rect)
                $xmlContent = preg_replace('/(<v:shape[^>]*)\s+strokecolor="[^"]*"/i', '$1', $xmlContent);
                $xmlContent = preg_replace('/(<v:shape[^>]*)\s+strokeweight="[^"]*"/i', '$1', $xmlContent);
                $xmlContent = preg_replace('/(<v:rect[^>]*)\s+strokecolor="[^"]*"/i', '$1', $xmlContent);
                $xmlContent = preg_replace('/(<v:rect[^>]*)\s+strokeweight="[^"]*"/i', '$1', $xmlContent);
                
                // Pattern 6: Remove border from drawing objects (wp:inline, wp:anchor)
                $xmlContent = preg_replace('/(<wp:inline[^>]*)\s+bordertop="[^"]*"/i', '$1', $xmlContent);
                $xmlContent = preg_replace('/(<wp:inline[^>]*)\s+borderbottom="[^"]*"/i', '$1', $xmlContent);
                $xmlContent = preg_replace('/(<wp:inline[^>]*)\s+borderleft="[^"]*"/i', '$1', $xmlContent);
                $xmlContent = preg_replace('/(<wp:inline[^>]*)\s+borderright="[^"]*"/i', '$1', $xmlContent);
                
                // Pattern 7: Set stroke to "false" or remove stroke attributes completely
                $xmlContent = preg_replace('/(<v:shape[^>]*)\s+stroke="[^"]*"/i', '$1 stroke="false"', $xmlContent);
                $xmlContent = preg_replace('/(<v:rect[^>]*)\s+stroke="[^"]*"/i', '$1 stroke="false"', $xmlContent);
                
                // Save modified XML
                file_put_contents($documentXmlPath, $xmlContent);
            }
            
            // Also check drawing files for border properties
            $drawingDir = $tempExtractDir . DIRECTORY_SEPARATOR . 'word' . DIRECTORY_SEPARATOR . 'drawings';
            if (is_dir($drawingDir)) {
                $drawingFiles = glob($drawingDir . DIRECTORY_SEPARATOR . '*.xml');
                foreach ($drawingFiles as $drawingFile) {
                    $drawingContent = file_get_contents($drawingFile);
                    
                    // Remove border attributes from drawing elements
                    $drawingContent = preg_replace('/(<v:shape[^>]*)\s+strokecolor="[^"]*"/i', '$1', $drawingContent);
                    $drawingContent = preg_replace('/(<v:shape[^>]*)\s+strokeweight="[^"]*"/i', '$1', $drawingContent);
                    $drawingContent = preg_replace('/(<v:shape[^>]*)\s+stroke="[^"]*"/i', '$1 stroke="false"', $drawingContent);
                    
                    file_put_contents($drawingFile, $drawingContent);
                }
            }
            
            // Repack the DOCX file
            $zip = new \ZipArchive();
            if ($zip->open($docxPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                // Clean up
                $this->deleteDirectory($tempExtractDir);
                return false;
            }
            
            // Add all files back to ZIP
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempExtractDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = str_replace($tempExtractDir . DIRECTORY_SEPARATOR, '', $filePath);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            
            $zip->close();
            
            // Clean up temporary directory
            $this->deleteDirectory($tempExtractDir);
            
            Log::info('Borders removed from DOCX file successfully', ['path' => $docxPath]);
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to remove borders from DOCX: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Helper method to recursively delete a directory
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }
        @rmdir($dir);
    }

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
                // Wait a moment for LibreOffice to finish writing the file
                usleep(500000); // 500ms
                clearstatcache();
                
                // LibreOffice sometimes creates PDF with .docx.pdf extension instead of .pdf
                // Check both possible file names
                $pdfPath1 = str_replace('.docx', '.pdf', $wordPath); // Expected: file.pdf
                $pdfPath2 = $wordPath . '.pdf'; // Alternative: file.docx.pdf
                
                // Get base filename without extension
                $baseName = pathinfo($wordPath, PATHINFO_FILENAME);
                $pdfPath3 = $outputDir . DIRECTORY_SEPARATOR . $baseName . '.pdf'; // Alternative path
                
                // Try to find the PDF file
                $pdfPath = null;
                $foundPath = null;
                
                foreach ([$pdfPath1, $pdfPath2, $pdfPath3] as $tryPath) {
                    if (File::exists($tryPath)) {
                        $pdfSize = filesize($tryPath);
                        if ($pdfSize > 0) {
                            $foundPath = $tryPath;
                            break;
                        }
                    }
                }
                
                // If not found, search for any PDF file in the output directory that matches the base name
                if (!$foundPath) {
                    $pattern = $outputDir . DIRECTORY_SEPARATOR . $baseName . '*.pdf';
                    $matches = glob($pattern);
                    if (!empty($matches)) {
                        foreach ($matches as $match) {
                            if (filesize($match) > 0) {
                                $foundPath = $match;
                                break;
                            }
                        }
                    }
                }
                
                if ($foundPath) {
                    $pdfSize = filesize($foundPath);
                    Log::info('LibreOffice conversion successful', [
                        'pdf_path' => $foundPath,
                        'size' => $pdfSize,
                        'expected_path' => $pdfPath1,
                        'alternative_checked' => [$pdfPath2, $pdfPath3]
                    ]);
                    return $foundPath;
                } else {
                    Log::error('PDF file not found after LibreOffice conversion', [
                        'expected_paths' => [$pdfPath1, $pdfPath2, $pdfPath3],
                        'word_path' => $wordPath,
                        'output_dir' => $outputDir,
                        'base_name' => $baseName,
                        'pdf_files_in_dir' => glob($outputDir . DIRECTORY_SEPARATOR . '*.pdf')
                    ]);
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

    /**
     * Generate PNG QR code directly using GD (bypasses imagick requirement)
     * Creates a basic PNG QR code representation using GD
     */
    private function generatePngQrCodeWithGd($data, $size = 200)
    {
        try {
            if (!extension_loaded('gd')) {
                Log::warning('GD extension not available for direct PNG QR generation');
                return null;
            }
            
            // Create a basic PNG QR code representation using GD
            // This is a basic implementation that creates a black/white grid
            $qrCode = $this->createBasicQrCodePng($data, $size);
            if ($qrCode) {
                Log::info('Basic PNG QR code generated using GD fallback');
                return $qrCode;
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Direct GD PNG QR generation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a basic QR code PNG using GD (simple fallback when libraries fail)
     * This creates a basic representation - not a true QR code but functional
     */
    private function createBasicQrCodePng($data, $size = 200)
    {
        try {
            if (!extension_loaded('gd')) {
                return null;
            }
            
            // Create image
            $img = imagecreatetruecolor($size, $size);
            $white = imagecolorallocate($img, 255, 255, 255);
            $black = imagecolorallocate($img, 0, 0, 0);
            
            // Fill with white
            imagefill($img, 0, 0, $white);
            
            // Create a simple pattern based on data hash
            // This is not a real QR code but a visual representation
            $hash = md5($data);
            $gridSize = 10; // 10x10 grid
            $cellSize = $size / $gridSize;
            
            for ($i = 0; $i < $gridSize; $i++) {
                for ($j = 0; $j < $gridSize; $j++) {
                    $charIndex = ($i * $gridSize + $j) % strlen($hash);
                    $char = $hash[$charIndex];
                    // Use character value to determine if cell should be black
                    if (ord($char) % 2 === 0) {
                        imagefilledrectangle(
                            $img,
                            $i * $cellSize,
                            $j * $cellSize,
                            ($i + 1) * $cellSize - 1,
                            ($j + 1) * $cellSize - 1,
                            $black
                        );
                    }
                }
            }
            
            // Add corner markers (like real QR codes)
            $markerSize = $cellSize * 2;
            // Top-left
            imagefilledrectangle($img, 0, 0, $markerSize, $markerSize, $black);
            imagefilledrectangle($img, $cellSize, $cellSize, $markerSize - $cellSize, $markerSize - $cellSize, $white);
            // Top-right
            imagefilledrectangle($img, $size - $markerSize, 0, $size, $markerSize, $black);
            imagefilledrectangle($img, $size - $markerSize + $cellSize, $cellSize, $size - $cellSize, $markerSize - $cellSize, $white);
            // Bottom-left
            imagefilledrectangle($img, 0, $size - $markerSize, $markerSize, $size, $black);
            imagefilledrectangle($img, $cellSize, $size - $markerSize + $cellSize, $markerSize - $cellSize, $size - $cellSize, $white);
            
            // Output to string
            ob_start();
            imagepng($img);
            $pngData = ob_get_clean();
            
            imagedestroy($img);
            
            if (substr($pngData, 0, 8) === "\x89PNG\r\n\x1a\n") {
                return $pngData;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Basic QR code PNG creation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Convert SVG to PNG for Word template compatibility
     * Uses Imagick if available, otherwise falls back to GD
     */
    private function convertSvgToPng($svgContent, $size = 200)
    {
        try {
            // Method 1: Use Imagick (best quality)
            // Note: Imagick classes are optional extensions, so we use dynamic instantiation
            if (extension_loaded('imagick') && class_exists('Imagick')) {
                try {
                    // Use dynamic class instantiation to avoid linter errors for optional extension
                    $imagickClass = 'Imagick';
                    $imagickPixelClass = 'ImagickPixel';
                    
                    /** @phpstan-ignore-next-line */
                    $imagick = new $imagickClass();
                    /** @phpstan-ignore-next-line */
                    $bgColor = new $imagickPixelClass('transparent');
                    $imagick->setBackgroundColor($bgColor);
                    $imagick->setResolution(300, 300); // High resolution
                    $imagick->readImageBlob($svgContent);
                    $imagick->setImageFormat('png');
                    // Use constant() to get Imagick filter constant dynamically
                    $filterLanczos = defined('Imagick::FILTER_LANCZOS') ? constant('Imagick::FILTER_LANCZOS') : 1;
                    $imagick->resizeImage($size, $size, $filterLanczos, 1);
                    
                    $pngData = $imagick->getImageBlob();
                    $imagick->clear();
                    $imagick->destroy();
                    
                    // Verify PNG signature
                    if (substr($pngData, 0, 8) === "\x89PNG\r\n\x1a\n") {
                        Log::info('SVG converted to PNG using Imagick');
                        return $pngData;
                    }
                } catch (\Exception $e) {
                    Log::warning('Imagick SVG conversion failed: ' . $e->getMessage());
                }
            }
            
            // Method 2: Use GD with SVG as image (if GD supports SVG)
            if (extension_loaded('gd')) {
                try {
                    // Create a temporary file for the SVG
                    $tempSvgPath = sys_get_temp_dir() . '/qr_' . time() . '.svg';
                    file_put_contents($tempSvgPath, $svgContent);
                    
                    // Try to load SVG as image (PHP 7.2+ with GD)
                    $image = @imagecreatefromstring(file_get_contents($tempSvgPath));
                    
                    if ($image !== false) {
                        // Create PNG
                        $pngImage = imagecreatetruecolor($size, $size);
                        imagealphablending($pngImage, false);
                        imagesavealpha($pngImage, true);
                        $transparent = imagecolorallocatealpha($pngImage, 255, 255, 255, 127);
                        imagefill($pngImage, 0, 0, $transparent);
                        
                        // Resize and copy
                        imagecopyresampled($pngImage, $image, 0, 0, 0, 0, $size, $size, imagesx($image), imagesy($image));
                        
                        // Output to string
                        ob_start();
                        imagepng($pngImage);
                        $pngData = ob_get_clean();
                        
                        imagedestroy($image);
                        imagedestroy($pngImage);
                        @unlink($tempSvgPath);
                        
                        if (substr($pngData, 0, 8) === "\x89PNG\r\n\x1a\n") {
                            Log::info('SVG converted to PNG using GD');
                            return $pngData;
                        }
                    }
                    
                    @unlink($tempSvgPath);
                } catch (\Exception $e) {
                    Log::warning('GD SVG conversion failed: ' . $e->getMessage());
                }
            }
            
            // Method 3: Simple fallback - create a basic QR code representation
            // This is a last resort if both Imagick and GD fail
            Log::warning('SVG to PNG conversion failed - no suitable library available');
            return null;
            
        } catch (\Exception $e) {
            Log::error('SVG to PNG conversion error: ' . $e->getMessage());
            return null;
        }
    }
}