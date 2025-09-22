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
    public function collection(Collection $rows): void
    {
        // Skip first 4 rows (rows 1-4), start from row 5 which contains headers
        $rows = $rows->slice(4);
        
        // Get header row (now the first row after skipping)
        $firstRow = $rows->shift();
        $header = collect($firstRow)->map(fn($h) => trim(strtolower($h)))->toArray();
        
        $created = 0;
        $updated = 0;
        $errors = 0;

        foreach ($rows as $row) {
            $rowArray = is_array($row) ? $row : $row->toArray();

            // skip rows that don't match header column count
            if (count($header) !== count($rowArray)) {
                // optionally log the mismatch
                continue;
            }

            $data = array_combine($header, $rowArray);

            $validator = Validator::make($data, [
                'name' => 'required|string',
                'ic/passport' => 'required|string',
                'email' => 'required|email',
                'contact no.' => 'nullable|string',
                'address' => 'nullable|string',
                'category' => 'nullable|string',
                'programme name' => 'nullable|string',
                'programme code' => 'nullable|string',
                // add other validations
            ]);

            if ($validator->fails()) {
                Log::warning('Student import validation failed', [
                    'data' => $data,
                    'errors' => $validator->errors()->toArray()
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
                        'previous_university' => $data['programme name'] ?? null,
                        'col_ref_no' => $data['programme code'] ?? null,
                        'student_id' => $data['category'] ?? null, // Using category as student_id for now
                        'password' => Hash::make($icPassword),
                        'role' => 'student',
                        'must_reset_password' => false,
                        'courses' => $courses,
                    ]);
                    $created++;
                    Log::info('Student created', ['name' => $data['name'], 'ic' => $data['ic/passport']]);
                } else {
                    // update fields if changed, keep IC as password
                    $user->update([
                        'name' => $data['name'],
                        'phone' => $data['contact no.'] ?? $user->phone,
                        'address' => $data['address'] ?? $user->address,
                        'previous_university' => $data['programme name'] ?? $user->previous_university,
                        'col_ref_no' => $data['programme code'] ?? $user->col_ref_no,
                        'student_id' => $data['category'] ?? $user->student_id,
                        'password' => Hash::make($icPassword),
                        'courses' => $courses,
                        'must_reset_password' => false,
                    ]);
                    $updated++;
                    Log::info('Student updated', ['name' => $data['name'], 'ic' => $data['ic/passport']]);
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