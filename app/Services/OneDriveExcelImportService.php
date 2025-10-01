<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class OneDriveExcelImportService
{
    protected $onedriveUrl;
    protected $lmsSheets;
    protected $tempFilePath;

    public function __construct()
    {
        $this->onedriveUrl = config('google_sheets.onedrive_url') ?: config('services.onedrive.excel_url') ?: env('ONEDRIVE_EXCEL_URL');
        
        // Define specific sheets by index (11-17) with their names
        // Process sheets in order, skipping empty ones
        $this->lmsSheets = [
            12 => 'IUC LMS', 
            13 => 'VIVA-IUC LMS',
            14 => 'LUC LMS',
            15 => 'EXECUTIVE LMS',
            16 => 'UPM LMS',
            17 => 'TVET LMS'
            // Skip sheet 11 (DHU LMS) as it's empty
        ];
        
        $this->tempFilePath = storage_path('app/temp_enrollment.xlsx');
    }

    public function importFromOneDrive()
    {
        Log::info('Starting OneDrive Excel import', [
            'onedrive_url' => $this->onedriveUrl,
            'temp_file_path' => $this->tempFilePath
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
            
            // Check file size for performance estimation
            if (file_exists($this->tempFilePath)) {
                $fileSize = filesize($this->tempFilePath);
                $fileSizeMB = round($fileSize / 1024 / 1024, 2);
                Log::info("Excel file downloaded successfully", [
                    'file_size_bytes' => $fileSize,
                    'file_size_mb' => $fileSizeMB,
                    'temp_file' => $this->tempFilePath
                ]);
                
                // Warn if file is very large
                if ($fileSizeMB > 50) {
                    Log::warning("Large Excel file detected - import may take longer", [
                        'file_size_mb' => $fileSizeMB
                    ]);
                }
            }

            // Process each LMS sheet by index with progress tracking
            $sheetCount = count($this->lmsSheets);
            $currentSheet = 0;
            
            foreach ($this->lmsSheets as $sheetIndex => $sheetName) {
                $currentSheet++;
                Log::info("Processing sheet {$currentSheet}/{$sheetCount} - Index {$sheetIndex}: {$sheetName}");
                
                // Add a small delay to prevent overwhelming the system
                if ($currentSheet > 1) {
                    usleep(100000); // 0.1 second delay between sheets
                }
                
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

                    // Use Laravel Excel to import the specific sheet by index
                    $import = new StudentsImport();
                    $import->setCurrentSheet($sheetName);
                    
                    // Import using Laravel Excel with specific sheet index
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

            Log::info('OneDrive Excel import completed', [
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
            Log::error('OneDrive Excel import failed', [
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
        
        // Method 1: Direct URL (for public links)
        $urls[] = $this->onedriveUrl;
        
        // Method 2: If URL already has download=1, try it first
        if (strpos($this->onedriveUrl, 'download=1') !== false) {
            array_unshift($urls, $this->onedriveUrl); // Put it first
        }
        
        // Method 2.5: Handle 1drv.ms URLs specifically
        if (strpos($this->onedriveUrl, '1drv.ms') !== false) {
            // Your URL: https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=G4v8Jw&download=1
            // This should work as-is, but let's try variations
            
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
        
        // Method 3: Convert to direct download format
        if (preg_match('/\/x\/c\/([a-zA-Z0-9_-]+)\/([a-zA-Z0-9_-]+)/', $this->onedriveUrl, $matches)) {
            $fileId = $matches[2];
            $urls[] = "https://1drv.ms/x/s!{$fileId}/download";
            $urls[] = "https://1drv.ms/x/s!{$fileId}";
        }
        
        // Method 4: Try alternative formats
        $urls[] = $this->getDirectDownloadUrl();
        $urls[] = $this->getAlternativeDownloadUrl();
        $urls[] = $this->getEmbedDownloadUrl();
        
        // Method 5: Try with different parameters
        if (strpos($this->onedriveUrl, '?') !== false) {
            $baseUrl = explode('?', $this->onedriveUrl)[0];
            $urls[] = $baseUrl . '/download';
            
            // If original URL has parameters, try adding download=1
            if (strpos($this->onedriveUrl, 'download=1') === false) {
                $urls[] = $this->onedriveUrl . '&download=1';
            }
        }
        
        return array_unique($urls);
    }

    private function getDirectDownloadUrl()
    {
        // Extract file ID from OneDrive URL
        $urlParts = parse_url($this->onedriveUrl);
        $pathParts = explode('/', $urlParts['path']);
        $fileId = end($pathParts);
        
        return "https://1drv.ms/x/s!{$fileId}/download";
    }

    private function getAlternativeDownloadUrl()
    {
        $urlParts = parse_url($this->onedriveUrl);
        $pathParts = explode('/', $urlParts['path']);
        $fileId = end($pathParts);
        
        return "https://1drv.ms/x/s!{$fileId}";
    }

    private function getEmbedDownloadUrl()
    {
        $urlParts = parse_url($this->onedriveUrl);
        $pathParts = explode('/', $urlParts['path']);
        $fileId = end($pathParts);
        
        return "https://1drv.ms/x/s!{$fileId}?e=choAKY&embed=0";
    }

    private function cleanupTempFile()
    {
        if (file_exists($this->tempFilePath)) {
            unlink($this->tempFilePath);
            Log::info('Temporary file cleaned up');
        }
    }

    public function testConnection()
    {
        try {
            $result = $this->downloadExcelFile();
            
            if ($result['success']) {
                $this->cleanupTempFile();
                return [
                    'success' => true,
                    'message' => 'Successfully connected to OneDrive Excel file'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $result['error'],
                    'message' => 'Failed to connect to OneDrive Excel file'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Connection test failed'
            ];
        }
    }
}
