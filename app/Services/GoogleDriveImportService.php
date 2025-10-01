<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\SyncActivity;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GoogleDriveImportService
{
    protected $googleDriveUrl;
    protected $lmsSheets;
    protected $tempFilePath;

    public function __construct()
    {
        // Get Google Drive URL from environment
        $this->googleDriveUrl = env('GOOGLE_DRIVE_EXCEL_URL');
        
        // Process specific sheets by index (only the 6 required sheets)
        $this->lmsSheets = [
            10 => 'DHU LMS',        // Sheet 10
            11 => 'IUC LMS',        // Sheet 11
            13 => 'LUC LMS',        // Sheet 13
            14 => 'EXECUTIVE LMS',  // Sheet 14
            15 => 'UPM LMS',        // Sheet 15
            16 => 'TVET LMS'        // Sheet 16
        ];
        
        $this->tempFilePath = storage_path('app/temp_google_drive_enrollment.xlsx');
    }

    public function importFromGoogleDrive()
    {
        Log::info('Starting Google Drive Excel import', [
            'google_drive_url' => $this->googleDriveUrl,
            'sheets_to_process' => count($this->lmsSheets)
        ]);
        
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $processedSheets = [];

        try {
            // Check if Google Drive URL is configured
            if (empty($this->googleDriveUrl)) {
                return [
                    'success' => false,
                    'message' => 'Google Drive URL is not configured. Please set GOOGLE_DRIVE_EXCEL_URL in your .env file.',
                    'created' => 0,
                    'updated' => 0,
                    'errors' => 1,
                    'processed_sheets' => []
                ];
            }

            // Download the Excel file from Google Drive
            $localFile = $this->downloadFileFromGoogleDrive($this->googleDriveUrl);
            
            if (!$localFile) {
                return [
                    'success' => false,
                    'message' => 'Failed to download file from Google Drive',
                    'created' => 0,
                    'updated' => 0,
                    'errors' => 1,
                    'processed_sheets' => []
                ];
            }

            // Process each specific sheet
            foreach ($this->lmsSheets as $sheetIndex => $sheetName) {
                Log::info("Processing Google Drive sheet {$sheetIndex}: {$sheetName}");
                
                try {
                    $result = $this->processSheetData($localFile, $sheetIndex, $sheetName);
                    
                    $totalCreated += $result['created'];
                    $totalUpdated += $result['updated'];
                    $totalErrors += $result['errors'];
                    
                    $processedSheets[] = [
                        'sheet' => $sheetName,
                        'sheet_index' => $sheetIndex,
                        'created' => $result['created'],
                        'updated' => $result['updated'],
                        'errors' => $result['errors']
                    ];
                    
                } catch (\Exception $e) {
                    Log::error("Error processing Google Drive sheet {$sheetName}", [
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
            
            // Clean up the temporary file
            if (file_exists($localFile)) {
                unlink($localFile);
            }
            
            Log::info('Google Drive import completed', [
                'total_created' => $totalCreated,
                'total_updated' => $totalUpdated,
                'total_errors' => $totalErrors,
                'processed_sheets' => $processedSheets
            ]);
            
            // Log the activity to database
            SyncActivity::logActivity('import', 'success', "Google Drive import completed. Created: {$totalCreated}, Updated: {$totalUpdated}, Errors: {$totalErrors}", [
                'created' => $totalCreated,
                'updated' => $totalUpdated,
                'errors' => $totalErrors,
                'processed_sheets' => $processedSheets,
                'source' => 'google_drive'
            ]);

            return [
                'success' => true,
                'message' => "Google Drive import completed. Created: {$totalCreated}, Updated: {$totalUpdated}, Errors: {$totalErrors}",
                'created' => $totalCreated,
                'updated' => $totalUpdated,
                'errors' => $totalErrors,
                'processed_sheets' => $processedSheets
            ];
            
        } catch (\Exception $e) {
            Log::error('Google Drive import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Log error activity to database
            SyncActivity::logActivity('import', 'error', 'Google Drive import failed: ' . $e->getMessage(), [
                'created' => 0,
                'updated' => 0,
                'errors' => 1,
                'processed_sheets' => [],
                'source' => 'google_drive'
            ]);
            
            return [
                'success' => false,
                'message' => 'Google Drive import failed: ' . $e->getMessage(),
                'created' => 0,
                'updated' => 0,
                'errors' => 1,
                'processed_sheets' => []
            ];
        }
    }

    private function downloadFileFromGoogleDrive($url)
    {
        try {
            Log::info('Downloading file from Google Drive', ['url' => $url]);
            
            // Convert Google Drive sharing URL to direct download URL
            $directUrl = $this->convertToDirectDownloadUrl($url);
            
            Log::info('Using direct download URL', ['direct_url' => $directUrl]);
            
            // Download the file
            $response = Http::timeout(60)->get($directUrl);
            
            if ($response->successful()) {
                // Save to temporary file
                file_put_contents($this->tempFilePath, $response->body());
                
                Log::info('File downloaded successfully', [
                    'file_size' => filesize($this->tempFilePath),
                    'temp_file' => $this->tempFilePath
                ]);
                
                return $this->tempFilePath;
            } else {
                Log::error('Failed to download file from Google Drive', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Error downloading file from Google Drive', [
                'error' => $e->getMessage(),
                'url' => $url
            ]);
            return false;
        }
    }

    private function convertToDirectDownloadUrl($url)
    {
        // Convert Google Sheets URL to Excel export URL
        // Format: https://docs.google.com/spreadsheets/d/SPREADSHEET_ID/edit?usp=sharing
        // To: https://docs.google.com/spreadsheets/d/SPREADSHEET_ID/export?format=xlsx
        
        if (preg_match('/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            $spreadsheetId = $matches[1];
            return "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=xlsx";
        }
        
        // Convert Google Drive sharing URL to direct download URL
        // Format: https://drive.google.com/file/d/FILE_ID/view?usp=sharing
        // To: https://drive.google.com/uc?export=download&id=FILE_ID
        
        if (preg_match('/\/file\/d\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            $fileId = $matches[1];
            return "https://drive.google.com/uc?export=download&id=" . $fileId;
        }
        
        // If it's already a direct download URL, return as is
        if (strpos($url, 'uc?export=download') !== false || strpos($url, 'export?format=xlsx') !== false) {
            return $url;
        }
        
        // If it's a different format, try to extract file ID
        if (preg_match('/id=([a-zA-Z0-9-_]+)/', $url, $matches)) {
            $fileId = $matches[1];
            return "https://drive.google.com/uc?export=download&id=" . $fileId;
        }
        
        Log::warning('Could not convert URL to direct download format', ['url' => $url]);
        return $url; // Return original URL as fallback
    }

    private function processSheetData($filePath, $sheetIndex, $sheetName)
    {
        $created = 0;
        $updated = 0;
        $errors = 0;

        try {
            // Use a more memory-efficient approach for large files
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $reader->setReadEmptyCells(false);
            
            $spreadsheet = $reader->load($filePath);
            
            // Get the specific sheet by index
            if ($spreadsheet->getSheetCount() <= $sheetIndex) {
                Log::warning("Sheet index {$sheetIndex} not found in file", ['sheet_name' => $sheetName]);
                return ['created' => 0, 'updated' => 0, 'errors' => 1];
            }
            
            $worksheet = $spreadsheet->getSheet($sheetIndex);
            
            // Get the highest row and column
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            
            Log::info("Processing Google Drive sheet data", [
                'sheet' => $sheetName,
                'highest_row' => $highestRow,
                'highest_column' => $highestColumn
            ]);
            
            // Read data in chunks to avoid memory issues
            $headerRowIndex = -1;
            $headerRow = [];
            
            // Find header row by reading first 10 rows
            for ($row = 1; $row <= min(10, $highestRow); $row++) {
                $rowData = [];
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellValue = $worksheet->getCell($col . $row)->getCalculatedValue();
                    $rowData[] = $cellValue;
                }
                
                if ($this->isHeaderRow($rowData)) {
                    $headerRowIndex = $row - 1; // Convert to 0-based index
                    $headerRow = $rowData;
                    break;
                }
            }
            
            if ($headerRowIndex === -1) {
                Log::error("Could not find header row in Google Drive sheet {$sheetName}");
                return ['created' => 0, 'updated' => 0, 'errors' => 1];
            }
            
            Log::info("Found header row at index {$headerRowIndex} in Google Drive sheet {$sheetName}");
            
            // Process data rows in chunks
            $chunkSize = 100; // Process 100 rows at a time
            for ($startRow = $headerRowIndex + 2; $startRow <= $highestRow; $startRow += $chunkSize) {
                $endRow = min($startRow + $chunkSize - 1, $highestRow);
                
                for ($row = $startRow; $row <= $endRow; $row++) {
                    try {
                        $rowData = [];
                        for ($col = 'A'; $col <= $highestColumn; $col++) {
                            $cellValue = $worksheet->getCell($col . $row)->getCalculatedValue();
                            $rowData[] = $cellValue;
                        }
                        
                        // Skip empty rows
                        if (empty(array_filter($rowData))) {
                            continue;
                        }
                        
                        // Skip program/category rows
                        if ($this->isProgramName($rowData[0] ?? '') || $this->isProgramName($rowData[1] ?? '')) {
                            continue;
                        }
                        
                        // Skip if no name in first position
                        if (empty(trim($rowData[0] ?? ''))) {
                            continue;
                        }
                        
                        // Extract student data
                        $studentData = $this->extractStudentDataFromRow($rowData, $sheetName);
                        
                        if ($studentData) {
                            $result = $this->processStudent($studentData, $sheetName);
                            if ($result['success']) {
                                if ($result['action'] === 'created') {
                                    $created++;
                                } else {
                                    $updated++;
                                }
                            } else {
                                $errors++;
                            }
                        } else {
                            $errors++;
                        }
                        
                    } catch (\Exception $e) {
                        Log::error("Error processing row in Google Drive sheet {$sheetName}", [
                            'row_number' => $row,
                            'error' => $e->getMessage()
                        ]);
                        $errors++;
                    }
                }
                
                // Clear memory after each chunk
                unset($rowData);
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            }
            
        } catch (\Exception $e) {
            Log::error("Error processing Google Drive sheet {$sheetName}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $errors++;
        }
        
        return ['created' => $created, 'updated' => $updated, 'errors' => $errors];
    }
    
    private function isHeaderRow($rowData)
    {
        if (count($rowData) < 1) {
            return false;
        }
        
        $firstCell = trim($rowData[0] ?? '');
        
        // Simple and flexible header detection - just check if first cell contains "NAME"
        if (stripos($firstCell, 'NAME') !== false) {
            return true;
        }
        
        // Also check for other common header patterns in first cell
        $hasIc = stripos($firstCell, 'IC') !== false || stripos($firstCell, 'PASSPORT') !== false;
        $hasAddress = stripos($firstCell, 'ADDRESS') !== false;
        $hasLearners = stripos($firstCell, 'LEARNERS') !== false;
        
        // If it has NAME and at least one other common field, it's a header
        if (stripos($firstCell, 'NAME') !== false && ($hasIc || $hasAddress || $hasLearners)) {
            return true;
        }
        
        return false;
    }

    private function findHeaderRow($rows)
    {
        // Look for header row in the first 10 rows
        for ($i = 0; $i < min(10, count($rows)); $i++) {
            $row = $rows[$i];
            if (count($row) > 2) {
                $firstCell = $row[0] ?? '';
                $secondCell = $row[1] ?? '';
                $thirdCell = $row[2] ?? '';
                
                // Check for header pattern: NO | NAME | ADDRESS | IC/PASSPORT
                if ((stripos($firstCell, 'NO') !== false || stripos($firstCell, 'NAME') !== false) && 
                    (stripos($secondCell, 'NAME') !== false || stripos($thirdCell, 'NAME') !== false) &&
                    (stripos($secondCell, 'IC') !== false || stripos($thirdCell, 'IC') !== false || 
                     stripos($secondCell, 'PASSPORT') !== false || stripos($thirdCell, 'PASSPORT') !== false)) {
                    return $i;
                }
            }
        }
        
        return -1;
    }

    private function isProgramName($value)
    {
        if (empty($value) || !is_string($value)) {
            return false;
        }
        
        $programPatterns = [
            'PHILOSOPHY', 'DOCTOR', 'MASTER', 'BACHELOR', 'DIPLOMA', 'CERTIFICATE',
            'DEGREE', 'PROGRAMME', 'PROGRAM', 'COURSE', 'STUDY', 'MANAGEMENT',
            'BUSINESS', 'EDUCATION', 'RESEARCH', 'INTERNATIONAL', 'LOCAL',
            'TOTAL LEARNERS', 'FILE STATUS', 'HRDC', 'TPN', 'EDP', 'MQA', 'R/', 'N/',
            'FA7968', 'PA18014'
        ];
        
        $valueUpper = strtoupper($value);
        
        foreach ($programPatterns as $pattern) {
            if (strpos($valueUpper, $pattern) !== false) {
                return true;
            }
        }
        
        // Check for program code patterns
        if (preg_match('/^\(HRDC\/TPN\d+\/EDP\/\d+\)/', $value)) return true;
        if (preg_match('/^\(R\/\d+\/\d+\/\d+ \(MQA\/[A-Z0-9]+\)\d+\/\d+\)/', $value)) return true;
        if (preg_match('/^N\/\d+\/\d+\/\d+ \(MQA\/[A-Z0-9]+\)/', $value)) return true;
        if (preg_match('/^\d+$/', $value)) return true; // Pure numbers
        if (preg_match('/^\d+[A-Z]?\d*$/', $value)) return true; // Mostly numbers
        
        return false;
    }

    private function extractStudentDataFromRow($rowData, $sheetName)
    {
        // Always start with the first column as name
        $name = trim($rowData[0] ?? '');
        
        // Skip if no name
        if (empty($name)) {
            return null;
        }
        
        $ic = '';
        $email = '';
        $phone = '';
        $address = '';
        
        // Try to find IC/Passport in any column
        for ($i = 1; $i < count($rowData); $i++) {
            $value = trim($rowData[$i] ?? '');
            if (preg_match('/^\d{6}-\d{2}-\d{4}$/', $value) || preg_match('/^[A-Z]\d{7}$/', $value)) {
                $ic = $value;
                break;
            }
        }
        
        // Try to find email in any column
        for ($i = 1; $i < count($rowData); $i++) {
            $value = trim($rowData[$i] ?? '');
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $email = $value;
                break;
            }
        }
        
        // Try to find phone in any column
        for ($i = 1; $i < count($rowData); $i++) {
            $value = trim($rowData[$i] ?? '');
            if (preg_match('/^[\d\-\+\s\(\)]+$/', $value) && strlen($value) >= 8) {
                $phone = $value;
                break;
            }
        }
        
        // Try to find address in any column (look for longer text)
        for ($i = 1; $i < count($rowData); $i++) {
            $value = trim($rowData[$i] ?? '');
            if (strlen($value) > 10 && !preg_match('/^\d+$/', $value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $address = $value;
                break;
            }
        }
        
        // Generate email if not provided
        if (empty($email) && !empty($ic)) {
            $email = 'student_' . $ic . '_' . time() . '@lms.local';
        } elseif (empty($email)) {
            $email = 'student_' . preg_replace('/[^a-zA-Z0-9]/', '', $name) . '_' . time() . '@lms.local';
        }
        
        return [
            'name' => $name,
            'ic' => $ic,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'source_sheet' => $sheetName
        ];
    }

    private function processStudent($data, $sheetName)
    {
        try {
            $user = null;
            
            // Only match by IC if IC is not empty
            if (!empty($data['ic'])) {
                $user = User::where('ic', $data['ic'])->first();
            }
            
            // If no match by IC, try email
            if (!$user) {
                $user = User::where('email', $data['email'])->first();
            }
            
            // If still no match, try name and source sheet combination
            if (!$user) {
                $user = User::where('name', $data['name'])
                           ->where('source_sheet', $sheetName)
                           ->first();
            }
            
            // For students with empty IC, generate a unique IC to avoid constraint violation
            if (empty($data['ic'])) {
                $data['ic'] = 'AUTO_' . time() . '_' . rand(1000, 9999);
            }
            
            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'ic' => $data['ic'],
                    'phone' => $data['phone'] ?: null,
                    'address' => $data['address'] ?: null,
                    'password' => Hash::make($data['ic']),
                    'role' => 'student',
                    'must_reset_password' => false,
                    'source_sheet' => $data['source_sheet']
                ]);
                
                Log::info('User created from Google Drive', [
                    'name' => $data['name'],
                    'ic' => $data['ic'],
                    'sheet' => $sheetName
                ]);
                
                return ['success' => true, 'action' => 'created'];
            } else {
                // Update existing user
                $user->update([
                    'name' => $data['name'],
                    'phone' => $data['phone'] ?: $user->phone,
                    'address' => $data['address'] ?: $user->address,
                    'source_sheet' => $data['source_sheet']
                ]);
                
                Log::info('User updated from Google Drive', [
                    'name' => $data['name'],
                    'ic' => $data['ic'],
                    'sheet' => $sheetName
                ]);
                
                return ['success' => true, 'action' => 'updated'];
            }
            
        } catch (\Exception $e) {
            Log::error('Error processing student from Google Drive', [
                'data' => $data,
                'sheet' => $sheetName,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'action' => 'error'];
        }
    }
}
