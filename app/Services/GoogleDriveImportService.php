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
        
        // Process only UPM data from the new spreadsheet structure
        $this->lmsSheets = [
            10 => 'UPM LMS'  // Target the UPM sheet (11th sheet, index 10)
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
                        // Convert column letter to number for proper iteration
                        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                        
                        for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
                            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
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
                        
                        // Skip rows that are clearly not student data
                        $firstCol = trim($rowData[0] ?? '');
                        if ($firstCol === 'LOCAL' || $firstCol === 'INTERNATIONAL' || 
                            $firstCol === 'TOTAL LEARNERS' || $firstCol === 'FILE STATUS') {
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
        $secondCell = trim($rowData[1] ?? '');
        $thirdCell = trim($rowData[2] ?? '');
        $fourthCell = trim($rowData[3] ?? '');
        
        // Check for the new spreadsheet header structure
        // Column A: NAME, Column B: STATUS, Column C: ADDRESS, Column D: IC/PASSPORT
        if (stripos($firstCell, 'NAME') !== false && 
            stripos($secondCell, 'STATUS') !== false && 
            stripos($thirdCell, 'ADDRESS') !== false && 
            (stripos($fourthCell, 'IC') !== false || stripos($fourthCell, 'PASSPORT') !== false)) {
            return true;
        }
        
        // Fallback: Simple header detection for NAME
        if (stripos($firstCell, 'NAME') !== false) {
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
        // New spreadsheet structure based on the provided data:
        // Column A: NAME, Column B: STATUS, Column C: ADDRESS, Column D: IC/PASSPORT, etc.
        
        $name = trim($rowData[0] ?? ''); // Column A: NAME
        $status = trim($rowData[1] ?? ''); // Column B: STATUS
        $address = trim($rowData[2] ?? ''); // Column C: ADDRESS
        $ic = trim($rowData[3] ?? ''); // Column D: IC/PASSPORT
        $category = trim($rowData[4] ?? ''); // Column E: CATEGORY
        $programmeName = trim($rowData[5] ?? ''); // Column F: PROGRAMME NAME
        $programmeCode = trim($rowData[6] ?? ''); // Column G: PROGRAMME CODE
        $colRefNo = trim($rowData[7] ?? ''); // Column H: COL REF. NO.
        $studentId = trim($rowData[8] ?? ''); // Column I: STUDENT ID
        $contactNo = trim($rowData[9] ?? ''); // Column J: CONTACT NO.
        $email = trim($rowData[10] ?? ''); // Column K: EMAIL
        $studentEmail = trim($rowData[11] ?? ''); // Column L: STUDENT EMAIL
        $studentPortal = trim($rowData[12] ?? ''); // Column M: STUDENT PORTAL
        $semesterEntry = trim($rowData[13] ?? ''); // Column N: SEMESTER ENTRY
        $researchTitle = trim($rowData[14] ?? ''); // Column O: RESEARCH TITLE
        $supervisor = trim($rowData[15] ?? ''); // Column P: SUPERVISOR
        $externalExaminer = trim($rowData[16] ?? ''); // Column Q: EXTERNAL EXAMINER
        $internalExaminer = trim($rowData[17] ?? ''); // Column R: INTERNAL EXAMINER
        $colDate = trim($rowData[18] ?? ''); // Column S: COL DATE
        $programmeIntake = trim($rowData[19] ?? ''); // Column T: PROGRAMME INTAKE
        $dateOfCommencement = trim($rowData[20] ?? ''); // Column U: DATE OF COMMENCEMENT
        $totalFees = trim($rowData[21] ?? ''); // Column V: TOTAL FEES
        $transactionMonth = trim($rowData[22] ?? ''); // Column W: TRANSACTION MONTH
        $remarks = trim($rowData[23] ?? ''); // Column X: REMARKS
        $pic = trim($rowData[24] ?? ''); // Column Y: PIC
        
        // Skip if no name or if status is "Withdrawn"
        if (empty($name) || $status === 'Withdrawn') {
            return null;
        }
        
        // Only process UPM students - check if this is UPM data
        // We'll filter by programme name or other UPM-specific indicators
        $isUPM = $this->isUPMStudent($programmeName, $programmeCode, $studentId, $name);
        
        if (!$isUPM) {
            return null; // Skip non-UPM students
        }
        
        // Clean up IC/Passport field
        if (!empty($ic)) {
                // Malaysian IC patterns
            if (preg_match('/^\d{6}-\d{2}-\d{1,7}$/', $ic)) {
                // Valid Malaysian IC
            }
            // Passport patterns
            elseif (preg_match('/^[A-Z]{1,4}\d{6,9}$/', $ic)) {
                // Valid passport
            }
            else {
                // Invalid IC/Passport format, generate one
                $ic = 'AUTO_' . time() . '_' . rand(1000, 9999);
            }
        } else {
            // No IC provided, generate one
            $ic = 'AUTO_' . time() . '_' . rand(1000, 9999);
        }
        
        // Use email from spreadsheet directly, no auto-generation
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = null; // Leave email empty if not valid
        }
        
        // Clean up phone number
        if (empty($contactNo)) {
            $contactNo = '';
        }
        
        return [
            'name' => $name,
            'ic' => $ic,
            'email' => $email,
            'student_email' => $studentEmail,
            'phone' => $contactNo,
            'address' => $address,
            'source_sheet' => $sheetName,
            'status' => $status,
            'category' => $category,
            'programme_name' => $programmeName,
            'programme_code' => $programmeCode,
            'student_id' => $studentId,
            'research_title' => $researchTitle,
            'supervisor' => $supervisor,
            'total_fees' => $totalFees,
            'col_ref_no' => $colRefNo,
            'student_portal' => $studentPortal,
            'semester_entry' => $semesterEntry,
            'external_examiner' => $externalExaminer,
            'internal_examiner' => $internalExaminer,
            'col_date' => $colDate,
            'programme_intake' => $programmeIntake,
            'date_of_commencement' => $dateOfCommencement,
            'transaction_month' => $transactionMonth,
            'remarks' => $remarks,
            'pic' => $pic,
            'contact_no' => $contactNo
        ];
    }
    
    private function isUPMStudent($programmeName, $programmeCode, $studentId, $name)
    {
        // Check various indicators that this is a UPM student
        $upmIndicators = [
            'UPM',
            'Universiti Putra Malaysia',
            'Putra Malaysia',
            'UPM LMS'
        ];
        
        $textToCheck = strtoupper($programmeName . ' ' . $programmeCode . ' ' . $studentId . ' ' . $name);
        
        foreach ($upmIndicators as $indicator) {
            if (strpos($textToCheck, strtoupper($indicator)) !== false) {
                return true;
            }
        }
        
        // Check if student ID contains UPM pattern
        if (preg_match('/UPM\d+/', $studentId)) {
            return true;
        }
        
        // Check if programme code contains UPM pattern
        if (preg_match('/UPM\d+/', $programmeCode)) {
            return true;
        }
        
        // For now, we'll process all students from the new spreadsheet
        // You can modify this logic based on your specific UPM identification criteria
        return true; // Process all students for now
    }

    private function processStudent($data, $sheetName)
    {
        try {
            $user = null;
            
            // Only match by IC if IC is not empty and not an AUTO IC
            if (!empty($data['ic']) && strpos($data['ic'], 'AUTO_') !== 0) {
                $user = User::where('ic', $data['ic'])->first();
            }
            
            // If no match by IC, try email
            if (!$user && !empty($data['email'])) {
                $user = User::where('email', $data['email'])->first();
            }
            
            // If still no match, try to find student with AUTO IC by name (prioritize updating AUTO ICs)
            if (!$user && !empty($data['ic']) && strpos($data['ic'], 'AUTO_') !== 0) {
                $user = User::where('name', $data['name'])
                           ->where('role', 'student')
                           ->where('ic', 'like', 'AUTO_%')
                           ->first();
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
                // Create new user with all new fields
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'student_email' => $data['student_email'] ?? null,
                    'ic' => $data['ic'],
                    'phone' => $data['phone'] ?: null,
                    'address' => $data['address'] ?: null,
                    'password' => Hash::make($data['ic']),
                    'role' => 'student',
                    'must_reset_password' => false,
                    'source_sheet' => $data['source_sheet'],
                    'status' => $data['status'] ?? null,
                    'contact_no' => $data['contact_no'] ?? null,
                    'student_portal' => $data['student_portal'] ?? null,
                    'total_fees' => $data['total_fees'] ?? null,
                    'transaction_month' => $data['transaction_month'] ?? null,
                    'remarks' => $data['remarks'] ?? null,
                    'pic' => $data['pic'] ?? null,
                    'category' => $data['category'] ?? null,
                    'programme_name' => $data['programme_name'] ?? null,
                    'programme_code' => $data['programme_code'] ?? null,
                    'student_id' => $data['student_id'] ?? null,
                    'col_ref_no' => $data['col_ref_no'] ?? null,
                    'semester_entry' => $data['semester_entry'] ?? null,
                    'research_title' => $data['research_title'] ?? null,
                    'supervisor' => $data['supervisor'] ?? null,
                    'external_examiner' => $data['external_examiner'] ?? null,
                    'internal_examiner' => $data['internal_examiner'] ?? null,
                    'col_date' => $data['col_date'] ?? null,
                    'programme_intake' => $data['programme_intake'] ?? null,
                    'date_of_commencement' => $data['date_of_commencement'] ?? null
                ]);
                
                Log::info('User created from Google Drive', [
                    'name' => $data['name'],
                    'ic' => $data['ic'],
                    'sheet' => $sheetName
                ]);
                
                return ['success' => true, 'action' => 'created'];
            } else {
                // Update existing user with all new fields
                $updateData = [
                    'name' => $data['name'],
                    'phone' => $data['phone'] ?: $user->phone,
                    'address' => $data['address'] ?: $user->address,
                    'student_email' => $data['student_email'] ?? $user->student_email,
                    'source_sheet' => $data['source_sheet'],
                    'status' => $data['status'] ?? $user->status,
                    'contact_no' => $data['contact_no'] ?? $user->contact_no,
                    'student_portal' => $data['student_portal'] ?? $user->student_portal,
                    'total_fees' => $data['total_fees'] ?? $user->total_fees,
                    'transaction_month' => $data['transaction_month'] ?? $user->transaction_month,
                    'remarks' => $data['remarks'] ?? $user->remarks,
                    'pic' => $data['pic'] ?? $user->pic,
                    'category' => $data['category'] ?? $user->category,
                    'programme_name' => $data['programme_name'] ?? $user->programme_name,
                    'programme_code' => $data['programme_code'] ?? $user->programme_code,
                    'student_id' => $data['student_id'] ?? $user->student_id,
                    'col_ref_no' => $data['col_ref_no'] ?? $user->col_ref_no,
                    'semester_entry' => $data['semester_entry'] ?? $user->semester_entry,
                    'research_title' => $data['research_title'] ?? $user->research_title,
                    'supervisor' => $data['supervisor'] ?? $user->supervisor,
                    'external_examiner' => $data['external_examiner'] ?? $user->external_examiner,
                    'internal_examiner' => $data['internal_examiner'] ?? $user->internal_examiner,
                    'col_date' => $data['col_date'] ?? $user->col_date,
                    'programme_intake' => $data['programme_intake'] ?? $user->programme_intake,
                    'date_of_commencement' => $data['date_of_commencement'] ?? $user->date_of_commencement
                ];
                
                // If user has AUTO IC and we have a real IC, update it
                if (strpos($user->ic, 'AUTO_') === 0 && !empty($data['ic']) && strpos($data['ic'], 'AUTO_') !== 0) {
                    $updateData['ic'] = $data['ic'];
                    $updateData['password'] = Hash::make($data['ic']); // Update password to match new IC
                }
                    
                // Update email from spreadsheet if provided
                if (!empty($data['email'])) {
                        $updateData['email'] = $data['email'];
                }
                
                $user->update($updateData);
                
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
