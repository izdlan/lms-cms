<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SheetSpecificImportService;

class ExcelDataImportController extends Controller
{
    /**
     * Import Excel data from Python OneDrive bridge
     */
    public function importExcelData(Request $request)
    {
        try {
            Log::info('Python OneDrive Bridge: Starting Excel data import', [
                'timestamp' => $request->input('timestamp'),
                'source' => $request->input('source'),
                'sheets_count' => count($request->input('sheets', []))
            ]);
            
            $sheetsData = $request->input('sheets', []);
            
            if (empty($sheetsData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No sheet data provided'
                ], 400);
            }
            
            $totalCreated = 0;
            $totalUpdated = 0;
            $totalErrors = 0;
            $processedSheets = [];
            
            // Process each sheet
            foreach ($sheetsData as $sheetName => $sheetData) {
                Log::info("Processing sheet from Python: {$sheetName}", [
                    'headers' => $sheetData['headers'] ?? [],
                    'row_count' => $sheetData['row_count'] ?? 0
                ]);
                
                try {
                    // Convert Python data to format expected by import service
                    $result = $this->processSheetData($sheetName, $sheetData);
                    
                    $totalCreated += $result['created'];
                    $totalUpdated += $result['updated'];
                    $totalErrors += $result['errors'];
                    
                    $processedSheets[] = [
                        'sheet' => $sheetName,
                        'created' => $result['created'],
                        'updated' => $result['updated'],
                        'errors' => $result['errors']
                    ];
                    
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
            
            Log::info('Python OneDrive Bridge: Import completed', [
                'total_created' => $totalCreated,
                'total_updated' => $totalUpdated,
                'total_errors' => $totalErrors,
                'processed_sheets' => $processedSheets
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Imported {$totalCreated} new students, updated {$totalUpdated} students. Errors: {$totalErrors}",
                'created' => $totalCreated,
                'updated' => $totalUpdated,
                'errors' => $totalErrors,
                'processed_sheets' => $processedSheets,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Python OneDrive Bridge: Import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }
    
    /**
     * Process individual sheet data from Python
     */
    private function processSheetData($sheetName, $sheetData)
    {
        $created = 0;
        $updated = 0;
        $errors = 0;
        
        $headers = $sheetData['headers'] ?? [];
        $rows = $sheetData['data'] ?? [];
        
        if (empty($headers) || empty($rows)) {
            Log::warning("Empty sheet data for {$sheetName}");
            return ['created' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        // Find the header row (look for NAME, IC/PASSPORT columns)
        $headerRowIndex = -1;
        for ($i = 0; $i < min(5, count($rows)); $i++) {
            $row = $rows[$i];
            if (count($row) > 2) {
                $firstCell = $row[0] ?? '';
                $secondCell = $row[1] ?? '';
                $thirdCell = $row[2] ?? '';
                
                // Check for header pattern
                if ((stripos($firstCell, 'NAME') !== false || stripos($firstCell, 'NO') !== false) && 
                    (stripos($secondCell, 'NAME') !== false || stripos($thirdCell, 'NAME') !== false) &&
                    (stripos($secondCell, 'IC') !== false || stripos($thirdCell, 'IC') !== false || 
                     stripos($secondCell, 'PASSPORT') !== false || stripos($thirdCell, 'PASSPORT') !== false)) {
                    $headerRowIndex = $i;
                    break;
                }
            }
        }
        
        if ($headerRowIndex === -1) {
            Log::warning("Could not find header row in sheet {$sheetName}");
            return ['created' => 0, 'updated' => 0, 'errors' => 1];
        }
        
        // Process data rows
        $dataRows = array_slice($rows, $headerRowIndex + 1);
        
        foreach ($dataRows as $rowIndex => $row) {
            try {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Skip program/category rows
                $firstValue = $row[0] ?? '';
                $secondValue = $row[1] ?? '';
                
                if ($this->isProgramName($firstValue) || $this->isProgramName($secondValue)) {
                    continue;
                }
                
                // Skip if no name in expected position
                if (empty(trim($secondValue))) {
                    continue;
                }
                
                // Extract student data based on common patterns
                $studentData = $this->extractStudentDataFromRow($row, $headers);
                
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
                Log::error("Error processing row in sheet {$sheetName}", [
                    'row_index' => $rowIndex,
                    'error' => $e->getMessage()
                ]);
                $errors++;
            }
        }
        
        return ['created' => $created, 'updated' => $updated, 'errors' => $errors];
    }
    
    /**
     * Check if a value is a program name (not a student name)
     */
    private function isProgramName($value)
    {
        if (empty($value) || !is_string($value)) {
            return false;
        }
        
        $programPatterns = [
            'PHILOSOPHY', 'DOCTOR', 'MASTER', 'BACHELOR', 'DIPLOMA', 'CERTIFICATE',
            'DEGREE', 'PROGRAMME', 'PROGRAM', 'COURSE', 'STUDY', 'MANAGEMENT',
            'BUSINESS', 'EDUCATION', 'RESEARCH', 'INTERNATIONAL', 'LOCAL',
            'TOTAL LEARNERS', 'FILE STATUS', 'HRDC', 'TPN', 'EDP', 'MQA'
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
    
    /**
     * Extract student data from a row
     */
    private function extractStudentDataFromRow($row, $headers)
    {
        // Common column patterns
        $nameIndex = -1;
        $icIndex = -1;
        $emailIndex = -1;
        $phoneIndex = -1;
        $addressIndex = -1;
        
        // Find column indices
        foreach ($headers as $index => $header) {
            $headerLower = strtolower(trim($header));
            
            if (strpos($headerLower, 'name') !== false && $nameIndex === -1) {
                $nameIndex = $index;
            } elseif ((strpos($headerLower, 'ic') !== false || strpos($headerLower, 'passport') !== false) && $icIndex === -1) {
                $icIndex = $index;
            } elseif (strpos($headerLower, 'email') !== false && $emailIndex === -1) {
                $emailIndex = $index;
            } elseif ((strpos($headerLower, 'phone') !== false || strpos($headerLower, 'contact') !== false) && $phoneIndex === -1) {
                $phoneIndex = $index;
            } elseif (strpos($headerLower, 'address') !== false && $addressIndex === -1) {
                $addressIndex = $index;
            }
        }
        
        // Extract data
        $name = ($nameIndex >= 0 && isset($row[$nameIndex])) ? trim($row[$nameIndex]) : '';
        $ic = ($icIndex >= 0 && isset($row[$icIndex])) ? trim($row[$icIndex]) : '';
        $email = ($emailIndex >= 0 && isset($row[$emailIndex])) ? trim($row[$emailIndex]) : '';
        $phone = ($phoneIndex >= 0 && isset($row[$phoneIndex])) ? trim($row[$phoneIndex]) : '';
        $address = ($addressIndex >= 0 && isset($row[$addressIndex])) ? trim($row[$addressIndex]) : '';
        
        // Skip if no name
        if (empty($name)) {
            return null;
        }
        
        // Generate email if not provided
        if (empty($email) && !empty($ic)) {
            $email = 'student_' . $ic . '_' . time() . '@lms.local';
        }
        
        return [
            'name' => $name,
            'ic' => $ic,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'source_sheet' => 'OneDrive Python Bridge'
        ];
    }
    
    /**
     * Process individual student data
     */
    private function processStudent($data, $sheetName)
    {
        try {
            $user = \App\Models\User::where('ic', $data['ic'])->first();
            
            if (!$user) {
                $user = \App\Models\User::where('email', $data['email'])->first();
            }
            
            if (!$user) {
                // Create new user
                $user = \App\Models\User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'ic' => $data['ic'],
                    'phone' => $data['phone'] ?: null,
                    'address' => $data['address'] ?: null,
                    'password' => \Illuminate\Support\Facades\Hash::make($data['ic']),
                    'role' => 'student',
                    'must_reset_password' => false,
                    'source_sheet' => $data['source_sheet']
                ]);
                
                Log::info('User created from OneDrive Python Bridge', [
                    'name' => $data['name'],
                    'ic' => $data['ic']
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
                
                Log::info('User updated from OneDrive Python Bridge', [
                    'name' => $data['name'],
                    'ic' => $data['ic']
                ]);
                
                return ['success' => true, 'action' => 'updated'];
            }
            
        } catch (\Exception $e) {
            Log::error('Error processing student from OneDrive Python Bridge', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'action' => 'error'];
        }
    }
}


