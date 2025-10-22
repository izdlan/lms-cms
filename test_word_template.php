<?php
/**
 * Test Word Template PDF Generation
 * 
 * This script tests the cPanel-compatible Word template PDF generation
 * using your existing certificate_template.docx file.
 */

require_once 'vendor/autoload.php';

use App\Services\CpanelCertificateService;
use App\Models\ExStudent;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Word Template PDF Generation Test ===\n\n";

try {
    // Check if Word template exists
    $templatePath = storage_path('app/templates/certificate_template.docx');
    echo "Word Template Check:\n";
    echo "===================\n";
    echo "Template Path: " . $templatePath . "\n";
    echo "Template Exists: " . (file_exists($templatePath) ? 'YES' : 'NO') . "\n";
    
    if (!file_exists($templatePath)) {
        echo "\n❌ ERROR: Word template not found!\n";
        echo "Please upload your certificate_template.docx to: storage/app/templates/\n";
        exit(1);
    }
    
    echo "Template Size: " . filesize($templatePath) . " bytes\n\n";
    
    // Test with a sample ex-student
    $exStudent = ExStudent::first();
    
    if (!$exStudent) {
        echo "ERROR: No ex-students found in database.\n";
        echo "Please add some ex-students first.\n";
        exit(1);
    }
    
    echo "Testing with Ex-Student:\n";
    echo "=======================\n";
    echo "ID: " . $exStudent->id . "\n";
    echo "Student ID: " . $exStudent->student_id . "\n";
    echo "Name: " . $exStudent->name . "\n";
    echo "Program: " . ($exStudent->program ?? 'Not Specified') . "\n";
    echo "Certificate Number: " . $exStudent->certificate_number . "\n\n";
    
    // Test service capabilities
    $service = new CpanelCertificateService();
    $capabilities = $service->getServerCapabilities();
    
    echo "Server Capabilities:\n";
    echo "===================\n";
    echo "PhpWord Available: " . (class_exists('PhpOffice\PhpWord\TemplateProcessor') ? 'YES' : 'NO') . "\n";
    echo "DomPDF Available: " . (class_exists('Barryvdh\DomPDF\Facade\Pdf') ? 'YES' : 'NO') . "\n";
    echo "QR Code Library: " . (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode') ? 'YES' : 'NO') . "\n";
    echo "ImageMagick: " . ($capabilities['imagick_loaded'] ? 'YES' : 'NO') . "\n";
    echo "Temp Directory: " . $capabilities['temp_dir'] . " (writable: " . ($capabilities['temp_writable'] ? 'YES' : 'NO') . ")\n";
    echo "Storage Directory: " . $capabilities['storage_writable'] ? 'YES' : 'NO' . "\n\n";
    
    // Test QR code generation
    echo "Testing QR Code Generation:\n";
    echo "===========================\n";
    
    $qrCodeData = [
        'student_id' => $exStudent->student_id,
        'student_name' => $exStudent->name,
        'certificate_number' => $exStudent->certificate_number,
        'course' => $exStudent->program ?? 'Not Specified',
        'graduation_date' => $exStudent->graduation_date,
        'verification_url' => 'https://lms.olympia-education.com/certificates/verify/' . $exStudent->certificate_number,
        'generated_at' => now()->toISOString()
    ];
    
    // Test QR code generation
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('generateQrCodeBase64');
    $method->setAccessible(true);
    $qrCodeBase64 = $method->invoke($service, $qrCodeData);
    
    echo "QR Code Generated: " . (strlen($qrCodeBase64) > 50 ? 'YES' : 'NO') . "\n";
    echo "QR Code Length: " . strlen($qrCodeBase64) . " characters\n";
    echo "QR Code Type: " . (strpos($qrCodeBase64, 'data:image/png') === 0 ? 'PNG' : (strpos($qrCodeBase64, 'data:image/svg') === 0 ? 'SVG' : 'Text')) . "\n\n";
    
    // Test temp directory creation
    echo "Testing Temp Directory:\n";
    echo "======================\n";
    $tempDir = storage_path('app/temp');
    echo "Temp Directory: " . $tempDir . "\n";
    echo "Directory Exists: " . (is_dir($tempDir) ? 'YES' : 'NO') . "\n";
    
    if (!is_dir($tempDir)) {
        echo "Creating temp directory...\n";
        mkdir($tempDir, 0755, true);
        echo "Directory Created: " . (is_dir($tempDir) ? 'YES' : 'NO') . "\n";
    }
    
    echo "Directory Writable: " . (is_writable($tempDir) ? 'YES' : 'NO') . "\n\n";
    
    echo "=== Test Complete ===\n";
    echo "If all checks pass, the Word template PDF generation should work.\n";
    echo "You can test the actual PDF generation by visiting:\n";
    echo "https://lms.olympia-education.com/certificates/generate/pdf-cpanel/" . $exStudent->id . "\n";
    
    echo "\nWord Template Placeholders Expected:\n";
    echo "===================================\n";
    echo "STUDENT_NAME: " . $exStudent->name . "\n";
    echo "COURSE_NAME: " . ($exStudent->program ?? 'Not Specified') . "\n";
    echo "GRADUATION_DATE: " . $exStudent->graduation_date . "\n";
    echo "CERTIFICATE_NUMBER: " . $exStudent->certificate_number . "\n";
    echo "STUDENT_ID: " . $exStudent->student_id . "\n";
    echo "QR_CODE: [QR Code Image]\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
