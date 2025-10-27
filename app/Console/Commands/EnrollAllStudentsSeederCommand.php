<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\StudentEnrollment;
use App\Models\ClassSchedule;
use App\Models\Program;
use App\Models\ProgramSubject;
use Carbon\Carbon;

class EnrollAllStudentsSeederCommand extends Command
{
    protected $signature = 'students:enroll-all {--dry-run : Show what would be enrolled without making changes}';
    protected $description = 'Enroll all students in subjects for their assigned programs';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }
        
        $this->info('📚 Enrolling students in their program subjects...');
        
        // Get all students
        $students = User::where('role', 'student')->get();
        
        if ($students->count() === 0) {
            $this->error('No students found!');
            return;
        }
        
        $this->info("Found {$students->count()} students\n");
        
        $totalEnrollments = 0;
        $enrolledStudents = 0;
        
        // Get all class schedules grouped by program
        $classSchedules = ClassSchedule::all()->groupBy('program_code');
        
        foreach ($students as $student) {
            // Try to determine program from various sources
            $programCode = $this->getStudentProgram($student);
            
            if (!$programCode) {
                $this->warn("⚠️  Student {$student->name} has no program assigned. Skipping...");
                continue;
            }
            
            $this->info("Student: {$student->name} → Program: {$programCode}");
            
            // Get class schedules for this program
            $schedules = $classSchedules->get($programCode, collect());
            
            if ($schedules->isEmpty()) {
                $this->warn("  No class schedules found for program {$programCode}");
                continue;
            }
            
            $enrolledCount = 0;
            
            foreach ($schedules as $schedule) {
                // Check if already enrolled
                $existing = StudentEnrollment::where('user_id', $student->id)
                    ->where('subject_code', $schedule->subject_code)
                    ->first();
                
                if ($existing) {
                    $this->line("  ⊘ Already enrolled: {$schedule->subject_code}");
                    continue;
                }
                
                if (!$dryRun) {
                    // Create enrollment
                    StudentEnrollment::create([
                        'user_id' => $student->id,
                        'program_code' => $programCode,
                        'subject_code' => $schedule->subject_code,
                        'lecturer_id' => $schedule->lecturer_id,
                        'class_code' => $schedule->class_code,
                        'status' => 'enrolled',
                        'enrollment_date' => Carbon::now()->subDays(rand(1, 30)),
                    ]);
                }
                
                $this->line("  ✓ Enrolled in: {$schedule->subject_code}");
                $enrolledCount++;
                $totalEnrollments++;
            }
            
            if ($enrolledCount > 0) {
                $enrolledStudents++;
            }
        }
        
        $this->newLine();
        $this->info("=== Summary ===");
        $this->info("Total enrollments: {$totalEnrollments}");
        $this->info("Students enrolled: {$enrolledStudents}");
        
        if ($dryRun) {
            $this->newLine();
            $this->warn('This was a DRY RUN. No changes were made.');
            $this->info('Run without --dry-run to apply changes.');
        }
    }
    
    /**
     * Get student's program from various sources
     */
    private function getStudentProgram($student)
    {
        // Try program_code field first
        if (!empty($student->program_code)) {
            return strtoupper($student->program_code);
        }
        
        // Try programme_code
        if (!empty($student->programme_code)) {
            return strtoupper($student->programme_code);
        }
        
        // Try to infer from programme_name
        if (!empty($student->programme_name)) {
            $programName = strtoupper($student->programme_name);
            
            // Check for program codes in name
            if (stripos($programName, 'EMBA') !== false) {
                return 'EMBA';
            }
            if (stripos($programName, 'EBBA') !== false || stripos($programName, 'BACHELOR') !== false) {
                return 'EBBA';
            }
            if (stripos($programName, 'EDBA') !== false || stripos($programName, 'DOCTOR') !== false) {
                return 'EDBA';
            }
        }
        
        // Default to EMBA if we can't determine
        return 'EMBA';
    }
}

