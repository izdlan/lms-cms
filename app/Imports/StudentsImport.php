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

    public function setCurrentSheet($sheetName)
    {
        $this->currentSheet = $sheetName;
    }

    public function collection(Collection $rows): void
    {
        Log::info('Starting import from sheet "' . $this->currentSheet . '" with ' . $rows->count() . ' total rows');
        
        // Skip first 6 rows (rows 1-6), start from row 7 which contains headers
        $rows = $rows->slice(6);
        Log::info('After skipping first 6 rows, remaining: ' . $rows->count() . ' rows');
        
        // Get header row (now the first row after skipping)
        $firstRow = $rows->shift();
        $header = collect($firstRow)->map(fn($h) => trim(strtolower($h)))->toArray();
        
        // Skip the next 2 rows (program name and category rows)
        $rows = $rows->slice(2);
        Log::info('After skipping program name and category rows, remaining: ' . $rows->count() . ' rows');
        
        Log::info('Detected headers:', $header);
        
        // Direct mapping based on known Excel structure
        // Row 7: ["NO","NAME","ADDRESS","IC / PASSPORT","PREVIOUS UNIVERSITY","COL REF. NO. ","STUDENT ID","CONTACT NO.","EMAIL",...]
        // We'll skip the NO column (index 0) and map the rest
        $mappedHeader = [
            0 => 'skip',                  // NO (skip this column)
            1 => 'name',                  // NAME
            2 => 'address',               // ADDRESS
            3 => 'ic/passport',           // IC / PASSPORT
            4 => 'previous university',   // PREVIOUS UNIVERSITY
            5 => 'col ref. no.',          // COL REF. NO.
            6 => 'student id',            // STUDENT ID
            7 => 'contact no.',           // CONTACT NO.
            8 => 'email',                 // EMAIL
            9 => 'student portal',        // STUDENT PORTAL
            10 => 'semester entry',       // SEMESTER ENTRY
            11 => 'research title',       // RESEARCH TITLE
            12 => 'supervisor',           // SUPERVISOR
            13 => 'external examiner',    // EXTERNAL EXAMINER
            14 => 'internal examiner',    // INTERNAL EXAMINER
            15 => 'col date',             // COL DATE
            16 => 'programme intake',     // PROGRAMME INTAKE
            17 => 'date of commencement', // DATE OF COMMENCEMENT
            18 => 'total fees',           // TOTAL FEES
            19 => 'rm1 date',             // RM1 (DATE)
            20 => 'rm2 date',             // RM2 (DATE)
            21 => 'proposal defence',     // PROPOSAL DEFENCE
            22 => 'unknown_22',           // null
            23 => 'pre viva',             // PRE VIVA
            24 => 'viva',                 // VIVA
            25 => 'file status'           // FILE STATUS
        ];
        
        Log::info('Mapped headers:', $mappedHeader);
        
        $created = 0;
        $updated = 0;
        $errors = 0;

        foreach ($rows as $index => $row) {
            $rowArray = is_array($row) ? $row : $row->toArray();
            
            // Skip empty rows
            if (empty(array_filter($rowArray))) {
                Log::info("Skipping empty row " . ($index + 1));
                continue;
            }
            
            Log::info("Processing row " . ($index + 1) . " with " . count($rowArray) . " columns");
            Log::info("Row data:", $rowArray);

            // skip rows that don't match header column count
            if (count($header) !== count($rowArray)) {
                Log::warning("Skipping row " . ($index + 1) . " - column count mismatch. Headers: " . count($header) . ", Row: " . count($rowArray));
                $errors++;
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
                $errors++;
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
                $errors++;
                continue;
            }

            // find by IC first, then by email if IC not found
            $user = User::where('ic', $data['ic/passport'])->first();
            
            if (!$user) {
                $user = User::where('email', $data['email'])->first();
            }

            // Use IC as password for all students
            $icPassword = $data['ic/passport'];

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
                        'password' => Hash::make($icPassword),
                        'role' => 'student',
                        'must_reset_password' => false,
                        'courses' => $courses,
                    ]);
                    $created++;
                    Log::info('Student created', ['name' => $data['name'], 'ic' => $data['ic/passport'], 'sheet' => $this->currentSheet]);
                } else {
                    // update fields if changed, keep IC as password
                    $user->update([
                        'name' => $data['name'],
                        'phone' => $data['contact no.'] ?? $user->phone,
                        'address' => $data['address'] ?? $user->address,
                        'previous_university' => $data['previous university'] ?? $user->previous_university,
                        'col_ref_no' => $data['col ref. no.'] ?? $user->col_ref_no,
                        'student_id' => $data['student id'] ?? $user->student_id,
                        'source_sheet' => $this->currentSheet,
                        'password' => Hash::make($icPassword),
                        'courses' => $courses,
                        'must_reset_password' => false,
                    ]);
                    $updated++;
                    Log::info('Student updated', ['name' => $data['name'], 'ic' => $data['ic/passport'], 'sheet' => $this->currentSheet]);
                }
            } catch (\Exception $e) {
                Log::error('Error processing student', [
                    'data' => $data,
                    'error' => $e->getMessage()
                ]);
                $errors++;
                continue;
            }
        }
        
        // Log import summary
        Log::info('Student import completed', [
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
            'total_processed' => $created + $updated + $errors
        ]);
    }
}