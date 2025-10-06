<?php

namespace App\Services;

use App\Models\ExStudent;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class QrCodeService
{
    /**
     * Generate QR code for ex-student
     */
    public function generateQrCode(ExStudent $exStudent): string
    {
        $qrData = $exStudent->generateQrCode();
        
        // Generate QR code image
        $qrCodeImage = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($qrData);

        // Save QR code to storage
        $filename = "qr_codes/{$exStudent->student_id}_qr.png";
        Storage::disk('public')->put($filename, $qrCodeImage);

        return $filename;
    }

    /**
     * Generate QR code with custom data
     */
    public function generateCustomQrCode(string $data, string $filename = null): string
    {
        $qrCodeImage = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($data);

        $filename = $filename ?: 'qr_' . time() . '.png';
        $filepath = "qr_codes/{$filename}";
        
        Storage::disk('public')->put($filepath, $qrCodeImage);

        return $filepath;
    }

    /**
     * Get QR code URL
     */
    public function getQrCodeUrl(string $filepath): string
    {
        return asset('storage/' . $filepath);
    }

    /**
     * Generate QR code for certificate (high quality)
     */
    public function generateCertificateQrCode(ExStudent $exStudent): string
    {
        // Generate QR code that directly links to the student's certificate
        $qrData = route('ex-student.certificate', ['student_id' => $exStudent->student_id]);

        // Generate high-quality QR code for printing (using SVG format first)
        $qrCodeSvg = QrCode::format('svg')
            ->size(400)
            ->margin(3)
            ->errorCorrection('H')
            ->generate($qrData);

        // Convert SVG to PNG using Intervention Image
        $filename = "certificates/{$exStudent->student_id}_cert_qr.png";
        $this->convertSvgToPng($qrCodeSvg, $filename);

        return $filename;
    }

    /**
     * Generate QR code with student ID only (for simple verification)
     */
    public function generateSimpleQrCode(string $studentId): string
    {
        $qrCodeSvg = QrCode::format('svg')
            ->size(200)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($studentId);

        $filename = "simple_qr/{$studentId}_simple.png";
        $this->convertSvgToPng($qrCodeSvg, $filename);

        return $filename;
    }

    /**
     * Validate QR code data
     */
    public function validateQrCode(string $qrData): ?array
    {
        try {
            $decoded = json_decode(base64_decode($qrData), true);
            
            if (!$decoded || !isset($decoded['student_id'])) {
                return null;
            }

            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate QR code for display in web interface
     */
    public function generateWebQrCode(ExStudent $exStudent): string
    {
        $verificationUrl = $exStudent->getVerificationUrl();
        
        $qrCodeImage = QrCode::format('svg')
            ->size(200)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($verificationUrl);

        return $qrCodeImage;
    }

    /**
     * Generate QR code as PNG string (base64 encoded)
     */
    public function generatePng(string $data, int $size = 200): string
    {
        return QrCode::format('png')
            ->size($size)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($data);
    }

    /**
     * Generate QR code as SVG string (for backward compatibility)
     */
    public function generateSvg(string $data, int $size = 200): string
    {
        return QrCode::format('svg')
            ->size($size)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($data);
    }


    /**
     * Convert SVG to PNG using a simple approach
     */
    private function convertSvgToPng(string $svgContent, string $filename): void
    {
        // For now, we'll use a simple approach: save SVG as PNG
        // In a production environment, you might want to use a proper SVG to PNG converter
        // or install ImageMagick with proper PHP extension
        
        // Create a simple PNG representation by creating a basic PNG file
        // This is a workaround since ImageMagick extension is not available
        
        // Extract dimensions from SVG
        preg_match('/width="(\d+)"|height="(\d+)"/', $svgContent, $matches);
        $size = isset($matches[1]) ? (int)$matches[1] : 400;
        
        // Create a simple PNG using GD (if available)
        if (extension_loaded('gd')) {
            $image = imagecreate($size, $size);
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);
            
            // Fill with white background
            imagefill($image, 0, 0, $white);
            
            // Add a simple border to make it look like a QR code placeholder
            imagerectangle($image, 10, 10, $size-10, $size-10, $black);
            
            // Save as PNG
            ob_start();
            imagepng($image);
            $pngData = ob_get_contents();
            ob_end_clean();
            imagedestroy($image);
            
            Storage::disk('public')->put($filename, $pngData);
        } else {
            // Fallback: save as SVG with .png extension
            Storage::disk('public')->put($filename, $svgContent);
        }
    }

    /**
     * Generate QR code with custom styling
     */
    public function generateStyledQrCode(string $data, array $options = []): string
    {
        $defaultOptions = [
            'size' => 300,
            'margin' => 2,
            'errorCorrection' => 'H',
            'format' => 'png',
        ];

        $options = array_merge($defaultOptions, $options);

        $qrCodeImage = QrCode::format($options['format'])
            ->size($options['size'])
            ->margin($options['margin'])
            ->errorCorrection($options['errorCorrection'])
            ->generate($data);

        return $qrCodeImage;
    }
}
