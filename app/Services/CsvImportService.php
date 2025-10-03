<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CsvImportService
{
    public function importFromCsv($filePath)
    {
        $created = 0;
        $updated = 0;
        $errors = 0;

        if (!file_exists($filePath)) {
            Log::error('CSV file not found: ' . $filePath);
            return false;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            Log::error('Could not open CSV file: ' . $filePath);
            return false;
        }

        // Read header row
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            Log::error('Could not read header from CSV file');
            return false;
        }

        // Remove BOM and normalize header names
        $header = array_map(function($h) {
            // Remove BOM and trim
            $h = trim($h);
            // Remove UTF-8 BOM if present
            if (substr($h, 0, 3) === "\xEF\xBB\xBF") {
                $h = substr($h, 3);
            }
            return trim($h);
        }, $header);

        Log::info('CSV Headers detected', ['headers' => $header]);

        while (($row = fgetcsv($handle)) !== false) {
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
            
            // Only process rows that contain "LMS" in any field (indicating student data)
            $hasLmsData = false;
            foreach ($data as $value) {
                if (stripos($value, 'LMS') !== false) {
                    $hasLmsData = true;
                    break;
                }
            }
            
            if (!$hasLmsData) {
                continue; // Skip non-LMS rows
            }

            // Extract data with flexible column matching
            $extractedData = $this->extractStudentData($data);
            
            if (!$extractedData) {
                $errors++;
                continue;
            }

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

        fclose($handle);

        // Log import summary
        Log::info('CSV import completed', [
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
            'total_processed' => $created + $updated + $errors
        ]);

        return [
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
            'success' => true
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
        
        if (!$hasName || !$hasEmail || !$hasIc) {
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
            } elseif ((strpos($keyLower, 'programme') !== false || strpos($keyLower, 'program') !== false || strpos($keyLower, 'programe') !== false) && strpos($keyLower, 'name') !== false && empty($extracted['programmeName'])) {
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
                    'category' => $data['category'] ?: null,
                    'programme_name' => $data['programmeName'] ?: null,
                    'faculty' => $data['faculty'] ?: null,
                    'programme_code' => $data['programmeCode'] ?: null,
                    'semester_entry' => $data['semesterEntry'] ?: null,
                    'programme_intake' => $data['programmeIntake'] ?: null,
                    'date_of_commencement' => $data['dateOfCommencement'] ?: null,
                    'research_title' => $data['researchTitle'] ?: null,
                    'supervisor' => $data['supervisor'] ?: null,
                    'external_examiner' => $data['externalExaminer'] ?: null,
                    'internal_examiner' => $data['internalExaminer'] ?: null,
                    'student_portal_username' => $portalUsername,
                    'student_portal_password' => $portalPassword,
                    'col_date' => $data['colDate'] ?: null,
                ]);

                Log::info('Student created from CSV', ['name' => $data['name'], 'ic' => $data['ic']]);
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
                    'category' => $data['category'] ?: $user->category,
                    'programme_name' => $data['programmeName'] ?: $user->programme_name,
                    'faculty' => $data['faculty'] ?: $user->faculty,
                    'programme_code' => $data['programmeCode'] ?: $user->programme_code,
                    'semester_entry' => $data['semesterEntry'] ?: $user->semester_entry,
                    'programme_intake' => $data['programmeIntake'] ?: $user->programme_intake,
                    'date_of_commencement' => $data['dateOfCommencement'] ?: $user->date_of_commencement,
                    'research_title' => $data['researchTitle'] ?: $user->research_title,
                    'supervisor' => $data['supervisor'] ?: $user->supervisor,
                    'external_examiner' => $data['externalExaminer'] ?: $user->external_examiner,
                    'internal_examiner' => $data['internalExaminer'] ?: $user->internal_examiner,
                    'student_portal_username' => $portalUsername ?: $user->student_portal_username,
                    'student_portal_password' => $portalPassword ?: $user->student_portal_password,
                    'col_date' => $data['colDate'] ?: $user->col_date,
                ]);

                Log::info('Student updated from CSV', ['name' => $data['name'], 'ic' => $data['ic']]);
                return ['success' => true, 'action' => 'updated'];
            }
        } catch (\Exception $e) {
            Log::error('Error processing student from CSV', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'action' => 'error'];
        }
    }
}
