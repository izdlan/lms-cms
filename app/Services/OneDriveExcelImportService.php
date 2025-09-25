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
        $this->onedriveUrl = config('google_sheets.onedrive_url', 'https://1drv.ms/x/c/57E7A472BE891FFC/ESIdV_VbTeJBhvgxy-itpXcBcmwc-9gKw1B4XayoEDPi7w?e=choAKY');
        $this->lmsSheets = config('google_sheets.lms_sheets', [
            'DHU LMS' => 'DHU LMS',
            'IUC LMS' => 'IUC LMS',
            'VIVA-IUC LMS' => 'VIVA-IUC LMS',
            'LUC LMS' => 'LUC LMS',
            'EXECUTIVE LMS' => 'EXECUTIVE LMS',
            'UPM LMS' => 'UPM LMS',
            'TVET LMS' => 'TVET LMS'
        ]);
        $this->tempFilePath = storage_path('app/temp_enrollment.xlsx');
    }

    public function importFromOneDrive()
    {
        Log::info('Starting OneDrive Excel import');
        
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $processedSheets = [];

        try {
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

            // Process each LMS sheet
            foreach ($this->lmsSheets as $sheetName => $sheetIdentifier) {
                Log::info("Processing sheet: {$sheetName}");
                
                try {
                    $import = new StudentsImport();
                    $import->setCurrentSheet($sheetName);
                    
                    // Use the correct method to import a specific sheet
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                    $reader->setLoadSheetsOnly([$sheetName]);
                    $spreadsheet = $reader->load($this->tempFilePath);
                    $worksheet = $spreadsheet->getSheetByName($sheetName);
                    
                    if ($worksheet) {
                        $import->collection(collect($worksheet->toArray()));
                    } else {
                        throw new \Exception("Sheet '{$sheetName}' not found");
                    }
                    
                    $stats = $import->getStats();
                    $created = $stats['created'];
                    $updated = $stats['updated'];
                    $errors = $stats['errors'];
                    
                    $totalCreated += $created;
                    $totalUpdated += $updated;
                    $totalErrors += $errors;
                    
                    $processedSheets[] = [
                        'sheet' => $sheetName,
                        'created' => $created,
                        'updated' => $updated,
                        'errors' => $errors
                    ];
                    
                    Log::info("Completed processing sheet: {$sheetName}", [
                        'created' => $created,
                        'updated' => $updated,
                        'errors' => $errors
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error("Error processing sheet {$sheetName}", [
                        'error' => $e->getMessage()
                    ]);
                    $totalErrors++;
                    $processedSheets[] = [
                        'sheet' => $sheetName,
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

    private function downloadExcelFile(): array
    {
        try {
            Log::info('Downloading Excel file from OneDrive', [
                'url' => $this->onedriveUrl
            ]);

            // Try multiple URL conversion methods
            $downloadUrls = $this->getDownloadUrls();
            $response = null;
            
            foreach ($downloadUrls as $url) {
                Log::info('Trying download URL', ['url' => $url]);
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,*/*',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ])->timeout(30)->get($url);
                
                if ($response->successful()) {
                    Log::info('Successfully downloaded from URL', ['url' => $url]);
                    break;
                } else {
                    Log::warning('Failed to download from URL', [
                        'url' => $url,
                        'status' => $response->status()
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
