<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Subject;
use App\Models\StudentEnrollment;

class AutoEnrollEMBAStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emba:auto-enroll {--student-id= : Enroll specific student by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-enroll all EMBA students in all EMBA subjects';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $studentId = $this->option('student-id');
        
        // Get EMBA students
        $query = User::where('role', 'student');
        if ($studentId) {
            $query->where('id', $studentId);
        }
        $students = $query->get();
        
        if ($students->isEmpty()) {
            $this->warn('No students found.');
            return;
        }
        
        // Get all EMBA subjects
        $subjects = Subject::where('program_code', 'EMBA')
            ->where('is_active', true)
            ->get();
            
        if ($subjects->isEmpty()) {
            $this->warn('No EMBA subjects found.');
            return;
        }
        
        $this->info("Found {$students->count()} students and {$subjects->count()} EMBA subjects");
        
        $totalEnrollments = 0;
        $bar = $this->output->createProgressBar($students->count());
        $bar->start();
        
        foreach ($students as $student) {
            $enrolledCount = 0;
            
            foreach ($subjects as $subject) {
                // Check if already enrolled
                $existingEnrollment = StudentEnrollment::where('user_id', $student->id)
                    ->where('subject_code', $subject->code)
                    ->first();
                    
                if (!$existingEnrollment) {
                    // Create enrollment
                    StudentEnrollment::create([
                        'user_id' => $student->id,
                        'program_code' => 'EMBA',
                        'subject_code' => $subject->code,
                        'class_code' => 'EMBA2025A', // Default class code
                        'lecturer_id' => 1, // Default lecturer ID
                        'status' => 'enrolled',
                        'enrollment_date' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $enrolledCount++;
                }
            }
            
            $totalEnrollments += $enrolledCount;
            $this->line("\nStudent {$student->name} (ID: {$student->id}): {$enrolledCount} new enrollments");
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Successfully created {$totalEnrollments} new enrollments");
        
        // Show summary
        $this->info("\nEnrollment Summary:");
        foreach ($students as $student) {
            $enrollmentCount = StudentEnrollment::where('user_id', $student->id)
                ->where('status', 'enrolled')
                ->count();
            $this->line("- {$student->name}: {$enrollmentCount} subjects enrolled");
        }
    }
}