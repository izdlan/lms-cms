<?php
/**
 * Create a placeholder Word template for testing
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Creating placeholder Word template...\n";

// Create the templates directory if it doesn't exist
$templateDir = storage_path('app/templates');
if (!is_dir($templateDir)) {
    mkdir($templateDir, 0755, true);
    echo "Created templates directory: $templateDir\n";
}

// Create a simple placeholder template
$templatePath = $templateDir . '/certificate_template.docx';

// For now, create a simple text file as placeholder
$placeholderContent = "This is a placeholder for the Word template.\n";
$placeholderContent .= "Please replace this file with your actual Word template.\n";
$placeholderContent .= "The template should contain these placeholders:\n";
$placeholderContent .= "- {{STUDENT_NAME}}\n";
$placeholderContent .= "- {{COURSE_NAME}}\n";
$placeholderContent .= "- {{GRADUATION_DATE}}\n";
$placeholderContent .= "- {{CERTIFICATE_NUMBER}}\n";
$placeholderContent .= "- {{QR_CODE}}\n";

file_put_contents($templatePath, $placeholderContent);

echo "Created placeholder template at: $templatePath\n";
echo "Please replace this with your actual Word template.\n";

