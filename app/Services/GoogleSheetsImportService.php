<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GoogleSheetsImportService
{
    private $googleSheetsUrl;
    private $baseUrl;
    
    // LMS Sheets configuration - only process UPM data from the new spreadsheet
    private $lmsSheets = [
        10 => 'UPM LMS'  // Target the UPM sheet (11th sheet, index 10)
    ];
    
    // UPM Sheet GID for direct access
    private $upmSheetGid = '1696751501';
    
    public function __construct()
    {
        // Get configuration from config file
        $config = config('google_sheets', []);
        
        // Get the Google Sheets URL from config or environment
        $this->googleSheetsUrl = $config['url'] ?? env('GOOGLE_SHEETS_URL', 'https://docs.google.com/spreadsheets/d/1MnAeovkeOM_pZGx6DqS7hMvJQyaQDEu8/edit?usp=sharing&ouid=117738643589016699947&rtpof=true&sd=true');
        
        // Extract the base URL for CSV exports
        if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $this->googleSheetsUrl, $matches)) {
            $this->baseUrl = 'https://docs.google.com/spreadsheets/d/' . $matches[1];
        } else {
            // Fallback to the hardcoded URL if parsing fails
            $this->baseUrl = 'https://docs.google.com/spreadsheets/d/1MnAeovkeOM_pZGx6DqS7hMvJQyaQDEu8';
        }
        
        // Update LMS sheets from config if available
        if (isset($config['lms_sheets'])) {
            $this->lmsSheets = $config['lms_sheets'];
        }
        
        // Force only UPM processing
        $this->lmsSheets = [
            'UPM LMS' => 'UPM LMS'  // Only process UPM data
        ];
        
        Log::info('GoogleSheetsImportService initialized', [
            'googleSheetsUrl' => $this->googleSheetsUrl,
            'baseUrl' => $this->baseUrl,
            'lmsSheets' => $this->lmsSheets
        ]);
    }
    
    public function importFromGoogleSheets()
    {
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $processedSheets = [];

        Log::info('Starting Google Sheets import for all LMS sheets', ['sheets' => array_keys($this->lmsSheets)]);

        // First, validate that we can access the Google Sheets
        if (!$this->validateGoogleSheetsAccess()) {
            return [
                'success' => false,
                'created' => 0,
                'updated' => 0,
                'errors' => 1,
                'message' => 'Cannot access Google Sheets. Please check the URL and permissions.',
                'processed_sheets' => []
            ];
        }

        // Process each individual LMS sheet using sheet indices (11-17)
        foreach ($this->lmsSheets as $sheetIndex => $sheetName) {
            Log::info("Processing sheet index {$sheetIndex}: {$sheetName}");
            
            try {
                // Use the specific UPM sheet GID for direct access
                $csvExportUrl = $this->baseUrl . '/export?format=csv&gid=' . $this->upmSheetGid;
                
                // Fetch CSV data from Google Sheets
                Log::info('Fetching data from Google Sheets', ['url' => $csvExportUrl]);
                $response = Http::timeout(30)->get($csvExportUrl);
                
                Log::info('Google Sheets response', [
                    'sheet' => $sheetName,
                    'status' => $response->status(),
                    'body_length' => strlen($response->body())
                ]);
                
                if (!$response->successful()) {
                    Log::error('Failed to fetch Google Sheets data', [
                        'sheet' => $sheetName,
                        'status' => $response->status(),
                        'url' => $csvExportUrl
                    ]);
                    $totalErrors++;
                } else {
                    $csvData = $response->body();
                    $rows = $this->parseCsvData($csvData);
                    
                    if (empty($rows)) {
                        Log::warning('No data found in Google Sheets', ['sheet' => $sheetName]);
                    } else {
                        Log::info('Google Sheets data fetched successfully', [
                            'sheet' => $sheetName,
                            'total_rows' => count($rows)
                        ]);

                        // Process the data for this specific sheet
                        $result = $this->processGoogleSheetsData($rows, $sheetName);
                        
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
                    }
                }

            } catch (\Exception $e) {
                Log::error('Error processing Google Sheets data', [
                    'sheet' => $sheetName,
                    'sheet_index' => $sheetIndex,
                    'error' => $e->getMessage()
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
        
        Log::info('Google Sheets import completed', [
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
    }

    private function parseCsvData($csvData)
    {
        // Use PHP's built-in CSV parser which handles multi-line fields properly
        $rows = [];
        $tempFile = tmpfile();
        fwrite($tempFile, $csvData);
        rewind($tempFile);
        
        while (($row = fgetcsv($tempFile)) !== false) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            // Only process rows with the expected number of columns (25)
            if (count($row) >= 25) {
                $rows[] = $row;
            }
        }
        
        fclose($tempFile);
        
        return $rows;
    }
    
    private function mergePartialRow(&$currentRow, $newRow)
    {
        // Merge the address field (index 2) and other fields
        if (isset($newRow[0]) && !empty($newRow[0])) {
            $currentRow[2] = ($currentRow[2] ?? '') . ' ' . $newRow[0];
        }
        if (isset($newRow[1]) && !empty($newRow[1])) {
            $currentRow[2] = ($currentRow[2] ?? '') . ' ' . $newRow[1];
        }
        if (isset($newRow[2]) && !empty($newRow[2])) {
            $currentRow[2] = ($currentRow[2] ?? '') . ' ' . $newRow[2];
        }
        
        // Fill missing columns with empty strings to reach 26 columns
        while (count($currentRow) < 26) {
            $currentRow[] = '';
        }
    }

    
    
    
    private function validateGoogleSheetsAccess()
    {
        try {
            // Try to access the UPM sheet using the specific GID
            $testUrl = $this->baseUrl . '/export?format=csv&gid=' . $this->upmSheetGid;
            $response = Http::timeout(10)->get($testUrl);
            
            if ($response->successful()) {
                Log::info('Google Sheets access validated successfully', ['gid' => $this->upmSheetGid]);
                return true;
            } else {
                Log::error('Google Sheets access validation failed', [
                    'status' => $response->status(),
                    'url' => $testUrl,
                    'response' => substr($response->body(), 0, 200)
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Google Sheets access validation error', [
                'error' => $e->getMessage(),
                'url' => $this->baseUrl
            ]);
            return false;
        }
    }

    private function processGoogleSheetsData($rows, $sheetName = 'Unknown')
    {
        $created = 0;
        $updated = 0;
        $errors = 0;

        Log::info("Processing Google Sheets data for sheet: {$sheetName}", ['total_rows' => count($rows)]);

        // Find the header row (look for "NAME" and "STATUS" columns)
        $headerRowIndex = -1;
        $header = [];
        
        for ($i = 0; $i < min(10, count($rows)); $i++) {
            $row = $rows[$i];
            if (count($row) > 3) {
                $firstCell = trim($row[0] ?? '');
                $secondCell = trim($row[1] ?? '');
                $thirdCell = trim($row[2] ?? '');
                $fourthCell = trim($row[3] ?? '');
                
                // Check for new UPM format: NAME | STATUS | ADDRESS | IC/PASSPORT
                if (stripos($firstCell, 'NAME') !== false && 
                    stripos($secondCell, 'STATUS') !== false && 
                    stripos($thirdCell, 'ADDRESS') !== false &&
                    (stripos($fourthCell, 'IC') !== false || stripos($fourthCell, 'PASSPORT') !== false)) {
                    $headerRowIndex = $i;
                    $header = $row;
                    Log::info("Found UPM header row at index {$i}", [
                        'first_cell' => $firstCell,
                        'second_cell' => $secondCell,
                        'third_cell' => $thirdCell,
                        'fourth_cell' => $fourthCell
                    ]);
                    break;
                }
                
                // Check for old format: NO | NAME | ADDRESS | IC/PASSPORT
                if (stripos($firstCell, 'NO') !== false && 
                    stripos($secondCell, 'NAME') !== false && 
                    stripos($thirdCell, 'ADDRESS') !== false &&
                    (stripos($fourthCell, 'IC') !== false || stripos($fourthCell, 'PASSPORT') !== false)) {
                    $headerRowIndex = $i;
                    $header = $row;
                    break;
                }
                
                // Check for old format: NAME | ADDRESS | IC/PASSPORT
                if (stripos($firstCell, 'NAME') !== false && 
                    stripos($secondCell, 'ADDRESS') !== false && 
                    (stripos($thirdCell, 'IC') !== false || stripos($thirdCell, 'PASSPORT') !== false)) {
                    $headerRowIndex = $i;
                    $header = $row;
                    break;
                }
            }
        }

        if ($headerRowIndex === -1) {
            Log::error("Could not find header row in Google Sheets data for sheet: {$sheetName}");
            return ['created' => 0, 'updated' => 0, 'errors' => 1];
        }

        Log::info("Found header row at index: {$headerRowIndex} for sheet: {$sheetName}", ['headers' => $header]);

        // Clean headers (remove BOM and trim)
        $header = array_map(function($h) {
            $h = trim($h);
            if (substr($h, 0, 3) === "\xEF\xBB\xBF") {
                $h = substr($h, 3);
            }
            return trim($h);
        }, $header);

        // Process data rows (skip header row and any program/category rows)
        $dataRows = array_slice($rows, $headerRowIndex + 1);
        
        foreach ($dataRows as $rowIndex => $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Skip rows that contain program names or categories (but not student data)
            $firstColumnValue = $row[0] ?? '';
            $secondColumnValue = $row[1] ?? '';
            
            // Skip if first column contains program names and second column is empty
            if (is_string($firstColumnValue) && empty(trim($secondColumnValue)) && (
                stripos($firstColumnValue, 'PHILOSOPHY') !== false ||
                stripos($firstColumnValue, 'INTERNATIONAL') !== false ||
                stripos($firstColumnValue, 'LOCAL') !== false ||
                stripos($firstColumnValue, 'PROGRAMME') !== false ||
                stripos($firstColumnValue, 'DOCTOR') !== false ||
                stripos($firstColumnValue, 'MASTER') !== false ||
                stripos($firstColumnValue, 'BACHELOR') !== false ||
                stripos($firstColumnValue, 'FILE STATUS') !== false ||
                stripos($firstColumnValue, 'TOTAL LEARNERS') !== false
            )) {
                Log::info("Skipping program/category row " . ($rowIndex + 1) . " - contains: " . $firstColumnValue);
                continue;
            }
            
            // Skip if it's clearly not a student row (no name in second column)
            if (empty(trim($secondColumnValue)) || 
                stripos($secondColumnValue, 'TOTAL LEARNERS') !== false ||
                stripos($secondColumnValue, 'FILE STATUS') !== false) {
                Log::info("Skipping non-student row " . ($rowIndex + 1) . " - no name: " . $secondColumnValue);
                continue;
            }
            
            // Skip rows where the name looks like an address or IC number
            if (is_string($secondColumnValue) && (
                preg_match('/^\d{6}-\d{2}-\d{4}$/', $secondColumnValue) || // IC number pattern
                preg_match('/^\d{5}\s/', $secondColumnValue) || // Postal code pattern
                stripos($secondColumnValue, 'JALAN') !== false ||
                stripos($secondColumnValue, 'TAMAN') !== false ||
                stripos($secondColumnValue, 'KAMPUNG') !== false ||
                stripos($secondColumnValue, 'KAJANG') !== false ||
                stripos($secondColumnValue, 'IPOH') !== false ||
                stripos($secondColumnValue, 'SELANGOR') !== false ||
                stripos($secondColumnValue, 'PERAK') !== false ||
                stripos($secondColumnValue, 'PAHANG') !== false
            )) {
                Log::info("Skipping non-student row " . ($rowIndex + 1) . " - name looks like address: " . $secondColumnValue);
                continue;
            }
            
            // Skip if first column is not numeric (not a student number) and second column is empty
            // This catches continuation rows like "TAMAN TROPIKA 2" that should be skipped
            if (!is_numeric($firstColumnValue) && empty(trim($secondColumnValue))) {
                Log::info("Skipping continuation row " . ($rowIndex + 1) . " - not a student: " . $firstColumnValue);
                continue;
            }
            
            // Skip if first column is empty or looks like a header/total row
            if (empty($firstColumnValue) || 
                stripos($firstColumnValue, 'TOTAL') !== false ||
                stripos($firstColumnValue, 'FILE STATUS') !== false ||
                stripos($firstColumnValue, 'PHILOSOPHY') !== false) {
                Log::info("Skipping non-student row " . ($rowIndex + 1) . " - header/total: " . $firstColumnValue);
                continue;
            }

            // Ensure row has same number of columns as header
            if (count($row) !== count($header)) {
                Log::warning('Row column count mismatch', [
                    'expected' => count($header),
                    'actual' => count($row),
                    'row' => $row
                ]);
                $errors++;
                continue;
            }

            // Combine header with row data
            $data = array_combine($header, $row);
            
            // Extract student data
            $extractedData = $this->extractStudentData($data);
            
            if (!$extractedData) {
                Log::warning('Failed to extract student data for row', [
                    'rowIndex' => $rowIndex + 1,
                    'data' => $data,
                    'headers' => $header
                ]);
                $errors++;
                continue;
            }

            // Add sheet name to the extracted data
            $extractedData['source_sheet'] = $sheetName;
            
            // Process the student data
            $result = $this->processStudent($extractedData);
            if ($result['success']) {
                if ($result['action'] === 'created') {
                    $created++;
                } else {
                    $updated++;
                }
            } else {
                $errors++;
            }
        }

        return ['created' => $created, 'updated' => $updated, 'errors' => $errors];
    }

    private function extractStudentData($data)
    {
        // Handle both numeric arrays (direct CSV) and associative arrays (processed data)
        if (isset($data['NAME'])) {
            // Associative array from processGoogleSheetsData
            $hasName = !empty(trim($data['NAME'] ?? ''));
            $hasEmail = !empty(trim($data['EMAIL'] ?? ''));
            $hasIc = !empty(trim($data['IC / PASSPORT'] ?? ''));
        } else {
            // Numeric array from direct CSV parsing
            $hasName = !empty(trim($data[0] ?? '')); // Column 0: NAME
            $hasEmail = !empty(trim($data[10] ?? '')); // Column 10: EMAIL
            $hasIc = !empty(trim($data[3] ?? '')); // Column 3: IC/PASSPORT
        }
        
        // Be more lenient like Excel import - only require name
        if (!$hasName) {
            Log::warning('Missing required name field', [
                'hasName' => $hasName,
                'hasEmail' => $hasEmail,
                'hasIc' => $hasIc,
                'data' => $data
            ]);
            return false;
        }
        
        // Extract data with flexible column matching - all fields from spreadsheet
        $extracted = [
            'name' => '',
            'email' => '',
            'student_email' => '',
            'ic' => '',
            'phone' => '',
            'address' => '',
            'col_ref_no' => '',
            'student_id' => '',
            'programme_name' => '',
            'status' => '',
            'category' => '',
            'contact_no' => '',
            'student_portal' => '',
            'total_fees' => '',
            'transaction_month' => '',
            'remarks' => '',
            'pic' => '',
            'programme_code' => '',
            'semester_entry' => '',
            'research_title' => '',
            'supervisor' => '',
            'external_examiner' => '',
            'internal_examiner' => '',
            'col_date' => '',
            'programme_intake' => '',
            'date_of_commencement' => ''
        ];

        // For new format, try direct column mapping first
        if (isset($data['NAME'])) {
            // Associative array from processGoogleSheetsData
            $extracted['name'] = trim($data['NAME'] ?? '');
            $extracted['status'] = trim($data['STATUS'] ?? '');
            $extracted['address'] = trim($data['ADDRESS'] ?? '');
            $extracted['ic'] = trim($data['IC / PASSPORT'] ?? '');
            $extracted['category'] = trim($data['CATEGORY'] ?? '');
            $extracted['programme_name'] = trim($data['PROGRAMME NAME'] ?? '');
            $extracted['programme_code'] = trim($data['PROGRAMME CODE'] ?? '');
            $extracted['col_ref_no'] = trim($data['COL REF. NO.'] ?? '');
            $extracted['student_id'] = trim($data['STUDENT ID'] ?? '');
            $extracted['contact_no'] = trim($data['CONTACT NO.'] ?? '');
            $extracted['email'] = trim($data['EMAIL'] ?? '');
            $extracted['student_email'] = trim($data['STUDENT EMAIL'] ?? '');
            $extracted['student_portal'] = trim($data['STUDENT PORTAL'] ?? '');
            $extracted['semester_entry'] = trim($data['SEMESTER ENTRY'] ?? '');
            $extracted['research_title'] = trim($data['RESEARCH TITLE'] ?? '');
            $extracted['supervisor'] = trim($data['SUPERVISOR'] ?? '');
            $extracted['external_examiner'] = trim($data['EXTERNAL EXAMINER'] ?? '');
            $extracted['internal_examiner'] = trim($data['INTERNAL EXAMINER'] ?? '');
            $extracted['col_date'] = trim($data['COL DATE'] ?? '');
            $extracted['programme_intake'] = trim($data['PROGRAMME INTAKE'] ?? '');
            $extracted['date_of_commencement'] = trim($data['DATE OF COMMENCEMENT'] ?? '');
            $extracted['total_fees'] = trim($data['TOTAL FEES'] ?? '');
            $extracted['transaction_month'] = trim($data['TRANSACTION MONTH'] ?? '');
            $extracted['remarks'] = trim($data['REMARKS'] ?? '');
            $extracted['pic'] = trim($data['PIC'] ?? '');
        } elseif (count($data) >= 25) {
            // Numeric array from direct CSV parsing
            $extracted['name'] = trim($data[0] ?? '');
            $extracted['status'] = trim($data[1] ?? '');
            $extracted['address'] = trim($data[2] ?? '');
            $extracted['ic'] = trim($data[3] ?? '');
            $extracted['category'] = trim($data[4] ?? '');
            $extracted['programme_name'] = trim($data[5] ?? '');
            $extracted['programme_code'] = trim($data[6] ?? '');
            $extracted['col_ref_no'] = trim($data[7] ?? '');
            $extracted['student_id'] = trim($data[8] ?? '');
            $extracted['contact_no'] = trim($data[9] ?? '');
            $extracted['email'] = trim($data[10] ?? '');
            $extracted['student_email'] = trim($data[11] ?? '');
            $extracted['student_portal'] = trim($data[12] ?? '');
            $extracted['semester_entry'] = trim($data[13] ?? '');
            $extracted['research_title'] = trim($data[14] ?? '');
            $extracted['supervisor'] = trim($data[15] ?? '');
            $extracted['external_examiner'] = trim($data[16] ?? '');
            $extracted['internal_examiner'] = trim($data[17] ?? '');
            $extracted['col_date'] = trim($data[18] ?? '');
            $extracted['programme_intake'] = trim($data[19] ?? '');
            $extracted['date_of_commencement'] = trim($data[20] ?? '');
            $extracted['total_fees'] = trim($data[21] ?? '');
            $extracted['transaction_month'] = trim($data[22] ?? '');
            $extracted['remarks'] = trim($data[23] ?? '');
            $extracted['pic'] = trim($data[24] ?? '');
        }

        // If direct mapping didn't work, try flexible matching
        if (empty($extracted['name']) || empty($extracted['ic']) || empty($extracted['email'])) {
            foreach ($data as $key => $value) {
            $keyLower = strtolower(trim($key));
            $value = trim($value);
            
            if (strpos($keyLower, 'name') !== false && empty($extracted['name'])) {
                $extracted['name'] = $value;
            } elseif (strpos($keyLower, 'email') !== false && empty($extracted['email'])) {
                $extracted['email'] = $value;
            } elseif ((strpos($keyLower, 'ic') !== false || strpos($keyLower, 'passport') !== false) && empty($extracted['ic'])) {
                // Validate that this is actually an IC number, not a phone number
                if (preg_match('/^\d{6}-\d{2}-\d{4}$/', $value) || 
                    preg_match('/^\d{6}-\d{2}-\d{3}$/', $value) ||
                    preg_match('/^\d{6}-\d{2}-\d{5}$/', $value) ||
                    preg_match('/^\d{6}-\d{2}-\d{2}$/', $value) ||
                    preg_match('/^\d{6}-\d{2}-\d{6}$/', $value) ||
                    preg_match('/^\d{6}-\d{2}-\d{1}$/', $value) ||
                    preg_match('/^\d{6}-\d{2}-\d{7}$/', $value) ||
                    preg_match('/^[A-Z]\d{7}$/', $value) ||
                    preg_match('/^[A-Z]\d{8}$/', $value) ||
                    preg_match('/^[A-Z]\d{9}$/', $value) ||
                    preg_match('/^[A-Z]{2}\d{7}$/', $value) ||
                    preg_match('/^[A-Z]{2}\d{6}$/', $value) ||
                    preg_match('/^[A-Z]{2}\d{8}$/', $value) ||
                    preg_match('/^[A-Z]{3}\d{6}$/', $value) ||
                    preg_match('/^[A-Z]{3}\d{7}$/', $value) ||
                    preg_match('/^[A-Z]{4}\d{6}$/', $value) ||
                    preg_match('/^[A-Z]{4}\d{7}$/', $value)) {
                    $extracted['ic'] = $value;
                }
            } elseif ((strpos($keyLower, 'contact') !== false || strpos($keyLower, 'phone') !== false) && empty($extracted['phone'])) {
                // Validate that this is actually a phone number, not an IC number
                if (preg_match('/^[\d\-\+\s\(\)]+$/', $value) && 
                    strlen($value) >= 8 && 
                    !preg_match('/^\d{6}-\d{2}-\d{4}$/', $value) && // Not Malaysian IC
                    !preg_match('/^\d{6}-\d{2}-\d{3}$/', $value) && // Not alternative IC
                    !preg_match('/^\d{6}-\d{2}-\d{5}$/', $value) && // Not alternative IC
                    !preg_match('/^\d{6}-\d{2}-\d{2}$/', $value) && // Not alternative IC
                    !preg_match('/^\d{6}-\d{2}-\d{6}$/', $value) && // Not alternative IC
                    !preg_match('/^\d{6}-\d{2}-\d{1}$/', $value) && // Not alternative IC
                    !preg_match('/^\d{6}-\d{2}-\d{7}$/', $value)) { // Not alternative IC
                    $extracted['phone'] = $value;
                }
            } elseif (strpos($keyLower, 'address') !== false && empty($extracted['address'])) {
                $extracted['address'] = $value;
            } elseif ((strpos($keyLower, 'col') !== false && strpos($keyLower, 'ref') !== false) && empty($extracted['colRefNo'])) {
                $extracted['colRefNo'] = $value;
            } elseif ((strpos($keyLower, 'student') !== false && strpos($keyLower, 'id') !== false) && empty($extracted['studentId'])) {
                $extracted['studentId'] = $value;
            } elseif (strpos($keyLower, 'programme') !== false && strpos($keyLower, 'name') !== false && empty($extracted['programmeName'])) {
                $extracted['programmeName'] = $value;
            } elseif ((strpos($keyLower, 'programme') !== false || strpos($keyLower, 'program') !== false || strpos($keyLower, 'programe') !== false) && strpos($keyLower, 'name') !== false && empty($extracted['programmeName'])) {
                $extracted['programmeName'] = $value;
            }
        }
        }

        // If no email provided, generate one from IC
        if (!$hasEmail) {
            $extracted['email'] = 'student_' . $extracted['ic'] . '@lms.local';
            Log::info('Generated email for student without email', [
                'name' => $extracted['name'],
                'ic' => $extracted['ic'],
                'generated_email' => $extracted['email']
            ]);
        }

        return $extracted;
    }

    private function processStudent($data)
    {
        try {
            // Find existing user by Student ID first (most reliable), then IC, then email
            $user = null;
            
            // Try to find by Student ID first if it exists
            if (!empty($data['student_id'])) {
                $user = User::where('student_id', $data['student_id'])->first();
            }
            
            // If not found by Student ID, try IC
            if (!$user && !empty($data['ic'])) {
                $user = User::where('ic', $data['ic'])->first();
            }
            
            // If still not found, try email (but allow duplicates as per user request)
            if (!$user && !empty($data['email'])) {
                $user = User::where('email', $data['email'])->first();
            }

            // Parse courses
            $courses = [];
            if (!empty($data['programmeName'])) {
                $courses = array_map('trim', explode(',', $data['programmeName']));
            }

            // Parse student portal credentials
            $portalUsername = '';
            $portalPassword = '';
            if (!empty($data['studentPortal'])) {
                $parts = explode(' ', $data['studentPortal']);
                foreach ($parts as $part) {
                    if (strpos($part, 'Username:') !== false) {
                        $portalUsername = str_replace('Username:', '', $part);
                    } elseif (strpos($part, 'Password:') !== false) {
                        $portalPassword = str_replace('Password:', '', $part);
                    }
                }
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
                    'source_sheet' => $data['source_sheet'] ?? 'Google Sheets',
                    'status' => $data['status'] ?? null,
                    'category' => $data['category'] ?? null,
                    'contact_no' => $data['contact_no'] ?? null,
                    'student_portal' => $data['student_portal'] ?? null,
                    'total_fees' => !empty($data['total_fees']) ? $this->cleanNumericValue($data['total_fees']) : null,
                    'transaction_month' => $data['transaction_month'] ?? null,
                    'remarks' => $data['remarks'] ?? null,
                    'pic' => $data['pic'] ?? null,
                    'programme_name' => $data['programme_name'] ?? null,
                    'programme_code' => $data['programme_code'] ?? null,
                    'student_id' => $data['student_id'] ?? null,
                    'col_ref_no' => $data['col_ref_no'] ?? null,
                    'semester_entry' => $data['semester_entry'] ?? null,
                    'research_title' => $data['research_title'] ?? null,
                    'supervisor' => $data['supervisor'] ?? null,
                    'external_examiner' => $data['external_examiner'] ?? null,
                    'internal_examiner' => $data['internal_examiner'] ?? null,
                    'col_date' => !empty($data['col_date']) ? $this->convertDateFormat($data['col_date']) : null,
                    'programme_intake' => $data['programme_intake'] ?? null,
                    'date_of_commencement' => !empty($data['date_of_commencement']) ? $this->convertDateFormat($data['date_of_commencement']) : null,
                    'courses' => $courses,
                ]);

                Log::info('User created from Google Sheets', ['name' => $data['name'], 'ic' => $data['ic']]);
                return ['success' => true, 'action' => 'created'];
            } else {
                // Update existing user with all new fields
                $user->update([
                    'name' => $data['name'],
                    'phone' => $data['phone'] ?: $user->phone,
                    'address' => $data['address'] ?: $user->address,
                    'student_email' => $data['student_email'] ?? $user->student_email,
                    'password' => Hash::make($data['ic']),
                    'must_reset_password' => false,
                    'source_sheet' => $data['source_sheet'] ?? 'Google Sheets',
                    'status' => $data['status'] ?? $user->status,
                    'category' => $data['category'] ?? $user->category,
                    'contact_no' => $data['contact_no'] ?? $user->contact_no,
                    'student_portal' => $data['student_portal'] ?? $user->student_portal,
                    'total_fees' => !empty($data['total_fees']) ? $this->cleanNumericValue($data['total_fees']) : $user->total_fees,
                    'transaction_month' => $data['transaction_month'] ?? $user->transaction_month,
                    'remarks' => $data['remarks'] ?? $user->remarks,
                    'pic' => $data['pic'] ?? $user->pic,
                    'programme_name' => $data['programme_name'] ?? $user->programme_name,
                    'programme_code' => $data['programme_code'] ?? $user->programme_code,
                    'student_id' => $data['student_id'] ?? $user->student_id,
                    'col_ref_no' => $data['col_ref_no'] ?? $user->col_ref_no,
                    'semester_entry' => $data['semester_entry'] ?? $user->semester_entry,
                    'research_title' => $data['research_title'] ?? $user->research_title,
                    'supervisor' => $data['supervisor'] ?? $user->supervisor,
                    'external_examiner' => $data['external_examiner'] ?? $user->external_examiner,
                    'internal_examiner' => $data['internal_examiner'] ?? $user->internal_examiner,
                    'col_date' => !empty($data['col_date']) ? $this->convertDateFormat($data['col_date']) : $user->col_date,
                    'programme_intake' => $data['programme_intake'] ?? $user->programme_intake,
                    'date_of_commencement' => !empty($data['date_of_commencement']) ? $this->convertDateFormat($data['date_of_commencement']) : $user->date_of_commencement,
                    'courses' => $courses,
                ]);

                Log::info('User updated from Google Sheets', ['name' => $data['name'], 'ic' => $data['ic']]);
                return ['success' => true, 'action' => 'updated'];
            }
        } catch (\Exception $e) {
            Log::error('Error processing student from Google Sheets', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'action' => 'error'];
        }
    }

    private function convertDateFormat($dateString)
    {
        if (empty($dateString)) {
            return null;
        }
        
        // Handle formats like "14-Sep-25", "4-Sep-25", "13-Oct-25"
        if (preg_match('/^(\d{1,2})-([A-Za-z]{3})-(\d{2})$/', $dateString, $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = '20' . $matches[3]; // Convert 25 to 2025
            
            // Convert month name to number
            $monthMap = [
                'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
                'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
                'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
            ];
            
            if (isset($monthMap[$month])) {
                $monthNum = $monthMap[$month];
                return $year . '-' . $monthNum . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
            }
        }
        
        // If we can't parse it, return null to avoid database errors
        return null;
    }

    private function cleanNumericValue($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // Remove currency symbols, commas, and spaces
        $cleaned = preg_replace('/[^\d.]/', '', $value);
        
        // Convert to float if it's a valid number
        if (is_numeric($cleaned)) {
            return (float) $cleaned;
        }
        
        return null;
    }

    public function checkForChanges()
    {
        try {
            // Get the last modified time from cache
            $lastModifiedKey = 'google_sheets_last_modified';
            $lastModified = Cache::get($lastModifiedKey, 0);
            
            // Check if any of the LMS sheets have changes
            $hasChanges = false;
            
            foreach ($this->lmsSheets as $sheetName => $gid) {
                try {
                    $csvExportUrl = $this->baseUrl . '/export?format=csv&gid=' . $gid;
                    $response = Http::timeout(30)->get($csvExportUrl);
                    
                    if ($response->successful()) {
                        // Use the response timestamp as a simple change detection
                        $currentModified = time();
                        
                        if ($currentModified > $lastModified + 300) { // 5 minutes buffer
                            $hasChanges = true;
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("Error checking sheet {$sheetName} for changes: " . $e->getMessage());
                }
            }
            
            if ($hasChanges) {
                Cache::put($lastModifiedKey, time(), now()->addDays(1));
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Error checking Google Sheets for changes: ' . $e->getMessage());
            return false;
        }
    }
}
