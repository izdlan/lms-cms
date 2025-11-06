<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CertificateConverter
{
    /**
     * Convert DOCX file to PDF using ConvertAPI
     * 
     * @param string $inputPath Full path to input DOCX file
     * @param string $outputPath Relative path for output PDF (will be saved to storage/app/)
     * @return string|null Full path to converted PDF file, or null if conversion failed
     */
    public static function docxToPdf($inputPath, $outputPath = null)
    {
        $secret = config('services.convertapi.secret');
        
        if (!$secret) {
            Log::warning('ConvertAPI secret not configured');
            return null;
        }
        
        if (!file_exists($inputPath)) {
            Log::error('ConvertAPI: Input file does not exist', ['path' => $inputPath]);
            return null;
        }
        
        try {
            // Upload DOCX file and convert to PDF using Bearer token authentication
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => "Bearer {$secret}"
                ])
                ->asMultipart()
                ->post(
                    "https://v2.convertapi.com/convert/docx/to/pdf",
                    [
                        [
                            'name' => 'File',
                            'contents' => fopen($inputPath, 'r'),
                            'filename' => basename($inputPath),
                        ],
                        [
                            'name' => 'StoreFile',
                            'contents' => 'true',
                        ]
                    ]
                );
            
            if ($response->failed()) {
                Log::error('ConvertAPI conversion failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'input_path' => $inputPath
                ]);
                return null;
            }
            
            $result = $response->json();
            
            if (!isset($result['Files']) || empty($result['Files'])) {
                Log::error('ConvertAPI: No files returned in response', ['result' => $result]);
                return null;
            }
            
            $pdfUrl = $result['Files'][0]['Url'];
            
            // Download the converted PDF
            $pdfContent = Http::timeout(60)->get($pdfUrl)->body();
            
            if (empty($pdfContent) || substr($pdfContent, 0, 4) !== '%PDF') {
                Log::error('ConvertAPI: Downloaded content is not a valid PDF', [
                    'content_length' => strlen($pdfContent),
                    'content_preview' => substr($pdfContent, 0, 50)
                ]);
                return null;
            }
            
            // Generate output path if not provided
            if (!$outputPath) {
                $outputPath = 'temp/' . basename($inputPath, '.docx') . '_' . time() . '.pdf';
            }
            
            // Save PDF to storage
            $fullOutputPath = storage_path('app/' . $outputPath);
            $directory = dirname($fullOutputPath);
            if (!is_dir($directory)) {
                @mkdir($directory, 0755, true);
            }
            
            file_put_contents($fullOutputPath, $pdfContent);
            
            Log::info('ConvertAPI: PDF conversion successful', [
                'input_path' => $inputPath,
                'output_path' => $fullOutputPath,
                'pdf_size' => strlen($pdfContent)
            ]);
            
            return $fullOutputPath;
            
        } catch (\Exception $e) {
            Log::error('ConvertAPI conversion exception', [
                'error' => $e->getMessage(),
                'input_path' => $inputPath,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}

