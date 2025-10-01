<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class SheetSpecificImportService
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
        Log::info('Starting sheet-specific OneDrive Excel import', [
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

            // Load the Excel file with PhpSpreadsheet
            $reader = new Xlsx();
            $reader->setReadDataOnly(true);
            $reader->setReadEmptyCells(false);
            
            $spreadsheet = $reader->load($this->tempFilePath);
            $sheetNames = $spreadsheet->getSheetNames();
            
            Log::info('Available sheets in Excel file', [
                'sheet_names' => $sheetNames,
                'total_sheets' => count($sheetNames)
            ]);

            // Process each specified LMS sheet
            $sheetCount = count($this->lmsSheets);
            $currentSheet = 0;
            
            foreach ($this->lmsSheets as $sheetIndex => $sheetName) {
                $currentSheet++;
                Log::info("Processing sheet {$currentSheet}/{$sheetCount} - Index {$sheetIndex}: {$sheetName}");
                
                try {
                    // Check if the sheet exists
                    if (!isset($sheetNames[$sheetIndex - 1])) {
                        Log::warning("Sheet index {$sheetIndex} not found in Excel file", [
                            'available_sheets' => $sheetNames,
                            'requested_sheet' => $sheetName
                        ]);
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

                    // Get the specific sheet
                    $worksheet = $spreadsheet->getSheet($sheetIndex - 1); // PhpSpreadsheet uses 0-based indexing
                    $actualSheetName = $worksheet->getTitle();
                    
                    Log::info("Processing actual sheet: {$actualSheetName}", [
                        'requested_index' => $sheetIndex,
                        'actual_index' => $sheetIndex - 1
                    ]);

                    // Process the sheet data
                    $result = $this->processSheetData($worksheet, $sheetName);
                    
                    $created = $result['created'];
                    $updated = $result['updated'];
                    $errors = $result['errors'];
                    
                    $totalCreated += $created;
                    $totalUpdated += $updated;
                    $totalErrors += $errors;
                    
                    $processedSheets[] = [
                        'sheet' => $sheetName,
                        'sheet_index' => $sheetIndex,
                        'actual_sheet_name' => $actualSheetName,
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

            Log::info('Sheet-specific OneDrive Excel import completed', [
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
            Log::error('Sheet-specific OneDrive Excel import failed', [
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

    private function processSheetData($worksheet, $sheetName)
    {
        $created = 0;
        $updated = 0;
        $errors = 0;
        
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        
        Log::info("Processing sheet data", [
            'sheet_name' => $sheetName,
            'highest_row' => $highestRow,
            'highest_column' => $highestColumn
        ]);
        
        // Skip if no data
        if ($highestRow < 2) {
            Log::info("Sheet {$sheetName} has no data rows");
            return ['created' => 0, 'updated' => 0, 'errors' => 0];
        }
        
        // Get header row - try multiple rows to find the actual header
        $headers = [];
        $headerRow = 1;
        
        // Try to find the header row by looking for common header keywords
        // Scan more rows (up to 100) to find headers that might be in later rows
        // For specific LMS sheets, check known header row positions first
        $knownHeaderRows = [
            'DHU LMS' => 5,  // Headers are on row 5
            'IUC LMS' => 1,  // Headers are on row 1
            'LUC LMS' => 1,  // Headers are on row 1
            'UPM LMS' => 1,  // Headers are on row 1
        ];
        
        $rowsToCheck = [];
        if (isset($knownHeaderRows[$sheetName])) {
            // Check known header row first
            $rowsToCheck[] = $knownHeaderRows[$sheetName];
        }
        
        // Add other rows to check
        for ($row = 1; $row <= min(10, $highestRow); $row++) {
            if (!in_array($row, $rowsToCheck)) {
                $rowsToCheck[] = $row;
            }
        }
        
        foreach ($rowsToCheck as $row) {
            $testHeaders = [];
            // Force read all columns including empty ones to handle merged cells and formatting issues
            $rowIterator = $worksheet->getRowIterator($row, $row);
            foreach ($rowIterator as $rowIndex => $rowObject) {
                $cellIterator = $rowObject->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Important: read all cells
                
                $colIndex = 0;
                foreach ($cellIterator as $cell) {
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                    $testHeaders[$columnLetter] = trim($cell->getValue() ?? '');
                    $colIndex++;
                }
            }

            // Check if this row contains header keywords
            $headerKeywords = ['name', 'ic', 'passport', 'email', 'address', 'contact', 'phone', 'student', 'category', 'programme'];
            $keywordCount = 0;
            $nonNullCount = 0;
            $hasIcColumn = false;
            $hasNameColumn = false;
            $hasEmailColumn = false;
            $hasAddressColumn = false;

            foreach ($testHeaders as $header) {
                if ($header && is_string($header)) {
                    $nonNullCount++;
                    $headerLower = strtolower(trim($header));
                    
                    // More flexible header matching
                    $isHeader = false;
                    foreach ($headerKeywords as $keyword) {
                        if (strpos($headerLower, $keyword) !== false) {
                            $keywordCount++;
                            $isHeader = true;
                            
                            // Specifically check for IC/PASSPORT column
                            if (strpos($headerLower, 'ic') !== false || strpos($headerLower, 'passport') !== false) {
                                $hasIcColumn = true;
                            }
                            // Check for NAME column
                            if (strpos($headerLower, 'name') !== false && strpos($headerLower, 'programme') === false) {
                                $hasNameColumn = true;
                            }
                            // Check for EMAIL column
                            if (strpos($headerLower, 'email') !== false) {
                                $hasEmailColumn = true;
                            }
                            // Check for ADDRESS column
                            if (strpos($headerLower, 'address') !== false) {
                                $hasAddressColumn = true;
                            }
                            break;
                        }
                    }
                    
                    // If no keyword match, check if it looks like a header (short text, not empty)
                    if (!$isHeader && !empty($headerLower) && strlen($headerLower) < 50) {
                        $keywordCount++; // Count as potential header
                    }
                }
            }

            // Additional check: look for IC/PASSPORT pattern in any column
            $hasIcPassportPattern = false;
            foreach ($testHeaders as $header) {
                if ($header && is_string($header)) {
                    $headerLower = strtolower(trim($header));
                    if (strpos($headerLower, 'ic') !== false && strpos($headerLower, 'passport') !== false) {
                        $hasIcPassportPattern = true;
                        break;
                    }
                }
            }
            
            // More flexible header detection - if we find multiple header keywords, use this row
            // Also check for specific patterns like "IC/PASSPORT" or "IC / PASSPORT"
            
            if ($keywordCount >= 2 || 
                ($hasNameColumn && $hasIcColumn) || 
                ($hasNameColumn && $hasEmailColumn) ||
                ($hasNameColumn && $hasAddressColumn) ||
                ($hasIcColumn && $nonNullCount > 0) || 
                ($nonNullCount > 0 && $keywordCount >= 1) ||
                ($hasNameColumn && $nonNullCount > 0) ||
                $hasIcPassportPattern ||
                ($hasNameColumn && $hasIcPassportPattern)) {
                $headers = $testHeaders;
                $headerRow = $row;
                Log::info("Found header row", [
                    'sheet_name' => $sheetName,
                    'header_row' => $row,
                    'keyword_count' => $keywordCount,
                    'has_ic_column' => $hasIcColumn,
                    'has_name_column' => $hasNameColumn,
                    'has_email_column' => $hasEmailColumn,
                    'has_address_column' => $hasAddressColumn,
                    'has_ic_passport_pattern' => $hasIcPassportPattern,
                    'non_null_count' => $nonNullCount,
                    'headers' => $testHeaders
                ]);
                break;
            }
        }

        // If no proper header row found, try a different approach
        // Look for any row that has data in multiple columns and try to infer headers
        if (empty($headers) || count($headers) <= 1) {
            Log::info("No proper header row found, trying alternative approach", [
                'sheet_name' => $sheetName,
                'highest_row' => $highestRow,
                'highest_column' => $highestColumn
            ]);
            
            // Look for the first row that has data in multiple columns
            for ($row = 1; $row <= min(20, $highestRow); $row++) {
                $testHeaders = [];
                $dataCount = 0;
                
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellValue = $worksheet->getCell($col . $row)->getValue();
                    $testHeaders[$col] = $cellValue;
                    if ($cellValue && is_string($cellValue) && trim($cellValue) !== '') {
                        $dataCount++;
                    }
                }
                
                // If this row has data in multiple columns, use it as header
                if ($dataCount >= 2) {
                    // Create generic headers for columns with data
                    $genericHeaders = [];
                    $colIndex = 0;
                    foreach ($testHeaders as $col => $value) {
                        if ($value && is_string($value) && trim($value) !== '') {
                            $genericHeaders[$col] = 'COLUMN_' . chr(65 + $colIndex); // A, B, C, etc.
                            $colIndex++;
                        }
                    }
                    
                    $headers = $genericHeaders;
                    $headerRow = $row;
                    
                    Log::info("Using alternative header detection", [
                        'sheet_name' => $sheetName,
                        'header_row' => $row,
                        'data_count' => $dataCount,
                        'generic_headers' => $genericHeaders
                    ]);
                    break;
                }
            }
        }
        
        // Final fallback: if still no headers, scan all columns and create generic headers
        if (empty($headers) || count($headers) <= 1) {
            Log::info("Final fallback: creating headers for all columns with data", [
                'sheet_name' => $sheetName,
                'highest_column' => $highestColumn
            ]);
            
            $headers = [];
            $colIndex = 0;
            
            // Scan all columns to find any with data
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $hasData = false;
                
                // Check first 10 rows for data in this column
                for ($row = 1; $row <= min(10, $highestRow); $row++) {
                    $value = $worksheet->getCell($col . $row)->getValue();
                    if ($value && is_string($value) && trim($value) !== '') {
                        $hasData = true;
                        break;
                    }
                }
                
                if ($hasData) {
                    $headers[$col] = 'COLUMN_' . chr(65 + $colIndex);
                    $colIndex++;
                }
            }
            
            Log::info("Created fallback headers", [
                'sheet_name' => $sheetName,
                'headers' => $headers
            ]);
        }
        
        // If no header row found, use row 1 as default
        if (empty($headers)) {
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $headers[$col] = $worksheet->getCell($col . '1')->getValue();
            }
        }
        
            // For problematic sheets, if we only found NAME column, try to find more columns
            if (count($headers) == 1 && isset($headers['A']) && strtolower(trim($headers['A'])) == 'name') {
                Log::info("Only NAME column found, scanning for more columns", [
                    'sheet_name' => $sheetName,
                    'highest_column' => $highestColumn
                ]);
                
                // Try to find a row with more data to determine column structure
                // Scan more rows and be more aggressive in finding columns
                for ($row = 1; $row <= min(20, $highestRow); $row++) {
                    $testRow = [];
                    $nonNullCount = 0;
                    $hasIcPattern = false;
                    $hasEmailPattern = false;
                    
                    for ($col = 'A'; $col <= $highestColumn; $col++) {
                        $value = $worksheet->getCell($col . $row)->getValue();
                        $testRow[$col] = $value;
                        if ($value && is_string($value) && trim($value) !== '') {
                            $nonNullCount++;
                            $value = trim($value);
                            
                            // Check for IC patterns
                            if (preg_match('/^\d{6}-\d{2}-\d{4}$/', $value) || 
                                preg_match('/^\d{6}-\d{2}-\d{3}$/', $value) ||
                                preg_match('/^\d{6}-\d{2}-\d{5}$/', $value) ||
                                preg_match('/^[A-Z]\d{8}$/', $value) ||
                                preg_match('/^[A-Z]{2}\d{7}$/', $value) ||
                                preg_match('/^[A-Z]{3}\d{6}$/', $value) ||
                                preg_match('/^[A-Z]{4}\d{6}$/', $value)) {
                                $hasIcPattern = true;
                            }
                            
                            // Check for email patterns
                            if (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $value)) {
                                $hasEmailPattern = true;
                            }
                        }
                    }
                    
                    // If this row has more than 1 non-null value, or has IC/email patterns, use it to determine columns
                    if ($nonNullCount > 1 || $hasIcPattern || $hasEmailPattern) {
                        Log::info("Found row with multiple columns or patterns", [
                            'sheet_name' => $sheetName,
                            'row' => $row,
                            'non_null_count' => $nonNullCount,
                            'has_ic_pattern' => $hasIcPattern,
                            'has_email_pattern' => $hasEmailPattern,
                            'test_row' => $testRow
                        ]);
                        
                        // Create headers for all columns that have data
                        $headers = [];
                        for ($col = 'A'; $col <= $highestColumn; $col++) {
                            $value = $worksheet->getCell($col . $row)->getValue();
                            if ($value && is_string($value) && trim($value) !== '') {
                                $value = trim($value);
                                // Try to identify column types
                                if (preg_match('/^\d{6}-\d{2}-\d{4}$/', $value) || 
                                    preg_match('/^\d{6}-\d{2}-\d{3}$/', $value) ||
                                    preg_match('/^\d{6}-\d{2}-\d{5}$/', $value) ||
                                    preg_match('/^[A-Z]\d{8}$/', $value) ||
                                    preg_match('/^[A-Z]{2}\d{7}$/', $value) ||
                                    preg_match('/^[A-Z]{3}\d{6}$/', $value) ||
                                    preg_match('/^[A-Z]{4}\d{6}$/', $value)) {
                                    $headers[$col] = 'IC / PASSPORT';
                                } elseif (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $value)) {
                                    $headers[$col] = 'EMAIL';
                                } elseif (strlen($value) > 50) {
                                    $headers[$col] = 'ADDRESS';
                                } else {
                                    $headers[$col] = 'COLUMN_' . $col; // Generic column name
                                }
                            }
                        }
                        $headerRow = $row;
                        break;
                    }
                }
                
                // If still no additional columns found, try a different approach
                // Look for any column that might contain IC data
                if (count($headers) == 1) {
                    Log::info("Still only NAME column found, trying alternative approach", [
                        'sheet_name' => $sheetName
                    ]);
                    
                    // Scan all columns for IC patterns
                    for ($col = 'B'; $col <= $highestColumn; $col++) {
                        $hasIcData = false;
                        for ($row = 1; $row <= min(10, $highestRow); $row++) {
                            $value = $worksheet->getCell($col . $row)->getValue();
                            if ($value && is_string($value)) {
                                $value = trim($value);
                                if (preg_match('/^\d{6}-\d{2}-\d{4}$/', $value) || 
                                    preg_match('/^\d{6}-\d{2}-\d{3}$/', $value) ||
                                    preg_match('/^\d{6}-\d{2}-\d{5}$/', $value) ||
                                    preg_match('/^[A-Z]\d{8}$/', $value) ||
                                    preg_match('/^[A-Z]{2}\d{7}$/', $value) ||
                                    preg_match('/^[A-Z]{3}\d{6}$/', $value) ||
                                    preg_match('/^[A-Z]{4}\d{6}$/', $value)) {
                                    $hasIcData = true;
                                    break;
                                }
                            }
                        }
                        
                        if ($hasIcData) {
                            $headers[$col] = 'IC / PASSPORT';
                            Log::info("Found IC data in column", [
                                'sheet_name' => $sheetName,
                                'column' => $col
                            ]);
                        }
                    }
                }
            }
        
        Log::info("Sheet headers", [
            'sheet_name' => $sheetName,
            'headers' => $headers,
            'header_row' => $headerRow
        ]);
        
        // Debug: Log first few rows to understand the structure
        $debugRows = [];
        for ($debugRow = 1; $debugRow <= min(5, $highestRow); $debugRow++) {
            $debugRowData = [];
            for ($col = 'A'; $col <= min('Z', $highestColumn); $col++) {
                $debugRowData[$col] = $worksheet->getCell($col . $debugRow)->getValue();
            }
            $debugRows[] = $debugRowData;
        }
        
        Log::info("First 5 rows of sheet for debugging", [
            'sheet_name' => $sheetName,
            'rows' => $debugRows
        ]);
        
        // Process data rows (start after header row)
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            try {
                $rowData = [];
                // Force read all columns including empty ones to handle merged cells and formatting issues
                $rowIterator = $worksheet->getRowIterator($row, $row);
                foreach ($rowIterator as $rowIndex => $rowObject) {
                    $cellIterator = $rowObject->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false); // Important: read all cells
                    
                    $colIndex = 0;
                    foreach ($cellIterator as $cell) {
                        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                        $rowData[$columnLetter] = trim($cell->getValue() ?? '');
                        $colIndex++;
                    }
                }
                
                // Skip empty rows
                if (empty(array_filter($rowData))) {
                    continue;
                }
                
                    // For problematic sheets, try to find IC data even if headers are incomplete
                    if (in_array($sheetName, ['DHU LMS', 'IUC LMS', 'LUC LMS', 'UPM LMS'])) {
                        $this->enhanceRowDataWithIcDetection($rowData, $sheetName);
                    }
                    
                    // Also run enhanced IC detection for all sheets as a fallback
                    $this->enhanceRowDataWithIcDetection($rowData, $sheetName);
                
                // Process the student data
                $result = $this->processStudentRow($rowData, $headers, $sheetName);
                
                if ($result['created']) $created++;
                if ($result['updated']) $updated++;
                if ($result['error']) $errors++;
                
            } catch (\Exception $e) {
                Log::error("Error processing row {$row} in sheet {$sheetName}", [
                    'error' => $e->getMessage()
                ]);
                $errors++;
            }
        }
        
        return ['created' => $created, 'updated' => $updated, 'errors' => $errors];
    }
    
    private function enhanceRowDataWithIcDetection(&$rowData, $sheetName)
    {
        // This method enhances row data by scanning all columns for IC patterns
        // It's specifically designed for sheets where header detection fails
        
        foreach ($rowData as $col => $value) {
            if ($value && is_string($value)) {
                $value = trim($value);
                // Check for various IC/Passport patterns
                if (preg_match('/^\d{6}-\d{2}-\d{4}$/', $value) || 
                    preg_match('/^\d{6}-\d{2}-\d{3}$/', $value) ||
                    preg_match('/^\d{6}-\d{2}-\d{5}$/', $value) ||
                    preg_match('/^[A-Z]\d{8}$/', $value) ||  // Passport format like E88277018
                    preg_match('/^\d{6}-\d{2}-\d{2}$/', $value) ||
                    preg_match('/^\d{6}-\d{2}-\d{6}$/', $value) ||
                    preg_match('/^\d{6}-\d{2}-\d{1}$/', $value) ||
                    preg_match('/^[A-Z]\d{7}$/', $value) ||
                    preg_match('/^[A-Z]\d{9}$/', $value) ||
                    preg_match('/^\d{6}-\d{2}-\d{7}$/', $value) ||
                    preg_match('/^[A-Z]{2}\d{7}$/', $value) ||  // Alternative passport format like AA0204707
                    preg_match('/^[A-Z]{2}\d{6}$/', $value) ||  // Alternative passport format
                    preg_match('/^[A-Z]{2}\d{8}$/', $value) ||  // Alternative passport format
                    preg_match('/^[A-Z]{3}\d{6}$/', $value) ||  // Alternative passport format like LNRR95060
                    preg_match('/^[A-Z]{3}\d{7}$/', $value) ||  // Alternative passport format
                    preg_match('/^[A-Z]{4}\d{6}$/', $value) ||  // Alternative passport format like ZN6C12579
                    preg_match('/^[A-Z]{4}\d{7}$/', $value) ||  // Alternative passport format
                    preg_match('/\d{6}-\d{2}-\d+/', $value) ||
                    preg_match('/^[A-Z]\d{7,9}$/', $value) ||
                    preg_match('/^[A-Z]{2,4}\d{6,8}$/', $value) ||
                    preg_match('/^[A-Z]{1,4}\d{6,9}$/', $value)) {
                    
                    // Add this as a special IC column if not already mapped
                    if (!isset($rowData['IC_DETECTED'])) {
                        $rowData['IC_DETECTED'] = $value;
                        Log::info("Enhanced IC detection", [
                            'sheet' => $sheetName,
                            'column' => $col,
                            'ic' => $value
                        ]);
                    }
                    break;
                }
            }
        }
    }
    
    /**
     * Apply position-based mapping for specific LMS systems
     * This handles cases where IC/Passport is in specific columns regardless of header text
     */
    private function applyPositionBasedMapping(&$studentData, $rowData, $sheetName)
    {
        // Define column mappings for different LMS systems
        // Based on actual Excel structure analysis - ALL sheets have IC/Passport in Column C (index 2)
        $lmsColumnMappings = [
            'DHU LMS' => [
                'ic_column' => 'C', // Column C
                'name_column' => 'A', // Column A
                'email_column' => 'K', // Column K
                'phone_column' => 'J', // Column J
                'address_column' => 'B', // Column B
            ],
            'IUC LMS' => [
                'ic_column' => 'C', // Column C
                'name_column' => 'A', // Column A
                'email_column' => 'J', // Column J
                'phone_column' => 'I', // Column I
                'address_column' => 'B', // Column B
            ],
            'LUC LMS' => [
                'ic_column' => 'C', // Column C
                'name_column' => 'A', // Column A
                'email_column' => 'J', // Column J
                'phone_column' => 'I', // Column I
                'address_column' => 'B', // Column B
            ],
            'UPM LMS' => [
                'ic_column' => 'C', // Column C
                'name_column' => 'A', // Column A
                'email_column' => 'J', // Column J
                'phone_column' => 'I', // Column I
                'address_column' => 'B', // Column B
            ]
        ];
        
        // Check if we have a mapping for this sheet
        if (isset($lmsColumnMappings[$sheetName])) {
            $mapping = $lmsColumnMappings[$sheetName];
            
            Log::info("Applying position-based mapping for {$sheetName}", [
                'mapping' => $mapping,
                'row_data_keys' => array_keys($rowData)
            ]);
            
            // Map IC/Passport from specific column position
            if (isset($mapping['ic_column']) && isset($rowData[$mapping['ic_column']])) {
                $icValue = trim($rowData[$mapping['ic_column']]);
                if (!empty($icValue)) {
                    $studentData['ic'] = $icValue;
                    Log::info("Position-based IC mapping successful", [
                        'sheet' => $sheetName,
                        'column' => $mapping['ic_column'],
                        'value' => $icValue
                    ]);
                }
            }
            
            // Map other fields if not already set
            if (empty($studentData['name']) && isset($mapping['name_column']) && isset($rowData[$mapping['name_column']])) {
                $nameValue = trim($rowData[$mapping['name_column']]);
                Log::info("Position-based name mapping attempt", [
                    'sheet' => $sheetName,
                    'column' => $mapping['name_column'],
                    'value' => $nameValue,
                    'is_program_name' => $this->isProgramName($nameValue)
                ]);
                // Filter out program names and other non-student names
                if (!$this->isProgramName($nameValue)) {
                    $studentData['name'] = $nameValue;
                    Log::info("Position-based name mapping successful", [
                        'sheet' => $sheetName,
                        'name' => $nameValue
                    ]);
                } else {
                    Log::info("Position-based name mapping filtered out as program name", [
                        'sheet' => $sheetName,
                        'filtered_value' => $nameValue
                    ]);
                }
            }
            
            if (empty($studentData['email']) && isset($mapping['email_column']) && isset($rowData[$mapping['email_column']])) {
                $studentData['email'] = trim($rowData[$mapping['email_column']]);
            }
            
            if (empty($studentData['phone']) && isset($mapping['phone_column']) && isset($rowData[$mapping['phone_column']])) {
                $studentData['phone'] = trim($rowData[$mapping['phone_column']]);
            }
            
            if (empty($studentData['address']) && isset($mapping['address_column']) && isset($rowData[$mapping['address_column']])) {
                $studentData['address'] = trim($rowData[$mapping['address_column']]);
            }
        }
    }
    
    /**
     * Check if a value is a program name rather than a student name
     */
    private function isProgramName($value)
    {
        if (empty($value) || !is_string($value)) {
            return false;
        }
        
        $value = strtoupper(trim($value));
        
        // Common program name patterns
        $programPatterns = [
            'PHILOSOPHY',
            'DOCTOR',
            'MASTER',
            'BACHELOR',
            'DIPLOMA',
            'CERTIFICATE',
            'EXECUTIVE',
            'MANAGEMENT',
            'BUSINESS',
            'EDUCATION',
            'RESEARCH',
            'BY RESEARCH',
            'PROGRAMME',
            'PROGRAM',
            'DEGREE',
            'COURSE',
            'STUDY',
            'FACULTY',
            'DEPARTMENT',
            'SCHOOL',
            'INSTITUTE',
            'COLLEGE',
            'UNIVERSITY',
            'ACADEMY',
            'CENTER',
            'CENTRE',
            'LOCAL',
            'INTERNATIONAL',
            'COL.',
            'OEM',
            'PGD',
            'PHDP',
            'EMBA',
            'EBBA',
            'DEDM',
            'JPK',
            'HRDC',
            'TPN',
            'EDP',
            'MQA',
            'R/',
            'N/',
            'FA7968',
            'PA18014'
        ];
        
        // Check if the value contains program-related keywords
        foreach ($programPatterns as $pattern) {
            if (strpos($value, $pattern) !== false) {
                return true;
            }
        }
        
        // Check if it's too long to be a typical name (more than 50 characters)
        if (strlen($value) > 50) {
            return true;
        }
        
        // Check if it contains common program structure patterns (but be more specific)
        if (preg_match('/\b(OF|IN|BY|FOR|WITH)\b.*\b(DOCTOR|MASTER|BACHELOR|DIPLOMA|CERTIFICATE|DEGREE|PROGRAMME|PROGRAM|COURSE|STUDY|MANAGEMENT|BUSINESS|EDUCATION|RESEARCH)\b/', $value)) {
            return true;
        }
        
        // Check if it's all caps and contains multiple words (typical program names) - but be more lenient
        if (strtoupper($value) === $value && substr_count($value, ' ') >= 5) {
            return true;
        }
        
        // Check for reference numbers, codes, or IDs that look like program codes
        if (preg_match('/^[A-Z]{2,4}\.[A-Z]{2,4}\.[A-Z0-9]+/', $value)) {
            return true;
        }
        
        // Check for specific program code patterns
        if (preg_match('/^\(HRDC\/TPN\d+\/EDP\/\d+\)/', $value)) {
            return true;
        }
        
        if (preg_match('/^\(R\/\d+\/\d+\/\d+ \(MQA\/[A-Z0-9]+\)\d+\/\d+\)/', $value)) {
            return true;
        }
        
        if (preg_match('/^N\/\d+\/\d+\/\d+ \(MQA\/[A-Z0-9]+\)/', $value)) {
            return true;
        }
        
        if (preg_match('/^\(N\/\d+\/\d+\/\d+\) \(\d+\/\d+\) \(MQA\/[A-Z0-9]+\)/', $value)) {
            return true;
        }
        
        // Check for single words that are too short to be names (less than 3 characters)
        if (strlen($value) < 3) {
            return true;
        }
        
        // Check for values that are just dashes or single characters
        if (in_array($value, ['-', '--', '---', '.', '..', '...'])) {
            return true;
        }
        
        // Check for values that look like IC numbers but are being used as names
        if (preg_match('/^[A-Z]\d{6,9}$/', $value)) {
            return true;
        }
        
        // Check for purely numeric names (student IDs, etc.)
        if (preg_match('/^\d+$/', $value)) {
            return true;
        }
        
        // Check for names that are mostly numbers with some letters
        if (preg_match('/^\d+[A-Z]?\d*$/', $value)) {
            return true;
        }
        
        return false;
    }
    
    private function processStudentRow($rowData, $headers, $sheetName)
    {
        $created = false;
        $updated = false;
        $error = false;
        
        try {
        // Map the row data to a more usable format
        $studentData = [];
        
        // First, try position-based mapping for specific LMS systems
        $this->applyPositionBasedMapping($studentData, $rowData, $sheetName);
        
        // Then apply header-based mapping for any remaining fields
        foreach ($headers as $col => $header) {
            if ($header && isset($rowData[$col])) {
                $cleanHeader = strtolower(trim($header));
                // Handle specific header variations
                if (strpos($cleanHeader, 'ic') !== false || strpos($cleanHeader, 'passport') !== false) {
                    $studentData['ic'] = trim($rowData[$col]);
                } elseif (strpos($cleanHeader, 'ic/passport') !== false || strpos($cleanHeader, 'ic / passport') !== false) {
                    $studentData['ic'] = trim($rowData[$col]);
                } elseif (strpos($cleanHeader, 'name') !== false && strpos($cleanHeader, 'programme') === false) {
                    // Only map to name if it's not a programme name
                    $nameValue = trim($rowData[$col]);
                    if (!$this->isProgramName($nameValue)) {
                        $studentData['name'] = $nameValue;
                    }
                } elseif (strpos($cleanHeader, 'email') !== false) {
                    $studentData['email'] = trim($rowData[$col]);
                } elseif (strpos($cleanHeader, 'contact') !== false || strpos($cleanHeader, 'phone') !== false) {
                    $studentData['phone'] = trim($rowData[$col]);
                } elseif (strpos($cleanHeader, 'address') !== false) {
                    $studentData['address'] = trim($rowData[$col]);
                } elseif (strpos($cleanHeader, 'student') !== false && strpos($cleanHeader, 'id') !== false) {
                    $studentData['student_id'] = trim($rowData[$col]);
                } elseif (strpos($cleanHeader, 'programme') !== false && strpos($cleanHeader, 'name') !== false) {
                    // Store programme name separately, don't use as student name
                    $studentData['programme_name'] = trim($rowData[$col]);
                } else {
                    $studentData[$cleanHeader] = trim($rowData[$col]);
                }
            }
        }
        
        // If we have generic headers (COLUMN_A, COLUMN_B, etc.), try to identify data types
        if ((empty($studentData['name']) || empty($studentData['ic'])) && is_array($headers)) {
            foreach ($headers as $col => $header) {
                if (isset($rowData[$col]) && $rowData[$col]) {
                    $value = trim($rowData[$col]);
                    
                    // If no name found yet, and this looks like a name (not IC/email/address)
                    if (empty($studentData['name']) && 
                        !preg_match('/^\d{6}-\d{2}-\d{4}$/', $value) && 
                        !preg_match('/^[A-Z]\d{8}$/', $value) &&
                        !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $value) &&
                        strlen($value) < 100 &&
                        !$this->isProgramName($value)) {
                        $studentData['name'] = $value;
                        Log::info("Generic header name mapping", [
                            'sheet' => $sheetName,
                            'column' => $col,
                            'header' => $header,
                            'name' => $value
                        ]);
                    }
                    
                    // If no IC found yet, check if this looks like an IC/Passport
                    if (empty($studentData['ic']) && 
                        (preg_match('/^\d{6}-\d{2}-\d{4}$/', $value) || 
                         preg_match('/^\d{6}-\d{2}-\d{3}$/', $value) ||
                         preg_match('/^\d{6}-\d{2}-\d{5}$/', $value) ||
                         preg_match('/^[A-Z]\d{8}$/', $value) ||
                         preg_match('/^[A-Z]{2}\d{7}$/', $value) ||
                         preg_match('/^[A-Z]{3}\d{6}$/', $value) ||
                         preg_match('/^[A-Z]{4}\d{6}$/', $value))) {
                        $studentData['ic'] = $value;
                    }
                    
                    // If no email found yet, check if this looks like an email
                    if (empty($studentData['email']) && 
                        preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $value)) {
                        $studentData['email'] = $value;
                    }
                }
            }
        }
            
            // Use enhanced IC detection if available
            if (empty($studentData['ic']) && isset($rowData['IC_DETECTED'])) {
                $studentData['ic'] = $rowData['IC_DETECTED'];
                Log::info("Using enhanced IC detection", [
                    'sheet' => $sheetName,
                    'ic' => $rowData['IC_DETECTED']
                ]);
            }
            
            // Comprehensive IC detection - scan all data regardless of headers
            // This is a more aggressive approach to find IC/Passport data
            if (empty($studentData['ic'])) {
                foreach ($rowData as $col => $value) {
                    if ($value && is_string($value)) {
                        $value = trim($value);
                        // Check if this looks like an IC number (Malaysian IC format: YYMMDD-PB-GGGG)
                        // Also check for passport formats (letters and numbers)
                        if (preg_match('/^\d{6}-\d{2}-\d{4}$/', $value) || 
                            preg_match('/^\d{6}-\d{2}-\d{3}$/', $value) ||
                            preg_match('/^\d{6}-\d{2}-\d{5}$/', $value) ||
                            preg_match('/^[A-Z]\d{8}$/', $value) ||  // Passport format like E88277018
                            preg_match('/^\d{6}-\d{2}-\d{2}$/', $value) ||  // Alternative IC format
                            preg_match('/^\d{6}-\d{2}-\d{6}$/', $value) ||  // Alternative IC format
                            preg_match('/^\d{6}-\d{2}-\d{1}$/', $value) ||  // Alternative IC format
                            preg_match('/^[A-Z]\d{7}$/', $value) ||  // Alternative passport format
                            preg_match('/^[A-Z]\d{9}$/', $value) ||  // Alternative passport format
                            preg_match('/^\d{6}-\d{2}-\d{7}$/', $value) ||  // Alternative IC format
                            preg_match('/^[A-Z]{2}\d{7}$/', $value) ||  // Alternative passport format like AA0204707
                            preg_match('/^[A-Z]{2}\d{6}$/', $value) ||  // Alternative passport format
                            preg_match('/^[A-Z]{2}\d{8}$/', $value) ||  // Alternative passport format
                            preg_match('/^[A-Z]{3}\d{6}$/', $value) ||  // Alternative passport format like LNRR95060
                            preg_match('/^[A-Z]{3}\d{7}$/', $value) ||  // Alternative passport format
                            preg_match('/^[A-Z]{4}\d{6}$/', $value) ||  // Alternative passport format like ZN6C12579
                            preg_match('/^[A-Z]{4}\d{7}$/', $value)) {  // Alternative passport format
                            $studentData['ic'] = $value;
                            Log::info("Comprehensive IC detection", [
                                'sheet' => $sheetName,
                                'column' => $col,
                                'ic' => $value,
                                'row_data' => $rowData
                            ]);
                            break;
                        }
                    }
                }
            }
            
            // Additional fallback: if still no IC found, try to find any column that might contain IC data
            // by looking for patterns that might be IC numbers even if they don't match exact formats
            if (empty($studentData['ic'])) {
                foreach ($rowData as $col => $value) {
                    if ($value && is_string($value)) {
                        $value = trim($value);
                        // Look for any string that contains digits and dashes (potential IC)
                        // or alphanumeric patterns that could be passport numbers
                        if (preg_match('/\d{6}-\d{2}-\d+/', $value) || 
                            preg_match('/^[A-Z]\d{7,9}$/', $value) ||
                            preg_match('/^[A-Z]{2,4}\d{6,8}$/', $value) ||
                            preg_match('/^[A-Z]{1,4}\d{6,9}$/', $value)) {
                            $studentData['ic'] = $value;
                            Log::info("Pattern-based IC detection", [
                                'sheet' => $sheetName,
                                'column' => $col,
                                'ic' => $value,
                                'row_data' => $rowData
                            ]);
                            break;
                        }
                    }
                }
            }
            
            // Debug: Log the mapped data
            Log::info("Mapped student data", [
                'sheet' => $sheetName,
                'mapped_data' => $studentData,
                'original_headers' => $headers,
                'raw_row_data' => $rowData
            ]);
            
            // Skip if no essential data or if name looks like a program name
            if (empty($studentData['name'])) {
                Log::warning("Skipping row - no name found", [
                    'sheet' => $sheetName,
                    'mapped_data' => $studentData
                ]);
                return ['created' => false, 'updated' => false, 'error' => false];
            }
            
            // Skip students if both IC/Passport and email are missing
            if (empty($studentData['ic']) && empty($studentData['email'])) {
                Log::info("Skipping student - both IC/Passport and email data missing", [
                    'sheet' => $sheetName,
                    'name' => $studentData['name'] ?? 'Unknown',
                    'mapped_data' => $studentData
                ]);
                return ['created' => false, 'updated' => false, 'error' => false];
            }
            
            // Temporarily disable program name filtering to see what students are being skipped
            // TODO: Re-enable after fixing the mapping issue
            
            // Find existing student by IC or email
            $student = null;
            if (!empty($studentData['ic'])) {
                $student = User::where('ic', $studentData['ic'])->first();
            }
            if (!$student && !empty($studentData['email'])) {
                $student = User::where('email', $studentData['email'])->first();
            }
            
            if ($student) {
                // Update existing student
                $this->updateStudent($student, $studentData, $sheetName);
                $updated = true;
            } else {
                // Create new student
                $this->createStudent($studentData, $sheetName);
                $created = true;
            }
            
        } catch (\Exception $e) {
            Log::error("Error processing student row in sheet {$sheetName}", [
                'error' => $e->getMessage(),
                'row_data' => $rowData
            ]);
            $error = true;
        }
        
        return ['created' => $created, 'updated' => $updated, 'error' => $error];
    }
    
    private function createStudent($data, $sheetName)
    {
        $student = new User();
        $student->name = $data['name'] ?? 'Unknown';
        
        // Generate unique email if not provided to avoid database constraint violations
        if (empty($data['email'])) {
            $ic = $data['ic'] ?? 'UNKNOWN';
            $student->email = 'student_' . $ic . '_' . time() . '@lms.local';
        } else {
            $student->email = $data['email'];
        }
        
        $student->ic = $data['ic'] ?? 'UNKNOWN';
        $student->password = Hash::make('0000');
        $student->role = 'student';
        $student->must_reset_password = false;
        $student->source_sheet = $sheetName;
        
        // Add other fields if available
        if (isset($data['phone'])) $student->phone = $data['phone'];
        if (isset($data['address'])) $student->address = $data['address'];
        if (isset($data['student_id'])) $student->student_id = $data['student_id'];
        
        $student->save();
        
        Log::info("Created new student", [
            'name' => $student->name,
            'ic' => $student->ic,
            'email' => $student->email,
            'source_sheet' => $sheetName
        ]);
    }
    
    private function updateStudent($student, $data, $sheetName)
    {
        $updated = false;
        
        if (isset($data['name']) && $student->name !== $data['name']) {
            $student->name = $data['name'];
            $updated = true;
        }
        
        if (isset($data['email']) && $student->email !== $data['email']) {
            // Only update email if the new email is not empty
            if (!empty($data['email'])) {
                $student->email = $data['email'];
                $updated = true;
            }
        }
        
        if (isset($data['phone']) && $student->phone !== $data['phone']) {
            $student->phone = $data['phone'];
            $updated = true;
        }
        
        if (isset($data['address']) && $student->address !== $data['address']) {
            $student->address = $data['address'];
            $updated = true;
        }
        
        if (isset($data['student_id']) && $student->student_id !== $data['student_id']) {
            $student->student_id = $data['student_id'];
            $updated = true;
        }
        
        $student->source_sheet = $sheetName;
        
        if ($updated) {
            $student->save();
            Log::info("Updated student", [
                'id' => $student->id,
                'name' => $student->name,
                'ic' => $student->ic,
                'source_sheet' => $sheetName
            ]);
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
