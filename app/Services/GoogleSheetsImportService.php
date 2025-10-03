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
    
    // LMS Sheets configuration - specific sheets by index (11-17)
    private $lmsSheets = [
        11 => 'DHU LMS',
        12 => 'IUC LMS', 
        13 => 'VIVA-IUC LMS',
        14 => 'LUC LMS',
        15 => 'EXECUTIVE LMS',
        16 => 'UPM LMS',
        17 => 'TVET LMS'
    ];
    
    public function __construct()
    {
        // Get configuration from config file
        $config = config('google_sheets', []);
        
        // Get the Google Sheets URL from config or environment
        $this->googleSheetsUrl = $config['url'] ?? env('GOOGLE_SHEETS_URL', 'https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk/edit?usp=sharing&ouid=117738643589016699947&rtpof=true&sd=true');
        
        // Extract the base URL for CSV exports
        if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $this->googleSheetsUrl, $matches)) {
            $this->baseUrl = 'https://docs.google.com/spreadsheets/d/' . $matches[1];
        } else {
            // Fallback to the hardcoded URL if parsing fails
            $this->baseUrl = 'https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk';
        }
        
        // Update LMS sheets from config if available
        if (isset($config['lms_sheets'])) {
            $this->lmsSheets = $config['lms_sheets'];
        }
        
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
                // Use sheet index instead of sheet name for Google Sheets export
                $csvExportUrl = $this->baseUrl . '/export?format=csv&gid=' . $sheetIndex;
                
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
        $lines = explode("\n", $csvData);
        $rows = [];
        $currentRow = null;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $parsedLine = str_getcsv($line);
                
                // If we have a complete row (26 columns), add it
                if (count($parsedLine) >= 26) {
                    if ($currentRow !== null) {
                        // We had a partial row, merge it
                        $this->mergePartialRow($currentRow, $parsedLine);
                        $rows[] = $currentRow;
                        $currentRow = null;
                    } else {
                        $rows[] = $parsedLine;
                    }
                } else if (count($parsedLine) < 26 && !empty($parsedLine[0])) {
                    // This is a partial row (multiline address)
                    if ($currentRow === null) {
                        $currentRow = $parsedLine;
                    } else {
                        // Merge with existing partial row
                        $this->mergePartialRow($currentRow, $parsedLine);
                    }
                } else {
                    // Skip empty rows
                    continue;
                }
            }
        }
        
        // Add any remaining partial row
        if ($currentRow !== null) {
            $rows[] = $currentRow;
        }
        
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
            // Try to access one of the LMS sheets using sheet name
            $testSheet = 'DHU LMS';
            $testUrl = $this->baseUrl . '/export?format=csv&sheet=' . urlencode($testSheet);
            $response = Http::timeout(10)->get($testUrl);
            
            if ($response->successful()) {
                Log::info('Google Sheets access validated successfully', ['sheet' => $testSheet]);
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

        // Find the header row (look for "NAME" and "ADDRESS" columns)
        $headerRowIndex = -1;
        $header = [];
        
        for ($i = 0; $i < min(10, count($rows)); $i++) {
            $row = $rows[$i];
            if (count($row) > 3) {
                $firstCell = $row[0] ?? '';
                $secondCell = $row[1] ?? '';
                $thirdCell = $row[2] ?? '';
                $fourthCell = $row[3] ?? '';
                
                // Check for new format: NO | NAME | ADDRESS | IC/PASSPORT
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
            
            // Skip if first column is not numeric and doesn't look like a student row
            if (!is_numeric($firstColumnValue) && 
                !stripos($firstColumnValue, 'PHILOSOPHY') && 
                !stripos($firstColumnValue, 'INTERNATIONAL') && 
                !stripos($firstColumnValue, 'LOCAL') &&
                !stripos($firstColumnValue, 'TOTAL LEARNERS') &&
                !stripos($firstColumnValue, 'FILE STATUS')) {
                Log::info("Skipping non-student row " . ($rowIndex + 1) . " - not numeric: " . $firstColumnValue);
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
        // Check if we have the required fields with flexible matching
        $hasName = false;
        $hasEmail = false;
        $hasIc = false;
        
        foreach ($data as $key => $value) {
            $keyLower = strtolower(trim($key));
            if (strpos($keyLower, 'name') !== false && !empty($value)) {
                $hasName = true;
            }
            if (strpos($keyLower, 'email') !== false && !empty($value)) {
                $hasEmail = true;
            }
            if ((strpos($keyLower, 'ic') !== false || strpos($keyLower, 'passport') !== false) && !empty($value)) {
                $hasIc = true;
            }
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
        
        // Extract data with flexible column matching - only fields that exist in database
        $extracted = [
            'name' => '',
            'email' => '',
            'ic' => '',
            'phone' => '',
            'address' => '',
            'colRefNo' => '',
            'studentId' => '',
            'previousUniversity' => '',
            'programmeName' => ''
        ];

        // For new format, try direct column mapping first
        if (count($data) >= 9) {
            // Based on actual Google Sheets structure:
            // Column 1=NAME, Column 3=IC/PASSPORT, Column 8=EMAIL, Column 7=PHONE, Column 2=ADDRESS
            $extracted['name'] = trim($data[1] ?? '');
            $extracted['ic'] = trim($data[3] ?? '');
            $extracted['email'] = trim($data[8] ?? '');
            $extracted['phone'] = trim($data[7] ?? '');
            $extracted['address'] = trim($data[2] ?? '');
            $extracted['colRefNo'] = trim($data[5] ?? '');
            $extracted['studentId'] = trim($data[6] ?? '');
            $extracted['previousUniversity'] = trim($data[4] ?? '');
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
            } elseif (strpos($keyLower, 'previous') !== false && strpos($keyLower, 'university') !== false && empty($extracted['previousUniversity'])) {
                $extracted['previousUniversity'] = $value;
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
            // Find existing user by IC or email
            $user = User::where('ic', $data['ic'])->first();
            if (!$user) {
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
                // Create new user - use the same fields as Excel import
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'ic' => $data['ic'],
                    'phone' => $data['phone'] ?: null,
                    'address' => $data['address'] ?: null,
                    'col_ref_no' => $data['colRefNo'] ?: null,
                    'student_id' => $data['studentId'] ?: null,
                    'password' => Hash::make($data['ic']),
                    'role' => 'student',
                    'must_reset_password' => false,
                    'courses' => $courses,
                    'programme_name' => $data['programmeName'] ?: null,
                    'source_sheet' => $data['source_sheet'] ?? 'Google Sheets',
                    'previous_university' => $data['previousUniversity'] ?: null,
                ]);

                Log::info('User created from Google Sheets', ['name' => $data['name'], 'ic' => $data['ic']]);
                return ['success' => true, 'action' => 'created'];
            } else {
                // Update existing user - use the same fields as Excel import
                $user->update([
                    'name' => $data['name'],
                    'phone' => $data['phone'] ?: $user->phone,
                    'address' => $data['address'] ?: $user->address,
                    'col_ref_no' => $data['colRefNo'] ?: $user->col_ref_no,
                    'student_id' => $data['studentId'] ?: $user->student_id,
                    'password' => Hash::make($data['ic']),
                    'courses' => $courses,
                    'programme_name' => $data['programmeName'] ?: $user->programme_name,
                    'must_reset_password' => false,
                    'source_sheet' => $data['source_sheet'] ?? 'Google Sheets',
                    'previous_university' => $data['previousUniversity'] ?: $user->previous_university,
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
