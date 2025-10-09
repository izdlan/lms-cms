<?php

namespace App\Services;

use App\Models\ExStudent;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Paragraph;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CertificateService
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Generate Word certificate for ex-student
     */
    public function generateWordCertificate(ExStudent $exStudent): string
    {
        // Create new PhpWord object
        $phpWord = new PhpWord();
        
        // Set document properties
        $phpWord->getDocInfo()->setCreator('Olympia Education LMS');
        $phpWord->getDocInfo()->setTitle('Certificate - ' . $exStudent->name);
        $phpWord->getDocInfo()->setDescription('Official Certificate for ' . $exStudent->name);

        // Add a section
        $section = $phpWord->addSection([
            'marginTop' => 1134,    // 0.8 inch
            'marginBottom' => 1134, // 0.8 inch
            'marginLeft' => 1134,   // 0.8 inch
            'marginRight' => 1134,  // 0.8 inch
        ]);

        // Add certificate content
        $this->addCertificateHeader($section, $exStudent);
        $this->addCertificateBody($section, $exStudent);
        $this->addCertificateFooter($section, $exStudent);

        // Save the document
        $filename = "certificates/{$exStudent->student_id}_certificate_" . time() . ".docx";
        $filepath = storage_path('app/public/' . $filename);
        
        // Ensure directory exists
        $directory = dirname($filepath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filepath);

        return $filename;
    }

    /**
     * Add certificate header with logo and university name
     */
    private function addCertificateHeader($section, ExStudent $exStudent)
    {
        // University Logo (if exists)
        $logoPath = public_path('store/1/logo/OLYMPIA.png');
        if (file_exists($logoPath)) {
            $section->addImage($logoPath, [
                'width' => 100,
                'height' => 100,
                'alignment' => 'center',
                'marginTop' => 0,
                'marginBottom' => 20,
            ]);
        }

        // University Name
        $section->addText('OLYMPIA UNIVERSITY', [
            'name' => 'Times New Roman',
            'size' => 24,
            'bold' => true,
            'color' => '000000',
        ], [
            'alignment' => 'center',
            'spaceAfter' => 0,
        ]);

        $section->addText('OLYMPIA EDUCATION', [
            'name' => 'Times New Roman',
            'size' => 20,
            'bold' => true,
            'color' => '000000',
        ], [
            'alignment' => 'center',
            'spaceAfter' => 40,
        ]);
    }

    /**
     * Add certificate body with student information
     */
    private function addCertificateBody($section, ExStudent $exStudent)
    {
        // Declaration text (Malay)
        $section->addText('DENGAN KUASA YANG DIBERIKAN OLEH LEMBAGA AKADEMIK, ADALAH DIPERAKUI BAHAWA', [
            'name' => 'Times New Roman',
            'size' => 14,
            'color' => '000000',
        ], [
            'alignment' => 'center',
            'spaceAfter' => 20,
        ]);

        // Declaration text (English)
        $section->addText('By the authority of Academic Board it is certify that', [
            'name' => 'Times New Roman',
            'size' => 14,
            'color' => '000000',
        ], [
            'alignment' => 'center',
            'spaceAfter' => 30,
        ]);

        // Student Name (placeholder 1)
        $section->addText($exStudent->name, [
            'name' => 'Times New Roman',
            'size' => 18,
            'bold' => true,
            'color' => '000000',
        ], [
            'alignment' => 'center',
            'spaceAfter' => 10,
        ]);

        // Student ID (placeholder 2)
        $section->addText($exStudent->student_id, [
            'name' => 'Times New Roman',
            'size' => 14,
            'color' => '000000',
        ], [
            'alignment' => 'center',
            'spaceAfter' => 20,
        ]);

        // Award text (Malay)
        $section->addText('TELAH DIANUGERAHKAN / has been awarded the', [
            'name' => 'Times New Roman',
            'size' => 14,
            'color' => '000000',
        ], [
            'alignment' => 'center',
            'spaceAfter' => 20,
        ]);

        // Degree (placeholder 3)
        $degreeText = $exStudent->program ?? 'SARJANA MUDA EKSEKUTIF PENTADBIRAN PERNIAGAAN';
        $section->addText($degreeText, [
            'name' => 'Times New Roman',
            'size' => 16,
            'bold' => true,
            'color' => '000000',
        ], [
            'alignment' => 'center',
            'spaceAfter' => 10,
        ]);

        // Degree English (placeholder 4)
        $degreeEnglish = 'Bachelor Executive in Business Administration';
        $section->addText($degreeEnglish, [
            'name' => 'Times New Roman',
            'size' => 14,
            'color' => '000000',
        ], [
            'alignment' => 'center',
            'spaceAfter' => 20,
        ]);

        // Completion text
        $section->addText('SETELAH MEMENUHI SEMUA SYARAT YANG DITETAPKAN DAN DIKURNIAKAN IJAZAH PADA / having fulfilled all the requirements and has been conferred the degree at', [
            'name' => 'Times New Roman',
            'size' => 14,
            'color' => '000000',
        ], [
            'alignment' => 'center',
            'spaceAfter' => 20,
        ]);

        // Graduation Date (placeholder 5)
        $graduationDate = $this->formatGraduationDate($exStudent);
        $section->addText($graduationDate, [
            'name' => 'Times New Roman',
            'size' => 16,
            'bold' => true,
            'color' => '000000',
        ], [
            'alignment' => 'center',
            'spaceAfter' => 40,
        ]);
    }

    /**
     * Add certificate footer with signatures and QR codes
     */
    private function addCertificateFooter($section, ExStudent $exStudent)
    {
        // Create a table for signatures and QR codes
        $table = $section->addTable([
            'borderSize' => 0,
            'cellMargin' => 0,
        ]);

        $table->addRow();
        
        // Left cell for signatures
        $leftCell = $table->addCell(8000, [
            'valign' => 'top',
        ]);

        // Add signature section
        $this->addSignatureSection($leftCell, $exStudent);

        // Right cell for QR codes
        $rightCell = $table->addCell(4000, [
            'valign' => 'top',
        ]);

        // Add QR codes (placeholders 6, 7, 8)
        $this->addQrCodeSection($rightCell, $exStudent);

        // Add accreditation logos at bottom
        $this->addAccreditationLogos($section);
    }

    /**
     * Add signature section
     */
    private function addSignatureSection($cell, ExStudent $exStudent)
    {
        // Director signature
        $cell->addText('Assoc Prof Dr Mohd Kamil Yusoff', [
            'name' => 'Times New Roman',
            'size' => 12,
            'bold' => true,
        ], [
            'alignment' => 'left',
            'spaceAfter' => 5,
        ]);

        $cell->addText('Director, Olympia Education', [
            'name' => 'Times New Roman',
            'size' => 10,
        ], [
            'alignment' => 'left',
            'spaceAfter' => 20,
        ]);

        // Chairman signature
        $cell->addText('Brigadier General (R) Professor Dato Ts Dr. Hj. Shohaimi Abdullah', [
            'name' => 'Times New Roman',
            'size' => 12,
            'bold' => true,
        ], [
            'alignment' => 'left',
            'spaceAfter' => 5,
        ]);

        $cell->addText('Chairman, Olympia Academic Board', [
            'name' => 'Times New Roman',
            'size' => 10,
        ], [
            'alignment' => 'left',
            'spaceAfter' => 20,
        ]);
    }

    /**
     * Add QR code section
     */
    private function addQrCodeSection($cell, ExStudent $exStudent)
    {
        // Generate QR codes
        $qrCodePath1 = $this->generateQrCodeForCertificate($exStudent, 'verification');
        $qrCodePath2 = $this->generateQrCodeForCertificate($exStudent, 'certificate');
        
        // Add QR codes
        if ($qrCodePath1) {
            $cell->addImage($qrCodePath1, [
                'width' => 80,
                'height' => 80,
                'alignment' => 'center',
                'marginTop' => 10,
                'marginBottom' => 10,
            ]);
        }

        if ($qrCodePath2) {
            $cell->addImage($qrCodePath2, [
                'width' => 80,
                'height' => 80,
                'alignment' => 'center',
                'marginTop' => 10,
                'marginBottom' => 10,
            ]);
        }

        // Certificate number (placeholder 8)
        $cell->addText($exStudent->certificate_number ?? 'CERT-' . time(), [
            'name' => 'Times New Roman',
            'size' => 10,
            'bold' => true,
        ], [
            'alignment' => 'center',
            'spaceAfter' => 5,
        ]);
    }

    /**
     * Add accreditation logos
     */
    private function addAccreditationLogos($section)
    {
        $section->addTextBreak(2);

        // Create table for logos
        $logoTable = $section->addTable([
            'borderSize' => 0,
            'cellMargin' => 0,
        ]);

        $logoTable->addRow();

        // MQA Logo
        $mqaCell = $logoTable->addCell(2000, ['valign' => 'center']);
        $mqaLogoPath = public_path('store/1/logo/MQA.png');
        if (file_exists($mqaLogoPath)) {
            $mqaCell->addImage($mqaLogoPath, [
                'width' => 60,
                'height' => 60,
                'alignment' => 'center',
            ]);
        }

        // CMI Logo
        $cmiCell = $logoTable->addCell(2000, ['valign' => 'center']);
        $cmiLogoPath = public_path('store/1/logo/CMI.png');
        if (file_exists($cmiLogoPath)) {
            $cmiCell->addImage($cmiLogoPath, [
                'width' => 60,
                'height' => 60,
                'alignment' => 'center',
            ]);
        }

        // CTH Logo
        $cthCell = $logoTable->addCell(2000, ['valign' => 'center']);
        $cthLogoPath = public_path('store/1/logo/CTH.png');
        if (file_exists($cthLogoPath)) {
            $cthCell->addImage($cthLogoPath, [
                'width' => 60,
                'height' => 60,
                'alignment' => 'center',
            ]);
        }

        // Ministry Logo
        $ministryCell = $logoTable->addCell(2000, ['valign' => 'center']);
        $ministryLogoPath = public_path('store/1/logo/Ministry.png');
        if (file_exists($ministryLogoPath)) {
            $ministryCell->addImage($ministryLogoPath, [
                'width' => 60,
                'height' => 60,
                'alignment' => 'center',
            ]);
        }
    }

    /**
     * Generate QR code for certificate
     */
    private function generateQrCodeForCertificate(ExStudent $exStudent, string $type): ?string
    {
        try {
            $qrData = $this->getQrCodeData($exStudent, $type);
            $qrCodeImage = $this->qrCodeService->generateStyledQrCode($qrData, [
                'size' => 200,
                'format' => 'png',
                'errorCorrection' => 'H',
            ]);

            // Save QR code temporarily
            $filename = "temp_qr_{$exStudent->student_id}_{$type}_" . time() . ".png";
            $filepath = storage_path('app/temp/' . $filename);
            
            // Ensure temp directory exists
            $tempDir = dirname($filepath);
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            file_put_contents($filepath, $qrCodeImage);
            
            return $filepath;
        } catch (\Exception $e) {
            Log::error("QR Code generation failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get QR code data based on type
     */
    private function getQrCodeData(ExStudent $exStudent, string $type): string
    {
        switch ($type) {
            case 'verification':
                return $exStudent->getVerificationUrl();
            case 'certificate':
                return json_encode([
                    'student_id' => $exStudent->student_id,
                    'certificate_number' => $exStudent->certificate_number,
                    'name' => $exStudent->name,
                    'program' => $exStudent->program,
                    'graduation_date' => $exStudent->getFormattedGraduationDate(),
                ]);
            default:
                return $exStudent->student_id;
        }
    }

    /**
     * Format graduation date
     */
    private function formatGraduationDate(ExStudent $exStudent): string
    {
        if ($exStudent->graduation_year && $exStudent->graduation_month) {
            $date = Carbon::createFromFormat('Y-m', $exStudent->graduation_year . '-' . $exStudent->graduation_month);
            return $date->format('jS \of F Y');
        }
        
        return 'Twenty-Ninth day of August 2025'; // Default date
    }

    /**
     * Generate PDF certificate (alternative to Word)
     */
    public function generatePdfCertificate(ExStudent $exStudent): string
    {
        // This would use a PDF library like DomPDF or TCPDF
        // For now, we'll return the Word document path
        return $this->generateWordCertificate($exStudent);
    }

    /**
     * Clean up temporary files
     */
    public function cleanupTempFiles()
    {
        $tempDir = storage_path('app/temp');
        if (is_dir($tempDir)) {
            $files = glob($tempDir . '/temp_qr_*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
}
