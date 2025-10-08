<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Subject;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\Log;

class FixStudentEnrollments extends Command
{
    protected $signature = 'students:fix-enrollments';
    protected $description = 'Fix student enrollments by creating course enrollments from programme_name data';

    public function handle()
    {
        $this->info('Starting to fix student enrollments...');
        
        $students = User::where('role', 'student')
            ->whereNotNull('programme_name')
            ->where('programme_name', '!=', '')
            ->get();
            
        $this->info("Found {$students->count()} students with programme data");
        
        $created = 0;
        $updated = 0;
        $errors = 0;
        
        foreach ($students as $student) {
            try {
                if (empty($student->programme_name)) {
                    continue;
                }
                
                // Create or find the subject/course
                $subject = Subject::firstOrCreate(
                    [
                        'code' => $student->programme_code ?: strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $student->programme_name), 0, 8)),
                        'name' => $student->programme_name
                    ],
                    [
                        'description' => $student->programme_name,
                        'classification' => 'Core',
                        'credit_hours' => 3,
                        'program_code' => $student->programme_code ?: 'GEN',
                        'is_active' => true
                    ]
                );
                
                // Create enrollment if it doesn't exist
                $enrollment = StudentEnrollment::firstOrCreate(
                    [
                        'user_id' => $student->id,
                        'subject_code' => $subject->code
                    ],
                    [
                        'program_code' => $student->programme_code ?: 'GEN',
                        'status' => 'active',
                        'enrollment_date' => $student->created_at,
                        'grade' => null
                    ]
                );
                
                if ($enrollment->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
                
                $this->line("✓ {$student->name} - {$subject->name}");
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("✗ {$student->name} - Error: " . $e->getMessage());
                Log::error('Error fixing enrollment for student', [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info("\n=== Summary ===");
        $this->info("Created: {$created}");
        $this->info("Updated: {$updated}");
        $this->info("Errors: {$errors}");
        
        // Show final statistics
        $totalSubjects = Subject::count();
        $totalEnrollments = StudentEnrollment::count();
        $activeEnrollments = StudentEnrollment::where('status', 'active')->count();
        
        $this->info("\n=== Final Statistics ===");
        $this->info("Total Subjects: {$totalSubjects}");
        $this->info("Total Enrollments: {$totalEnrollments}");
        $this->info("Active Enrollments: {$activeEnrollments}");
        
        return 0;
    }
}
