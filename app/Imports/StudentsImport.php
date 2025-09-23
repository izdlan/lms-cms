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

    public function collection(Collection $rows): void
    {
        Log::info('Starting import from sheet "' . $this->currentSheet . '" with ' . $rows->count() . ' total rows');
        
        // For LMS sheets, headers are on row 7 (index 6), data starts from row 10 (index 9)
        // Skip first 6 rows (rows 1-6), start from row 7 which contains headers
        $rows = $rows->slice(6);
        Log::info('After skipping first 6 rows, remaining: ' . $rows->count() . ' rows');
        
        // Get header row (now the first row after skipping)
        $firstRow = $rows->shift();
        $header = collect($firstRow)->map(fn($h) => trim(strtolower($h)))->toArray();
        
        // Skip the next 2 rows (program name and category rows)
        $rows = $rows->slice(2);
        Log::info('After skipping program name and category rows, remaining: ' . $rows->count() . ' rows');
        Log::info('First row after headers (should be data):', $rows->first() ? (is_array($rows->first()) ? $rows->first() : $rows->first()->toArray()) : []);
        
        Log::info('Detected headers:', $header);
        
        // Dynamic mapping based on actual header content
        $mappedHeader = [];
        foreach ($header as $index => $headerName) {
            $originalHeader = $headerName;
            $headerName = strtolower(trim($headerName));
            
            Log::info("Processing header {$index}: '{$originalHeader}' -> '{$headerName}'");
            
            // Map based on header content, not position
            if (strpos($headerName, 'name') !== false && strpos($headerName, 'learners') === false) {
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
                       Log::warning("Skipping row " . ($index + 1) . " - column count mismatch. Headers: " . count($header) . ", Row: " . count($rowArray));
                       $this->errors++;
                       continue;
                   }

            // Create a flexible mapping that handles different column counts
            $data = [];
            for ($i = 0; $i < count($rowArray); $i++) {
                $key = $mappedHeader[$i] ?? 'unknown_' . $i;
                if ($key !== 'skip') {
                    $data[$key] = $rowArray[$i];
                }
            }
            Log::info("Combined data for row " . ($index + 1) . ":", $data);

                   // Check if we have required fields
                   if (empty($data['name']) || empty($data['ic/passport']) || empty($data['email'])) {
                       Log::warning("Missing required fields for row " . ($index + 1), [
                           'name' => $data['name'] ?? 'MISSING',
                           'ic/passport' => $data['ic/passport'] ?? 'MISSING', 
                           'email' => $data['email'] ?? 'MISSING'
                       ]);
                       $this->errors++;
                       continue;
                   }

            $validator = Validator::make($data, [
                'name' => 'required|string',
                'ic/passport' => 'required|string',
                'email' => 'required|email',
                'contact no.' => 'nullable|string',
                'address' => 'nullable|string',
                'previous university' => 'nullable|string',
                'col ref. no.' => 'nullable|string',
                'student id' => 'nullable|string',
                'programme name' => 'nullable|string',
                'category' => 'nullable|string',
                // add other validations
            ]);

                   if ($validator->fails()) {
                       Log::warning('Student import validation failed for row ' . ($index + 1), [
                           'data' => $data,
                           'errors' => $validator->errors()->toArray(),
                           'available_keys' => array_keys($data)
                       ]);
                       $this->errors++;
                       continue;
                   }

            // find by IC first, then by email if IC not found
            $user = User::where('ic', $data['ic/passport'])->first();
            
            if (!$user) {
                $user = User::where('email', $data['email'])->first();
            }

            // Use "0000" as password for all students
            $studentPassword = "0000";

            // Parse courses if provided (using programme name as course)
            $courses = [];
            if (!empty($data['programme name'])) {
                $courses = array_map('trim', explode(',', $data['programme name']));
            }

            try {
                if (!$user) {
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'ic' => $data['ic/passport'],
                        'phone' => $data['contact no.'] ?? null,
                        'address' => $data['address'] ?? null,
                        'previous_university' => $data['previous university'] ?? null,
                        'col_ref_no' => $data['col ref. no.'] ?? null,
                        'student_id' => $data['student id'] ?? null,
                        'source_sheet' => $this->currentSheet,
                        'password' => Hash::make($studentPassword),
                        'role' => 'student',
                        'must_reset_password' => false,
                        'courses' => $courses,
                           ]);
                           $this->created++;
                           Log::info('Student created', ['name' => $data['name'], 'ic' => $data['ic/passport'], 'sheet' => $this->currentSheet]);
                } else {
                    // update fields if changed, keep password as "0000"
                    $user->update([
                        'name' => $data['name'],
                        'phone' => $data['contact no.'] ?? $user->phone,
                        'address' => $data['address'] ?? $user->address,
                        'previous_university' => $data['previous university'] ?? $user->previous_university,
                        'col_ref_no' => $data['col ref. no.'] ?? $user->col_ref_no,
                        'student_id' => $data['student id'] ?? $user->student_id,
                        'source_sheet' => $this->currentSheet,
                        'password' => Hash::make($studentPassword),
                        'courses' => $courses,
                        'must_reset_password' => false,
                       ]);
                       $this->updated++;
                       Log::info('Student updated', ['name' => $data['name'], 'ic' => $data['ic/passport'], 'sheet' => $this->currentSheet]);
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
    }
}