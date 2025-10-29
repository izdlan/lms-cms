<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Subject;
use App\Models\ClassSchedule;
use App\Models\StudentEnrollment;

class EnrollAllStudentsInClassesCommand extends Command
{
    protected $signature = 'students:enroll-all-classes {--dry-run : Show what would be enrolled without creating records}';
    protected $description = 'Enroll all students in their respective program classes';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No records will be created');
        }

        $this->info('📚 Starting bulk student enrollment...');
        
        $students = User::where('role', 'student')->get();
        $totalEnrollments = 0;
        $errors = [];

        foreach ($students as $student) {
            $this->line("Processing student: {$student->name} ({$student->student_id})");
            
            // Get student's program code and map to subject program code
            $programCode = $student->programme_code;
            $programName = $student->programme_name;
            
            if (!$programCode || !$programName) {
                $errors[] = "Student {$student->name} has no program code or name";
                continue;
            }

            // Map program name to subject program code
            $subjectProgramCode = null;
            if (str_contains($programName, 'EMBA')) {
                $subjectProgramCode = 'EMBA';
            } elseif (str_contains($programName, 'EBBA')) {
                $subjectProgramCode = 'EBBA';
            } elseif (str_contains($programName, 'EDBA')) {
                $subjectProgramCode = 'EDBA';
            }

            if (!$subjectProgramCode) {
                $errors[] = "Cannot map program '{$programName}' to subject program code";
                continue;
            }

            // Get all subjects for this program
            $subjects = Subject::where('program_code', $subjectProgramCode)->get();
            
            if ($subjects->isEmpty()) {
                $errors[] = "No subjects found for program: {$programCode}";
                continue;
            }

            foreach ($subjects as $subject) {
                // Find class schedule for this subject
                $classSchedule = ClassSchedule::where('subject_code', $subject->code)
                    ->where('program_code', $subjectProgramCode)
                    ->first();

                if (!$classSchedule) {
                    $errors[] = "No class schedule found for subject: {$subject->code} in program: {$programCode}";
                    continue;
                }

                // Check if already enrolled
                $existingEnrollment = StudentEnrollment::where('user_id', $student->id)
                    ->where('subject_code', $subject->code)
                    ->first();

                if ($existingEnrollment) {
                    $this->warn("  ⚠️  Already enrolled in {$subject->code}");
                    continue;
                }

                if (!$isDryRun) {
                    // Create enrollment
                    StudentEnrollment::create([
                        'user_id' => $student->id,
                        'program_code' => $subjectProgramCode,
                        'subject_code' => $subject->code,
                        'lecturer_id' => $classSchedule->lecturer_id,
                        'class_code' => $classSchedule->class_code,
                        'status' => 'enrolled',
                        'enrollment_date' => now(),
                    ]);
                }

                $totalEnrollments++;
                $this->info("  ✅ Enrolled in {$subject->code} (Lecturer: {$classSchedule->lecturer->name})");
            }
        }

        $this->newLine();
        $this->info("📊 SUMMARY:");
        $this->info("Total students processed: {$students->count()}");
        $this->info("Total enrollments " . ($isDryRun ? "that would be created" : "created") . ": {$totalEnrollments}");
        
        if (!empty($errors)) {
            $this->error("Errors encountered:");
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }

        if ($isDryRun) {
            $this->info("💡 Run without --dry-run to actually create enrollments");
        } else {
            $this->info("🎉 Enrollment completed successfully!");
        }

        return 0;
    }
}
