<?php
/**
 * Certificate System Setup Script
 * Run this script to set up the certificate generation system
 */

echo "🎓 Setting up Certificate Generation System...\n\n";

// 1. Create required directories
$directories = [
    'storage/app/templates',
    'storage/app/public/certificates',
    'storage/app/public/temp'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Created directory: $dir\n";
    } else {
        echo "📁 Directory already exists: $dir\n";
    }
}

// 2. Check if required packages are installed
echo "\n📦 Checking required packages...\n";

$composerJson = json_decode(file_get_contents('composer.json'), true);
$requiredPackages = [
    'phpoffice/phpword',
    'simplesoftwareio/simple-qrcode'
];

foreach ($requiredPackages as $package) {
    if (isset($composerJson['require'][$package])) {
        echo "✅ Package installed: $package\n";
    } else {
        echo "❌ Package missing: $package\n";
        echo "   Run: composer require $package\n";
    }
}

// 3. Create sample template instructions
echo "\n📝 Template Setup Instructions:\n";
echo "1. Copy your Word template to: storage/app/templates/certificate_template.docx\n";
echo "2. Use these placeholders in your template:\n";
echo "   - {{STUDENT_NAME}}\n";
echo "   - {{COURSE_NAME}}\n";
echo "   - {{GRADUATION_DATE}}\n";
echo "   - {{CERTIFICATE_NUMBER}}\n";
echo "   - {{QR_CODE}} (for QR code image)\n\n";

// 4. Database migration instructions
echo "🗄️ Database Setup:\n";
echo "Run: php artisan migrate\n\n";

// 5. Test routes
echo "🔗 Available Routes:\n";
echo "- Generate certificate: /certificates/generate/{studentId}\n";
echo "- Download certificate: /certificates/download/{studentId}\n";
echo "- Verify certificate: /certificates/verify/{certificateNumber}\n";
echo "- List certificates: /certificates/\n\n";

// 6. QR Code content example
echo "📱 QR Code Content Example:\n";
$qrExample = [
    'student_name' => 'John Doe',
    'certificate_number' => 'CERT-2025-000001',
    'course' => 'Bachelor of Science (Hons) in ICT',
    'graduation_date' => '10 June 2019',
    'verification_url' => 'https://lms.olympia-education.com/verify-certificate/CERT-2025-000001',
    'generated_at' => date('c')
];

echo json_encode($qrExample, JSON_PRETTY_PRINT) . "\n\n";

echo "🎉 Setup complete! Follow the instructions above to complete the setup.\n";

