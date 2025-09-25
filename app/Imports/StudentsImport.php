<?php
namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class StudentsImport implements ToCollection
{
    protected $allowedSheets = ['DHU LMS', 'IUC LMS', 'VIVA-IUC LMS', 'LUC LMS'];
    protected $currentSheet = '';
    
    // Statistics tracking
    public $created = 0;
    public $updated = 0;
    public $errors = 0;
    
    // Count tracking (for backward compatibility)
    protected $createdCount = 0;
    protected $updatedCount = 0;
    protected $errorCount = 0;

    public function setCurrentSheet($sheetName)
    {
        $this->currentSheet = $sheetName;
    }
    
    public function getStats()
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'errors' => $this->errors
        ];
    }
    
    // Store detailed error information
    protected $errorDetails = [];
    
    public function getErrorDetails()
    {
        return $this->errorDetails;
    }
    
    protected function addError($message, $rowData = [])
    {
        $this->errors++;
        $this->errorDetails[] = [
            'message' => $message,
            'data' => $rowData,
            'timestamp' => now()
        ];
        Log::error('Import Error: ' . $message, $rowData);
    }

    public function collection(Collection $rows): void
    {
        Log::info('Starting import from sheet "' . $this->currentSheet . '" with ' . $rows->count() . ' total rows');
        
        // For LMS sheets, headers are typically on row 1, data starts from row 2
        // Check multiple rows to find headers (some sheets have empty first rows)
        $headerRowIndex = 0;
        $headerRow = null;
        $hasHeaderKeywords = false;
        
        // Check first 10 rows for headers
        for ($i = 0; $i < min(10, $rows->count()); $i++) {
            $currentRow = $rows->skip($i)->first();
            $currentRowArray = is_array($currentRow) ? $currentRow : $currentRow->toArray();
            $currentRowText = implode(' ', array_filter($currentRowArray, function($cell) {
                return !empty(trim($cell));
            }));
            
            // Check if this row contains header keywords
            $headerKeywords = ['name', 'ic', 'passport', 'email', 'address', 'contact', 'student', 'programme', 'learners'];
            foreach ($headerKeywords as $keyword) {
                if (stripos($currentRowText, $keyword) !== false) {
                    $hasHeaderKeywords = true;
                    $headerRowIndex = $i;
                    $headerRow = $currentRow;
                    break 2; // Break out of both loops
                }
            }
        }
        
        Log::info('Header row found at index: ' . $headerRowIndex);
        if ($headerRow) {
            $headerRowArray = is_array($headerRow) ? $headerRow : $headerRow->toArray();
            Log::info('Header row text: ' . implode(' ', array_filter($headerRowArray, function($cell) {
                return !empty(trim($cell));
            })));
        }
        
        if ($hasHeaderKeywords) {
            // Headers found, process them
            Log::info('Headers detected on row ' . ($headerRowIndex + 1) . ', data starts from row ' . ($headerRowIndex + 2));
            $header = collect($headerRow)->map(function($h) {
                // Remove BOM and trim
                $h = trim($h);
                // Remove UTF-8 BOM if present
                if (substr($h, 0, 3) === "\xEF\xBB\xBF") {
                    $h = substr($h, 3);
                }
                return trim($h);
            })->toArray();
            
            // Don't filter out empty headers - keep all columns to match data rows
            // $header = array_filter($header, function($h) {
            //     return !empty(trim($h));
            // });
            
            // Skip rows up to and including the header row
            $rows = $rows->slice($headerRowIndex + 1);
            Log::info('After skipping header rows, remaining: ' . $rows->count() . ' rows');
        } else {
            // Fallback to old logic: headers on row 7, data from row 10
            Log::info('No headers detected on row 1, using fallback logic (headers on row 7)');
            $rows = $rows->slice(6);
            Log::info('After skipping first 6 rows, remaining: ' . $rows->count() . ' rows');
            
            // Get header row (now the first row after skipping)
            $firstRow = $rows->shift();
            $header = collect($firstRow)->map(function($h) {
                // Remove BOM and trim
                $h = trim($h);
                // Remove UTF-8 BOM if present
                if (substr($h, 0, 3) === "\xEF\xBB\xBF") {
                    $h = substr($h, 3);
                }
                return trim($h);
            })->toArray();
            
            // Skip the next 2 rows (program name and category rows)
            $rows = $rows->slice(2);
            Log::info('After skipping program name and category rows, remaining: ' . $rows->count() . ' rows');
        }
        Log::info('First row after headers (should be data):', $rows->first() ? (is_array($rows->first()) ? $rows->first() : $rows->first()->toArray()) : []);
        
        Log::info('Detected headers:', $header);
        
        // Dynamic mapping based on actual header content
        $mappedHeader = [];
        foreach ($header as $index => $headerName) {
            $originalHeader = $headerName;
            $headerName = strtolower(trim($headerName));
            
            Log::info("Processing header {$index}: '{$originalHeader}' -> '{$headerName}'");
            
            // Skip empty headers but keep the index mapping
            if (empty($headerName)) {
                $mappedHeader[$index] = 'empty_' . $index;
                Log::info("Mapped to 'empty_{$index}' (empty header)");
                continue;
            }
            
            // Map based on header content, not position
            if ((strpos($headerName, 'name') !== false || strpos($headerName, 'learners') !== false) && 
                strpos($headerName, 'programme') === false && 
                strpos($headerName, 'program') === false &&
                strpos($headerName, 'progame') === false &&
                strpos($headerName, 'programe') === false) {
                $mappedHeader[$index] = 'name';
                Log::info("Mapped to 'name'");
            } elseif (strpos($headerName, 'address') !== false) {
                $mappedHeader[$index] = 'address';
                Log::info("Mapped to 'address'");
            } elseif (strpos($headerName, 'ic') !== false || strpos($headerName, 'passport') !== false) {
                $mappedHeader[$index] = 'ic/passport';
                Log::info("Mapped to 'ic/passport'");
            } elseif (strpos($headerName, 'email') !== false) {
                $mappedHeader[$index] = 'email';
                Log::info("Mapped to 'email'");
            } elseif (strpos($headerName, 'contact') !== false || strpos($headerName, 'phone') !== false) {
                $mappedHeader[$index] = 'contact no.';
                Log::info("Mapped to 'contact no.'");
            } elseif (strpos($headerName, 'student id') !== false || strpos($headerName, 'id student') !== false) {
                $mappedHeader[$index] = 'student id';
                Log::info("Mapped to 'student id'");
            } elseif (strpos($headerName, 'col ref') !== false) {
                $mappedHeader[$index] = 'col ref. no.';
                Log::info("Mapped to 'col ref. no.'");
            } elseif (strpos($headerName, 'previous university') !== false) {
                $mappedHeader[$index] = 'previous university';
                Log::info("Mapped to 'previous university'");
            } elseif (strpos($headerName, 'programme name') !== false || strpos($headerName, 'program name') !== false) {
                $mappedHeader[$index] = 'programme name';
                Log::info("Mapped to 'programme name'");
            } elseif (strpos($headerName, 'category') !== false) {
                $mappedHeader[$index] = 'category';
                Log::info("Mapped to 'category'");
            } else {
                $mappedHeader[$index] = 'unknown_' . $index;
                Log::info("Mapped to 'unknown_{$index}'");
            }
        }
        
        Log::info('Mapped headers:', $mappedHeader);
        
        // Reset statistics
        $this->created = 0;
        $this->updated = 0;
        $this->errors = 0;

        foreach ($rows as $index => $row) {
            $rowArray = is_array($row) ? $row : $row->toArray();
            
            // Skip empty rows
            if (empty(array_filter($rowArray))) {
                Log::info("Skipping empty row " . ($index + 1));
                continue;
            }
            
            // Skip rows that contain program names or categories instead of student data
            $firstColumnValue = $rowArray[0] ?? '';
            if (is_string($firstColumnValue) && (
                stripos($firstColumnValue, 'PHILOSOPHY') !== false ||
                stripos($firstColumnValue, 'INTERNATIONAL') !== false ||
                stripos($firstColumnValue, 'LOCAL') !== false ||
                stripos($firstColumnValue, 'PROGRAMME') !== false ||
                stripos($firstColumnValue, 'DOCTOR') !== false ||
                stripos($firstColumnValue, 'MASTER') !== false ||
                stripos($firstColumnValue, 'BACHELOR') !== false
            )) {
                Log::info("Skipping program/category row " . ($index + 1) . " - contains: " . $firstColumnValue);
                continue;
            }
            
            Log::info("Processing row " . ($index + 1) . " with " . count($rowArray) . " columns");
            Log::info("Row data:", $rowArray);

                   // skip rows that don't match header column count
                   if (count($header) !== count($rowArray)) {
                       $this->addError("Column count mismatch. Headers: " . count($header) . ", Row: " . count($rowArray), $rowArray);
                       continue;
                   }

            // Create a flexible mapping that handles different column counts
            $data = [];
            for ($i = 0; $i < count($rowArray); $i++) {
                $key = $mappedHeader[$i] ?? 'unknown_' . $i;
                if ($key !== 'skip') {
                    $value = trim($rowArray[$i] ?? '');
                    // Handle blank content as "-"
                    $data[$key] = empty($value) ? '-' : $value;
                }
            }
            Log::info("Combined data for row " . ($index + 1) . ":", $data);

                   // Check if we have required fields (treat "-" as valid)
                   // For certain sheet types, make IC and email optional
                   $isResearchSheet = stripos($this->currentSheet, 'VIVA') !== false || 
                                     stripos($this->currentSheet, 'RESEARCH') !== false ||
                                     stripos($this->currentSheet, 'TVET') !== false;
                   
                   $hasName = !empty($data['name']) && $data['name'] !== '-';
                   $hasIc = !empty($data['ic/passport']) && $data['ic/passport'] !== '-';
                   $hasEmail = !empty($data['email']) && $data['email'] !== '-';
                   
                   if (!$hasName) {
                       $this->addError("Missing required field 'name' for row " . ($index + 1), [
                           'name' => $data['name'] ?? 'MISSING',
                           'ic/passport' => $data['ic/passport'] ?? 'MISSING', 
                           'email' => $data['email'] ?? 'MISSING'
                       ]);
                       continue;
                   }
                   
                   // Generate default values for missing IC and email for all sheets
                   if (!$hasIc) {
                       $data['ic/passport'] = 'AUTO-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $data['name']), 0, 8)) . '-' . date('Y');
                   }
                   if (!$hasEmail) {
                       $emailName = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $data['name']));
                       $data['email'] = $emailName . '@olympia.edu.my';
                   }

            // Check if we have the required fields with flexible matching
            $hasName = false;
            $hasEmail = false;
            $hasIc = false;
            
            foreach ($data as $key => $value) {
                $keyLower = strtolower(trim($key));
                if (strpos($keyLower, 'name') !== false && !empty($value) && $value !== '-') {
                    $hasName = true;
                }
                if (strpos($keyLower, 'email') !== false && !empty($value) && $value !== '-') {
                    $hasEmail = true;
                }
                if ((strpos($keyLower, 'ic') !== false || strpos($keyLower, 'passport') !== false) && !empty($value) && $value !== '-') {
                    $hasIc = true;
                }
            }
            
            if (!$hasName || !$hasEmail || !$hasIc) {
                $this->addError('Missing required fields after flexible matching', [
                    'hasName' => $hasName,
                    'hasEmail' => $hasEmail,
                    'hasIc' => $hasIc,
                    'data' => $data
                ]);
                continue;
            }

            // Create validator for the extracted data
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'ic/passport' => 'required|string|max:255',
                'contact no.' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                $this->addError('Student import validation failed for row ' . ($index + 1), [
                    'data' => $data,
                    'errors' => $validator->errors()->toArray(),
                    'available_keys' => array_keys($data)
                ]);
                continue;
            }

            // Extract data with flexible column matching
            $name = '';
            $email = '';
            $ic = '';
            $phone = '';
            $address = '';
            $colRefNo = '';
            $studentId = '';
            $category = '';
            $programmeName = '';
            $faculty = '';
            $programmeCode = '';
            $semesterEntry = '';
            $researchTitle = '';
            $supervisor = '';
            $externalExaminer = '';
            $internalExaminer = '';
            $studentPortal = '';
            $programmeIntake = '';
            $dateOfCommencement = '';
            $colDate = '';

            foreach ($data as $key => $value) {
                $keyLower = strtolower(trim($key));
                $value = trim($value);
                
                if (strpos($keyLower, 'name') !== false && empty($name)) {
                    $name = $value;
                } elseif (strpos($keyLower, 'email') !== false && empty($email)) {
                    $email = $value;
                } elseif ((strpos($keyLower, 'ic') !== false || strpos($keyLower, 'passport') !== false) && empty($ic)) {
                    $ic = $value;
                } elseif ((strpos($keyLower, 'contact') !== false || strpos($keyLower, 'phone') !== false) && empty($phone)) {
                    $phone = $value;
                } elseif (strpos($keyLower, 'address') !== false && empty($address)) {
                    $address = $value;
                } elseif ((strpos($keyLower, 'col') !== false && strpos($keyLower, 'ref') !== false) && empty($colRefNo)) {
                    $colRefNo = $value;
                } elseif ((strpos($keyLower, 'student') !== false && strpos($keyLower, 'id') !== false) && empty($studentId)) {
                    $studentId = $value;
                } elseif (strpos($keyLower, 'category') !== false && empty($category)) {
                    $category = $value;
                } elseif ((strpos($keyLower, 'programme') !== false || strpos($keyLower, 'program') !== false) && strpos($keyLower, 'name') !== false && empty($programmeName)) {
                    $programmeName = $value;
                } elseif (strpos($keyLower, 'faculty') !== false && empty($faculty)) {
                    $faculty = $value;
                } elseif ((strpos($keyLower, 'programme') !== false || strpos($keyLower, 'program') !== false) && strpos($keyLower, 'code') !== false && empty($programmeCode)) {
                    $programmeCode = $value;
                } elseif (strpos($keyLower, 'semester') !== false && empty($semesterEntry)) {
                    $semesterEntry = $value;
                } elseif ((strpos($keyLower, 'research') !== false || strpos($keyLower, 'title') !== false) && empty($researchTitle)) {
                    $researchTitle = $value;
                } elseif (strpos($keyLower, 'supervisor') !== false && empty($supervisor)) {
                    $supervisor = $value;
                } elseif (strpos($keyLower, 'external') !== false && empty($externalExaminer)) {
                    $externalExaminer = $value;
                } elseif (strpos($keyLower, 'internal') !== false && empty($internalExaminer)) {
                    $internalExaminer = $value;
                } elseif (strpos($keyLower, 'portal') !== false && empty($studentPortal)) {
                    $studentPortal = $value;
                } elseif (strpos($keyLower, 'intake') !== false && empty($programmeIntake)) {
                    $programmeIntake = $value;
                } elseif (strpos($keyLower, 'commencement') !== false && empty($dateOfCommencement)) {
                    $dateOfCommencement = $value;
                } elseif (strpos($keyLower, 'col') !== false && strpos($keyLower, 'date') !== false && empty($colDate)) {
                    $colDate = $value;
                }
            }

            // find by IC first, then by email if IC not found
            $user = User::where('ic', $ic)->first();
            
            if (!$user) {
                $user = User::where('email', $email)->first();
            }

            // Use IC as password for all students
            $studentPassword = $ic;

            // Parse courses if provided (using programme name as course)
            $courses = [];
            if (!empty($programmeName)) {
                $courses = array_map('trim', explode(',', $programmeName));
            }

            // Parse student portal credentials
            $portalUsername = '';
            $portalPassword = '';
            if (!empty($studentPortal)) {
                $parts = explode(' ', $studentPortal);
                foreach ($parts as $part) {
                    if (strpos($part, 'Username:') !== false) {
                        $portalUsername = str_replace('Username:', '', $part);
                    } elseif (strpos($part, 'Password:') !== false) {
                        $portalPassword = str_replace('Password:', '', $part);
                    }
                }
            }

            try {
                if (!$user) {
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'ic' => $ic,
                        'phone' => $phone ?: null,
                        'address' => $address ?: null,
                        'col_ref_no' => $colRefNo ?: null,
                        'student_id' => $studentId ?: null,
                        'source_sheet' => $this->currentSheet,
                        'password' => Hash::make($studentPassword),
                        'role' => 'student',
                        'must_reset_password' => false,
                        'courses' => $courses,
                        // Academic Information
                        'category' => $category ?: null,
                        'programme_name' => $programmeName ?: null,
                        'faculty' => $faculty ?: null,
                        'programme_code' => $programmeCode ?: null,
                        'semester_entry' => $semesterEntry ?: null,
                        'programme_intake' => $programmeIntake ?: null,
                        'date_of_commencement' => $dateOfCommencement ?: null,
                        // Research Information
                        'research_title' => $researchTitle ?: null,
                        'supervisor' => $supervisor ?: null,
                        'external_examiner' => $externalExaminer ?: null,
                        'internal_examiner' => $internalExaminer ?: null,
                        // Student Portal Information
                        'student_portal_username' => $portalUsername,
                        'student_portal_password' => $portalPassword,
                        // Additional Dates
                        'col_date' => $colDate ?: null,
                    ]);
                    $this->created++;
                    Log::info('Student created', ['name' => $name, 'ic' => $ic, 'sheet' => $this->currentSheet]);
                } else {
                    // update fields if changed, keep password as "0000"
                    $user->update([
                        'name' => $name,
                        'phone' => $phone ?: $user->phone,
                        'address' => $address ?: $user->address,
                        'col_ref_no' => $colRefNo ?: $user->col_ref_no,
                        'student_id' => $studentId ?: $user->student_id,
                        'source_sheet' => $this->currentSheet,
                        'password' => Hash::make($studentPassword),
                        'courses' => $courses,
                        'must_reset_password' => false,
                        // Academic Information
                        'category' => $category ?: $user->category,
                        'programme_name' => $programmeName ?: $user->programme_name,
                        'faculty' => $faculty ?: $user->faculty,
                        'programme_code' => $programmeCode ?: $user->programme_code,
                        'semester_entry' => $semesterEntry ?: $user->semester_entry,
                        'programme_intake' => $programmeIntake ?: $user->programme_intake,
                        'date_of_commencement' => $dateOfCommencement ?: $user->date_of_commencement,
                        // Research Information
                        'research_title' => $researchTitle ?: $user->research_title,
                        'supervisor' => $supervisor ?: $user->supervisor,
                        'external_examiner' => $externalExaminer ?: $user->external_examiner,
                        'internal_examiner' => $internalExaminer ?: $user->internal_examiner,
                        // Student Portal Information
                        'student_portal_username' => $portalUsername ?: $user->student_portal_username,
                        'student_portal_password' => $portalPassword ?: $user->student_portal_password,
                        // Additional Dates
                        'col_date' => $colDate ?: $user->col_date,
                    ]);
                    $this->updated++;
                    Log::info('Student updated', ['name' => $name, 'ic' => $ic, 'sheet' => $this->currentSheet]);
                }
                   } catch (\Exception $e) {
                       Log::error('Error processing student', [
                           'data' => $data,
                           'error' => $e->getMessage()
                       ]);
                       $this->errors++;
                       continue;
                   }
        }
        
        // Log import summary
        Log::info('Student import completed', [
            'created' => $this->created,
            'updated' => $this->updated,
            'errors' => $this->errors,
            'total_processed' => $this->created + $this->updated + $this->errors,
            'sheet' => $this->currentSheet
        ]);
        
        // Store counts for retrieval (backward compatibility)
        $this->createdCount = $this->created;
        $this->updatedCount = $this->updated;
        $this->errorCount = $this->errors;
    }
    
    public function getCreatedCount()
    {
        return $this->createdCount;
    }
    
    public function getUpdatedCount()
    {
        return $this->updatedCount;
    }
    
    public function getErrorCount()
    {
        return $this->errorCount;
    }
}