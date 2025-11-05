<?php

/**
 * Test DOCX File Structure
 * This script opens a generated DOCX file and checks its XML structure
 * 
 * Usage: php test_docx_file.php <path_to_docx_file>
 */

if ($argc < 2) {
    echo "Usage: php test_docx_file.php <path_to_docx_file>\n";
    echo "Example: php test_docx_file.php storage/app/certificates/certificate_UPBB988054_1762322655.docx\n";
    exit(1);
}

$docxPath = $argv[1];

if (!file_exists($docxPath)) {
    echo "Error: File not found: {$docxPath}\n";
    exit(1);
}

echo "=== DOCX File Analysis ===\n\n";
echo "File: {$docxPath}\n";
echo "Size: " . number_format(filesize($docxPath)) . " bytes\n\n";

// Open as ZIP
$zip = new ZipArchive();
$result = $zip->open($docxPath);

if ($result !== true) {
    echo "Error: Cannot open DOCX file as ZIP archive. Error code: {$result}\n";
    exit(1);
}

echo "✓ File is a valid ZIP archive\n\n";

// Check for required DOCX files
$requiredFiles = [
    '[Content_Types].xml',
    'word/document.xml',
    'word/_rels/document.xml.rels',
    '_rels/.rels'
];

echo "Checking required DOCX files:\n";
foreach ($requiredFiles as $file) {
    if ($zip->locateName($file) !== false) {
        echo "  ✓ {$file}\n";
    } else {
        echo "  ✗ {$file} MISSING\n";
    }
}

echo "\n";

// Read and validate document.xml
echo "=== Validating document.xml ===\n";
$documentXml = $zip->getFromName('word/document.xml');

if ($documentXml === false) {
    echo "Error: Cannot read word/document.xml\n";
    $zip->close();
    exit(1);
}

echo "Document XML size: " . strlen($documentXml) . " bytes\n\n";

// Validate XML
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$loaded = @$dom->loadXML($documentXml);

if ($loaded) {
    echo "✓ XML is valid\n\n";
} else {
    echo "✗ XML is INVALID\n";
    echo "XML Errors:\n";
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
        echo "  Line {$error->line}: {$error->message}\n";
    }
    echo "\n";
    
    // Show problematic area
    $lines = explode("\n", $documentXml);
    if (isset($errors[0]) && isset($lines[$errors[0]->line - 1])) {
        echo "Problematic line ({$errors[0]->line}):\n";
        echo "  " . htmlspecialchars($lines[$errors[0]->line - 1]) . "\n";
    }
    echo "\n";
}

libxml_clear_errors();

// Search for unescaped ampersands
echo "=== Checking for unescaped ampersands ===\n";
$unescapedAmpersands = preg_match_all('/&(?!(?:amp|lt|gt|quot|apos|#\d+|#x[0-9a-fA-F]+);)/', $documentXml, $matches);
if ($unescapedAmpersands > 0) {
    echo "✗ Found {$unescapedAmpersands} unescaped ampersand(s)\n";
    foreach ($matches[0] as $match) {
        echo "  Found: {$match}\n";
    }
    echo "\n";
} else {
    echo "✓ No unescaped ampersands found\n\n";
}

// Search for common problematic patterns
echo "=== Checking for problematic content ===\n";
$problematicPatterns = [
    '/&(?!(?:amp|lt|gt|quot|apos|#\d+|#x[0-9a-fA-F]+);)/' => 'Unescaped ampersand',
    '/<[^>]*[^\x20-\x7E]/' => 'Non-ASCII in tags',
    '/[\x00-\x08\x0B\x0C\x0E-\x1F]/' => 'Control characters',
];

foreach ($problematicPatterns as $pattern => $description) {
    if (preg_match($pattern, $documentXml)) {
        echo "⚠ Found: {$description}\n";
    } else {
        echo "✓ No {$description}\n";
    }
}

echo "\n";

// Extract text content to see what's actually in the document
echo "=== Document Content Preview ===\n";
$textNodes = [];
if ($loaded) {
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
    $textElements = $xpath->query('//w:t');
    
    foreach ($textElements as $textElement) {
        $text = trim($textElement->nodeValue);
        if (!empty($text) && strlen($text) > 3) {
            $textNodes[] = $text;
        }
    }
    
    echo "Found " . count($textNodes) . " text elements\n";
    if (count($textNodes) > 0) {
        echo "Sample text content:\n";
        foreach (array_slice($textNodes, 0, 10) as $text) {
            echo "  - " . htmlspecialchars(substr($text, 0, 100)) . "\n";
        }
    }
}

$zip->close();

echo "\n=== Analysis Complete ===\n";

