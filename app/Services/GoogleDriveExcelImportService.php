<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class GoogleDriveExcelImportService
{
    protected $googleDriveUrl;
    protected $lmsSheets;
    protected $tempFilePath;

    public function __construct()
    {
        $this->googleDriveUrl = config('google_sheets.google_drive_url');
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

    public function importFromGoogleDrive()
    {
        Log::info('Starting Google Drive Excel import');
        
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $processedSheets = [];

        try {
            // Download the Excel file from Google Drive
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
                    Excel::import($import, $this->tempFilePath, $sheetName);
                    
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

            Log::info('Google Drive Excel import completed', [
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
            Log::error('Google Drive Excel import failed', [
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

    private function downloadExcelFile()
    {
        try {
            Log::info('Downloading Excel file from Google Drive', [
                'url' => $this->googleDriveUrl
            ]);

            // Convert Google Drive sharing URL to direct download URL
            $directUrl = $this->getDirectDownloadUrl();
            
            $response = Http::timeout(30)->get($directUrl);
            
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
                    return [
                        'success' => false,
                        'error' => 'Downloaded content is not a valid Excel file'
                    ];
                }
            } else {
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

    private function getDirectDownloadUrl()
    {
        // Convert Google Drive sharing URL to direct download URL
        // Format: https://drive.google.com/file/d/FILE_ID/view?usp=sharing
        // To: https://drive.google.com/uc?export=download&id=FILE_ID
        
        if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $this->googleDriveUrl, $matches)) {
            $fileId = $matches[1];
            return "https://drive.google.com/uc?export=download&id={$fileId}";
        }
        
        return $this->googleDriveUrl;
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
                    'message' => 'Successfully connected to Google Drive Excel file'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $result['error'],
                    'message' => 'Failed to connect to Google Drive Excel file'
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

