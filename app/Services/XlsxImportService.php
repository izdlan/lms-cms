<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class XlsxImportService
{
    public function importFromXlsx($filePath)
    {
        $created = 0;
        $updated = 0;
        $errors = 0;

        Log::info('Starting XLSX import', ['file' => $filePath]);

        if (!file_exists($filePath)) {
            Log::error('XLSX file not found: ' . $filePath);
            return ['success' => false, 'created' => 0, 'updated' => 0, 'errors' => 1, 'message' => 'File not found: ' . $filePath];
        }

        // Check if ZipArchive is available
        if (!class_exists('ZipArchive')) {
            Log::error('ZipArchive class not available. Please enable zip extension in PHP.');
            return ['success' => false, 'created' => 0, 'updated' => 0, 'errors' => 1, 'message' => 'ZipArchive extension not available. Please convert your Excel file to CSV format.'];
        }

        try {
            // Read XLSX file using simple XML parsing
            $zip = new \ZipArchive();
            $openResult = $zip->open($filePath);
            Log::info('ZipArchive open result', ['result' => $openResult, 'file' => $filePath]);
            
            if ($openResult === TRUE) {
                // Read the shared strings
                $sharedStrings = [];
                $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
                if ($sharedStringsXml) {
                    $xml = simplexml_load_string($sharedStringsXml);
                    if ($xml && isset($xml->si)) {
                        foreach ($xml->si as $si) {
                            $sharedStrings[] = (string)$si->t;
                        }
                    }
                }

                // Only process specific sheets: 11, 12, 13, 14, 15, 16, 17
                $targetSheets = [11, 12, 13, 14, 15, 16, 17];
                $sheetsToProcess = [];
                
                // Get list of available sheets
                $workbookXml = $zip->getFromName('xl/workbook.xml');
                if ($workbookXml) {
                    $workbook = simplexml_load_string($workbookXml);
                    if (isset($workbook->sheets->sheet)) {
                        $sheetIndex = 0;
                        foreach ($workbook->sheets->sheet as $sheet) {
                            $sheetName = (string)$sheet['name'];
                            $sheetId = (string)$sheet['sheetId'];
                            
                            // Only include sheets 11-17
                            if (in_array($sheetId, $targetSheets)) {
                                $sheetsToProcess[] = [
                                    'name' => $sheetName,
                                    'id' => $sheetId,
                                    'file' => 'xl/worksheets/sheet' . $sheetId . '.xml'
                                ];
                                Log::info('Including sheet for processing', ['name' => $sheetName, 'id' => $sheetId]);
                            } else {
                                Log::info('Skipping sheet', ['name' => $sheetName, 'id' => $sheetId]);
                            }
                            $sheetIndex++;
                        }
                    }
                }
                
                Log::info('Processing only specific sheets', ['target_sheets' => $targetSheets, 'sheets_to_process' => count($sheetsToProcess)]);
                
                // Process each sheet
                foreach ($sheetsToProcess as $sheetInfo) {
                    Log::info('Processing sheet: ' . $sheetInfo['name'], [
                    'sheet_name' => $sheetInfo['name'], 
                    'file' => $sheetInfo['file'],
                    'timestamp' => now()->setTimezone('Asia/Kuala_Lumpur')->toDateTimeString()
                ]);
                    
                    // Read the worksheet
                    $worksheetXml = $zip->getFromName($sheetInfo['file']);
                    if ($worksheetXml) {
                    $xml = simplexml_load_string($worksheetXml);
                    if ($xml && isset($xml->sheetData->row)) {
                        $rows = [];
                        foreach ($xml->sheetData->row as $row) {
                            $rowData = [];
                            if (isset($row->c)) {
                                foreach ($row->c as $cell) {
                                    $value = '';
                                    if (isset($cell->v)) {
                                        $cellValue = (string)$cell->v;
                                        // Check if it's a shared string
                                        if (isset($cell['t']) && $cell['t'] == 's') {
                                            $value = isset($sharedStrings[$cellValue]) ? $sharedStrings[$cellValue] : '';
                                        } else {
                                            $value = $cellValue;
                                        }
                                    }
                                    $rowData[] = $value;
                                }
                            }
                            $rows[] = $rowData;
                        }

                        // Process the rows
                        if (!empty($rows)) {
                            // Check if this is a DHU LMS sheet by looking for the specific header pattern
                            $isDhulmsSheet = false;
                            for ($i = 0; $i < min(10, count($rows)); $i++) {
                                $firstCell = $rows[$i][0] ?? '';
                                if (stripos($firstCell, 'NAME') !== false && stripos($rows[$i][1] ?? '', 'ADDRESS') !== false) {
                                    $isDhulmsSheet = true;
                                    $header = $rows[$i];
                                    $rows = array_slice($rows, $i + 1); // Data starts from next row
                                    break;
                                }
                            }
                            
                            if (!$isDhulmsSheet) {
                                // For regular LMS Excel files, headers are typically in row 7 (index 6)
                                // Skip first 6 rows and use row 7 as header
                                if (count($rows) >= 7) {
                                    $header = $rows[6]; // Row 7 (index 6) contains headers
                                    $rows = array_slice($rows, 7); // Data starts from row 8
                                } else {
                                    $header = array_shift($rows); // Fallback to first row
                                }
                            }
                            
                            // Remove BOM and normalize headers
                            $header = array_map(function($h) {
                                $h = trim($h);
                                if (substr($h, 0, 3) === "\xEF\xBB\xBF") {
                                    $h = substr($h, 3);
                                }
                                return trim($h);
                            }, $header);

                            Log::info('XLSX Headers detected', ['headers' => $header]);

                            foreach ($rows as $rowIndex => $row) {
                                if (count($header) !== count($row)) {
                                    Log::warning('Row column count mismatch', [
                                        'expected' => count($header),
                                        'actual' => count($row),
                                        'row' => $row
                                    ]);
                                    $errors++;
                                    continue;
                                }

                                $data = array_combine($header, $row);
                                
                                // Skip empty rows
                                if (empty(array_filter($data))) {
                                    continue;
                                }
                                
                                // Skip rows that contain program names or categories instead of student data
                                $firstColumnValue = $data[array_key_first($data)] ?? '';
                                if (is_string($firstColumnValue) && (
                                    stripos($firstColumnValue, 'PHILOSOPHY') !== false ||
                                    stripos($firstColumnValue, 'INTERNATIONAL') !== false ||
                                    stripos($firstColumnValue, 'LOCAL') !== false ||
                                    stripos($firstColumnValue, 'PROGRAMME') !== false ||
                                    stripos($firstColumnValue, 'DOCTOR') !== false ||
                                    stripos($firstColumnValue, 'MASTER') !== false ||
                                    stripos($firstColumnValue, 'BACHELOR') !== false ||
                                    stripos($firstColumnValue, 'FILE STATUS') !== false
                                )) {
                                    Log::info("Skipping program/category row " . ($rowIndex + 1) . " - contains: " . $firstColumnValue);
                                    continue;
                                }

                                // Extract data with flexible column matching
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
                                
                                // Log successful extraction for debugging
                                Log::info('Successfully extracted student data', [
                                    'name' => $extractedData['name'] ?? 'N/A',
                                    'email' => $extractedData['email'] ?? 'N/A',
                                    'ic' => $extractedData['ic'] ?? 'N/A'
                                ]);

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
                        }
                    }
                    } // Close the if ($worksheetXml) block
                } // Close the foreach ($sheetsToProcess) loop
                $zip->close();
            } else {
                Log::error('Could not open XLSX file: ' . $filePath, ['result' => $openResult]);
                return ['success' => false, 'created' => 0, 'updated' => 0, 'errors' => 1, 'message' => 'Could not open XLSX file. Error code: ' . $openResult];
            }

        } catch (\Exception $e) {
            Log::error('Error processing XLSX file: ' . $e->getMessage());
            return ['success' => false, 'created' => 0, 'updated' => 0, 'errors' => 1];
        }

        // Log professional completion message
        $message = "Student Import System: Successfully processed Excel file";
        if ($created > 0 || $updated > 0) {
            $message .= " - {$created} new students added, {$updated} existing students updated";
        } else {
            $message .= " - No student data changes detected";
        }
        
        if ($errors > 0) {
            $message .= " with {$errors} validation warnings";
        }
        
        Log::info($message, [
            'total_created' => $created,
            'total_updated' => $updated,
            'total_errors' => $errors,
            'file' => $filePath,
            'timestamp' => now()->setTimezone('Asia/Kuala_Lumpur')->toDateTimeString()
        ]);

        return [
            'success' => true,
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors
        ];
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
        
        if (!$hasName || !$hasIc) {
            Log::warning('Missing required fields', [
                'hasName' => $hasName,
                'hasEmail' => $hasEmail,
                'hasIc' => $hasIc,
                'data' => $data
            ]);
            return false;
        }
        
        // Extract data with flexible column matching
        $extracted = [
            'name' => '',
            'email' => '',
            'ic' => '',
            'phone' => '',
            'address' => '',
            'colRefNo' => '',
            'studentId' => '',
            'category' => '',
            'programmeName' => '',
            'faculty' => '',
            'programmeCode' => '',
            'semesterEntry' => '',
            'researchTitle' => '',
            'supervisor' => '',
            'externalExaminer' => '',
            'internalExaminer' => '',
            'studentPortal' => '',
            'programmeIntake' => '',
            'dateOfCommencement' => '',
            'colDate' => ''
        ];

        foreach ($data as $key => $value) {
            $keyLower = strtolower(trim($key));
            $value = trim($value);
            
            if (strpos($keyLower, 'name') !== false && empty($extracted['name'])) {
                $extracted['name'] = $value;
            } elseif (strpos($keyLower, 'email') !== false && empty($extracted['email'])) {
                $extracted['email'] = $value;
            } elseif ((strpos($keyLower, 'ic') !== false || strpos($keyLower, 'passport') !== false) && empty($extracted['ic'])) {
                $extracted['ic'] = $value;
            } elseif ((strpos($keyLower, 'contact') !== false || strpos($keyLower, 'phone') !== false) && empty($extracted['phone'])) {
                $extracted['phone'] = $value;
            } elseif (strpos($keyLower, 'address') !== false && empty($extracted['address'])) {
                $extracted['address'] = $value;
            } elseif ((strpos($keyLower, 'col') !== false && strpos($keyLower, 'ref') !== false) && empty($extracted['colRefNo'])) {
                $extracted['colRefNo'] = $value;
            } elseif ((strpos($keyLower, 'student') !== false && strpos($keyLower, 'id') !== false) && empty($extracted['studentId'])) {
                $extracted['studentId'] = $value;
            } elseif (strpos($keyLower, 'category') !== false && empty($extracted['category'])) {
                $extracted['category'] = $value;
            } elseif ((strpos($keyLower, 'programme') !== false || strpos($keyLower, 'program') !== false) && strpos($keyLower, 'name') !== false && empty($extracted['programmeName'])) {
                $extracted['programmeName'] = $value;
            } elseif (strpos($keyLower, 'faculty') !== false && empty($extracted['faculty'])) {
                $extracted['faculty'] = $value;
            } elseif ((strpos($keyLower, 'programme') !== false || strpos($keyLower, 'program') !== false) && strpos($keyLower, 'code') !== false && empty($extracted['programmeCode'])) {
                $extracted['programmeCode'] = $value;
            } elseif (strpos($keyLower, 'semester') !== false && empty($extracted['semesterEntry'])) {
                $extracted['semesterEntry'] = $value;
            } elseif ((strpos($keyLower, 'research') !== false || strpos($keyLower, 'title') !== false) && empty($extracted['researchTitle'])) {
                $extracted['researchTitle'] = $value;
            } elseif (strpos($keyLower, 'supervisor') !== false && empty($extracted['supervisor'])) {
                $extracted['supervisor'] = $value;
            } elseif (strpos($keyLower, 'external') !== false && empty($extracted['externalExaminer'])) {
                $extracted['externalExaminer'] = $value;
            } elseif (strpos($keyLower, 'internal') !== false && empty($extracted['internalExaminer'])) {
                $extracted['internalExaminer'] = $value;
            } elseif (strpos($keyLower, 'portal') !== false && empty($extracted['studentPortal'])) {
                $extracted['studentPortal'] = $value;
            } elseif (strpos($keyLower, 'intake') !== false && empty($extracted['programmeIntake'])) {
                $extracted['programmeIntake'] = $value;
            } elseif (strpos($keyLower, 'commencement') !== false && empty($extracted['dateOfCommencement'])) {
                $extracted['dateOfCommencement'] = $value;
            } elseif (strpos($keyLower, 'col') !== false && strpos($keyLower, 'date') !== false && empty($extracted['colDate'])) {
                $extracted['colDate'] = $value;
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
                // Create new user
                User::create([
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
                ]);

                Log::info('Student created from XLSX', ['name' => $data['name'], 'ic' => $data['ic']]);
                return ['success' => true, 'action' => 'created'];
            } else {
                // Update existing user
                $user->update([
                    'name' => $data['name'],
                    'phone' => $data['phone'] ?: $user->phone,
                    'address' => $data['address'] ?: $user->address,
                    'col_ref_no' => $data['colRefNo'] ?: $user->col_ref_no,
                    'student_id' => $data['studentId'] ?: $user->student_id,
                    'password' => Hash::make($data['ic']),
                    'courses' => $courses,
                    'must_reset_password' => false,
                ]);

                Log::info('Student updated from XLSX', ['name' => $data['name'], 'ic' => $data['ic']]);
                return ['success' => true, 'action' => 'updated'];
            }
        } catch (\Exception $e) {
            Log::error('Error processing student from XLSX', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'action' => 'error'];
        }
    }
}
