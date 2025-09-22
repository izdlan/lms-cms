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

        // Normalize header names
        $header = array_map(function($h) {
            return trim(strtolower($h));
        }, $header);

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

            // Validate required fields
            if (empty($data['name']) || empty($data['ic']) || empty($data['email'])) {
                Log::warning('Missing required fields', ['data' => $data]);
                $errors++;
                continue;
            }

            // Find existing user by IC or email
            $user = User::where('ic', $data['ic'])->first();
            if (!$user) {
                $user = User::where('email', $data['email'])->first();
            }

            // Parse courses
            $courses = [];
            if (!empty($data['courses'])) {
                $courses = array_map('trim', explode(',', $data['courses']));
            }

            try {
                if (!$user) {
                    // Create new user
                    User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'ic' => $data['ic'],
                        'phone' => $data['number'] ?? null,
                        'password' => Hash::make($data['ic']), // Use IC as password
                        'role' => 'student',
                        'must_reset_password' => false,
                        'courses' => $courses,
                    ]);
                    $created++;
                    Log::info('Student created', ['name' => $data['name'], 'ic' => $data['ic']]);
                } else {
                    // Update existing user
                    $user->update([
                        'name' => $data['name'],
                        'phone' => $data['number'] ?? $user->phone,
                        'password' => Hash::make($data['ic']), // Reset password to IC
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
}
