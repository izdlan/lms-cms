<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class OptimizedOneDriveImportService
{
    protected $onedriveUrl;
    protected $lmsSheets;
    protected $tempFilePath;

    public function __construct()
    {
        $this->onedriveUrl = config('google_sheets.onedrive_url') ?: config('services.onedrive.excel_url') ?: env('ONEDRIVE_EXCEL_URL');
        
        // Process specific sheets by index as requested
        $this->lmsSheets = [
            11 => 'DHU LMS',
            12 => 'IUC LMS', 
            14 => 'LUC LMS',
            15 => 'EXECUTIVE LMS',
            16 => 'UPM LMS',
            17 => 'TVET LMS'
        ];
        
        $this->tempFilePath = storage_path('app/temp_enrollment.xlsx');
    }

    public function importFromOneDrive()
    {
        Log::info('Starting optimized OneDrive Excel import', [
            'onedrive_url' => $this->onedriveUrl,
            'sheets_to_process' => count($this->lmsSheets)
        ]);
        
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $processedSheets = [];

        try {
            // Check if OneDrive URL is configured
            if (empty($this->onedriveUrl)) {
                return [
                    'success' => false,
                    'error' => 'OneDrive URL is not configured. Please set ONEDRIVE_EXCEL_URL in your .env file.',
                    'created' => 0,
                    'updated' => 0,
                    'errors' => 1,
                    'processed_sheets' => []
                ];
            }

            // Download the Excel file from OneDrive
            $downloadResult = $this->downloadExcelFile();
            if (!$downloadResult['success']) {
                return [
                    'success' => false,
                    'error' => $downloadResult['error'],
                    'created' => 0,
                    'updated' => 0,
                    'errors' => 1,
                    'processed_sheets' => []
                ];
            }

            // Process each LMS sheet with progress tracking
            $sheetCount = count($this->lmsSheets);
            $currentSheet = 0;
            
            foreach ($this->lmsSheets as $sheetIndex => $sheetName) {
                $currentSheet++;
                Log::info("Processing sheet {$currentSheet}/{$sheetCount} - Index {$sheetIndex}: {$sheetName}");
                
                try {
                    // Check if temp file exists
                    if (!file_exists($this->tempFilePath)) {
                        Log::error("Temp file not found: {$this->tempFilePath}");
                        $totalErrors++;
                        $processedSheets[] = [
                            'sheet' => $sheetName,
                            'sheet_index' => $sheetIndex,
                            'created' => 0,
                            'updated' => 0,
                            'errors' => 1
                        ];
                        continue;
                    }

                    // Use Laravel Excel to import the specific sheet
                    $import = new StudentsImport();
                    $import->setCurrentSheet($sheetName);
                    
                    // Import using Laravel Excel with sheet-specific processing
                    // Note: We'll process all sheets but filter by sheet name in the import class
                    Excel::import($import, $this->tempFilePath, null, \Maatwebsite\Excel\Excel::XLSX);
                    
                    $stats = $import->getStats();
                    $created = $stats['created'];
                    $updated = $stats['updated'];
                    $errors = $stats['errors'];
                    
                    $totalCreated += $created;
                    $totalUpdated += $updated;
                    $totalErrors += $errors;
                    
                    $processedSheets[] = [
                        'sheet' => $sheetName,
                        'sheet_index' => $sheetIndex,
                        'created' => $created,
                        'updated' => $updated,
                        'errors' => $errors
                    ];
                    
                    Log::info("Completed sheet {$currentSheet}/{$sheetCount} - Index {$sheetIndex}: {$sheetName}", [
                        'created' => $created,
                        'updated' => $updated,
                        'errors' => $errors,
                        'progress' => round(($currentSheet / $sheetCount) * 100, 1) . '%'
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error("Error processing sheet {$currentSheet}/{$sheetCount} - Index {$sheetIndex}: {$sheetName}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $totalErrors++;
                    $processedSheets[] = [
                        'sheet' => $sheetName,
                        'sheet_index' => $sheetIndex,
                        'created' => 0,
                        'updated' => 0,
                        'errors' => 1
                    ];
                }
            }

            // Clean up temporary file
            $this->cleanupTempFile();

            Log::info('Optimized OneDrive Excel import completed', [
                'total_created' => $totalCreated,
                'total_updated' => $totalUpdated,
                'total_errors' => $totalErrors,
                'processed_sheets' => $processedSheets
            ]);

            return [
                'success' => true,
                'created' => $totalCreated,
                'updated' => $totalUpdated,
                'errors' => $totalErrors,
                'processed_sheets' => $processedSheets
            ];

        } catch (\Exception $e) {
            Log::error('Optimized OneDrive Excel import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->cleanupTempFile();

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'created' => $totalCreated,
                'updated' => $totalUpdated,
                'errors' => $totalErrors + 1,
                'processed_sheets' => $processedSheets
            ];
        }
    }

    public function downloadExcelFile(): array
    {
        try {
            Log::info('Downloading Excel file from OneDrive', [
                'url' => $this->onedriveUrl
            ]);

            // Try multiple URL conversion methods
            $downloadUrls = $this->getDownloadUrls();
            $response = null;
            
            foreach ($downloadUrls as $index => $url) {
                Log::info('Trying download URL ' . ($index + 1) . '/' . count($downloadUrls), [
                    'url' => $url,
                    'url_type' => strpos($url, '1drv.ms') !== false ? '1drv.ms' : 'other'
                ]);
                
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,*/*',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ])->timeout(30)->get($url);
                
                if ($response->successful()) {
                    Log::info('Successfully downloaded from URL', [
                        'url' => $url,
                        'status' => $response->status(),
                        'content_type' => $response->header('Content-Type'),
                        'content_length' => $response->header('Content-Length')
                    ]);
                    break;
                } else {
                    Log::warning('Failed to download from URL', [
                        'url' => $url,
                        'status' => $response->status(),
                        'response_preview' => substr($response->body(), 0, 200) . '...',
                        'headers' => $response->headers()
                    ]);
                }
            }
            
            if ($response->successful()) {
                $content = $response->body();
                
                // Validate that it's an Excel file
                if (strpos($content, 'PK') === 0) {
                    file_put_contents($this->tempFilePath, $content);
                    
                    Log::info('Excel file downloaded successfully', [
                        'size' => strlen($content),
                        'path' => $this->tempFilePath
                    ]);
                    
                    return ['success' => true];
                } else {
                    Log::warning('Downloaded content is not a valid Excel file', [
                        'first_100_chars' => substr($content, 0, 100)
                    ]);
                    
                    return [
                        'success' => false,
                        'error' => 'Downloaded content is not a valid Excel file'
                    ];
                }
            } else {
                Log::error('Failed to download Excel file', [
                    'status' => $response->status(),
                    'url' => $this->onedriveUrl
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Failed to download Excel file. Status: ' . $response->status()
                ];
            }

        } catch (\Exception $e) {
            Log::error('Error downloading Excel file', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Download error: ' . $e->getMessage()
            ];
        }
    }

    private function getDownloadUrls()
    {
        $urls = [];
        
        // Method 1: Use the original URL as-is
        if (!empty($this->onedriveUrl)) {
            array_unshift($urls, $this->onedriveUrl); // Put it first
        }
        
        // Method 2: Handle 1drv.ms URLs specifically
        if (strpos($this->onedriveUrl, '1drv.ms') !== false) {
            // Try without the e parameter
            $urlWithoutE = preg_replace('/[?&]e=[^&]*/', '', $this->onedriveUrl);
            if ($urlWithoutE !== $this->onedriveUrl) {
                $urls[] = $urlWithoutE;
            }
            
            // Try with different download parameters
            $baseUrl = preg_replace('/[?&]download=1/', '', $this->onedriveUrl);
            $urls[] = $baseUrl . '?download=1';
            $urls[] = $baseUrl . '?e=download';
        }
        
        return $urls;
    }

    private function cleanupTempFile()
    {
        if (file_exists($this->tempFilePath)) {
            unlink($this->tempFilePath);
            Log::info('Temporary file cleaned up', ['path' => $this->tempFilePath]);
        }
    }
}
