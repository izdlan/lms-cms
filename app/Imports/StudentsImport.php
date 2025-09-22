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
        // assume first row is header
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
                'ic' => 'required|string',
                'username' => 'required|email',
                'phone' => 'nullable|string',
                'courses' => 'nullable|string',
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
            $user = User::where('ic', $data['ic'])->first();
            
            if (!$user) {
                $user = User::where('email', $data['email'])->first();
            }

            // Use IC as password for all students
            $icPassword = $data['ic'];

            // Parse courses if provided
            $courses = [];
            if (!empty($data['courses'])) {
                $courses = array_map('trim', explode(',', $data['courses']));
            }

            try {
                if (!$user) {
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'ic' => $data['ic'],
                        'phone' => $data['number'] ?? null,
                        'password' => Hash::make($icPassword),
                        'role' => 'student',
                        'must_reset_password' => false,
                        'courses' => $courses,
                    ]);
                    $created++;
                    Log::info('Student created', ['name' => $data['name'], 'ic' => $data['ic']]);
                } else {
                    // update fields if changed, keep IC as password
                    $user->update([
                        'name' => $data['name'],
                        'phone' => $data['number'] ?? $user->phone,
                        'password' => Hash::make($icPassword),
                        'courses' => $courses,
                        'must_reset_password' => false,
                    ]);
                    $updated++;
                    Log::info('Student updated', ['name' => $data['name'], 'ic' => $data['ic']]);
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