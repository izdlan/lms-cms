<?php
/**
 * Test cPanel PDF Generation
 * 
 * This script tests the cPanel-compatible PDF generation
 * without requiring ImageMagick or complex file operations.
 */

require_once 'vendor/autoload.php';

use App\Services\CpanelCertificateService;
use App\Models\ExStudent;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== cPanel PDF Generation Test ===\n\n";

try {
    // Test server capabilities
    $service = new CpanelCertificateService();
    $capabilities = $service->getServerCapabilities();
    
    echo "Server Capabilities:\n";
    echo "===================\n";
    foreach ($capabilities as $key => $value) {
        $status = is_bool($value) ? ($value ? 'YES' : 'NO') : $value;
        echo sprintf("%-20s: %s\n", ucfirst(str_replace('_', ' ', $key)), $status);
    }
    echo "\n";
    
    // Test ImageMagick availability
    $imagickAvailable = $service->isImageMagickAvailable();
    echo "ImageMagick Available: " . ($imagickAvailable ? 'YES' : 'NO') . "\n\n";
    
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
    
    // Test simple QR code generation
    $simpleQr = $service->generateSimpleQrCode($exStudent);
    echo "Simple QR Code: " . (strlen($simpleQr) > 50 ? 'Generated successfully' : 'Failed') . "\n";
    echo "QR Code length: " . strlen($simpleQr) . " characters\n";
    echo "QR Code type: " . (strpos($simpleQr, 'data:image/png') === 0 ? 'PNG' : (strpos($simpleQr, 'data:image/svg') === 0 ? 'SVG' : 'Text')) . "\n\n";
    
    // Test PDF generation (without actually generating the file)
    echo "Testing PDF Generation Setup:\n";
    echo "=============================\n";
    
    // Check if required directories exist
    $tempDir = sys_get_temp_dir();
    $storageDir = storage_path();
    $publicDir = public_path();
    
    echo "Temp directory: " . $tempDir . " (writable: " . (is_writable($tempDir) ? 'YES' : 'NO') . ")\n";
    echo "Storage directory: " . $storageDir . " (writable: " . (is_writable($storageDir) ? 'YES' : 'NO') . ")\n";
    echo "Public directory: " . $publicDir . " (writable: " . (is_writable($publicDir) ? 'YES' : 'NO') . ")\n\n";
    
    // Check if DomPDF is available
    if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
        echo "DomPDF: Available\n";
    } else {
        echo "DomPDF: NOT AVAILABLE - Please install barryvdh/laravel-dompdf\n";
    }
    
    // Check if QR Code library is available
    if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
        echo "QR Code Library: Available\n";
    } else {
        echo "QR Code Library: NOT AVAILABLE - Please install simplesoftwareio/simple-qrcode\n";
    }
    
    echo "\n=== Test Complete ===\n";
    echo "If all checks pass, the cPanel PDF generation should work.\n";
    echo "You can test the actual PDF generation by visiting:\n";
    echo "https://lms.olympia-education.com/certificates/generate/pdf-cpanel/" . $exStudent->id . "\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
