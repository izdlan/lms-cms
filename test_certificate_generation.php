<?php

require_once 'vendor/autoload.php';

use App\Services\CertificateService;
use App\Services\QrCodeService;
use App\Models\ExStudent;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "🎓 Testing Certificate Generation System\n";
    echo "=====================================\n\n";

    // Create sample ex-student data
    $sampleStudent = new ExStudent([
        'student_id' => 'SAMPLE001',
        'name' => 'ASMAWI BIN ASA',
        'email' => 'asmawi@example.com',
        'phone' => '0123456789',
        'program' => 'SARJANA MUDA EKSEKUTIF PENTADBIRAN PERNIAGAAN',
        'graduation_year' => '2025',
        'graduation_month' => '08',
        'cgpa' => 3.50,
        'certificate_number' => 'CERT-20250829-0001',
        'qr_code' => 'SAMPLE_QR_CODE_12345',
        'is_verified' => true,
    ]);

    echo "✅ Sample student data created:\n";
    echo "   - Name: {$sampleStudent->name}\n";
    echo "   - Student ID: {$sampleStudent->student_id}\n";
    echo "   - Program: {$sampleStudent->program}\n";
    echo "   - Certificate Number: {$sampleStudent->certificate_number}\n\n";

    // Initialize services
    $qrCodeService = new QrCodeService();
    $certificateService = new CertificateService($qrCodeService);

    echo "🔧 Services initialized\n\n";

    // Test QR code generation
    echo "📱 Testing QR code generation...\n";
    $qrCodePath = $qrCodeService->generateCertificateQrCode($sampleStudent);
    echo "✅ QR code generated: {$qrCodePath}\n\n";

    // Test certificate generation
    echo "📄 Testing Word certificate generation...\n";
    $certificatePath = $certificateService->generateWordCertificate($sampleStudent);
    echo "✅ Word certificate generated: {$certificatePath}\n\n";

    // Check if files exist
    $fullPath = storage_path('app/public/' . $certificatePath);
    if (file_exists($fullPath)) {
        $fileSize = filesize($fullPath);
        echo "📊 Certificate file details:\n";
        echo "   - File size: " . number_format($fileSize) . " bytes\n";
        echo "   - Full path: {$fullPath}\n";
        echo "   - Public URL: " . asset('storage/' . $certificatePath) . "\n\n";
    } else {
        echo "❌ Certificate file not found at: {$fullPath}\n\n";
    }

    // Test cleanup
    echo "🧹 Testing cleanup...\n";
    $certificateService->cleanupTempFiles();
    echo "✅ Cleanup completed\n\n";

    echo "🎉 Certificate generation test completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Visit: " . route('certificate.template') . " to download a sample certificate\n";
    echo "2. Check the generated file in: storage/app/public/certificates/\n";
    echo "3. Test the preview page with a real ex-student\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
