<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudentEnrollment;
use App\Models\ClassSchedule;
use App\Models\Subject;

class UpdateStudentEnrollmentsToNewClassCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Updating student enrollments to use new class codes (A, B, C)...');

        // Get all subjects
        $subjects = Subject::where('program_code', 'EMBA')->get();
        
        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($subjects as $subject) {
            // Get all enrollments for this subject
            $enrollments = StudentEnrollment::where('subject_code', $subject->code)->get();
            
            // Get the new class schedules for this subject
            $classSchedules = ClassSchedule::where('subject_code', $subject->code)
                ->where('is_active', true)
                ->get();

            if ($classSchedules->isEmpty()) {
                $this->command->warn("No class schedules found for subject: {$subject->code}");
                continue;
            }

            // Distribute students across classes A, B, C
            $classIndex = 0;
            $totalClasses = $classSchedules->count();

            foreach ($enrollments as $enrollment) {
                // Get the appropriate class schedule (round-robin distribution)
                $classSchedule = $classSchedules[$classIndex % $totalClasses];
                
                // Update the enrollment with the new class code
                $oldClassCode = $enrollment->class_code;
                $enrollment->update([
                    'class_code' => $classSchedule->class_code,
                    'lecturer_id' => $classSchedule->lecturer_id
                ]);
                
                $this->command->info("✓ Updated: {$subject->code} - {$oldClassCode} → {$classSchedule->class_code}");
                $updatedCount++;
                
                $classIndex++;
            }
        }

        $this->command->info("\n✅ Student enrollments updated to new class codes!");
        $this->command->info("Updated: {$updatedCount} enrollments");
        $this->command->info("Skipped: {$skippedCount} enrollments");
        
        // Verify the fix
        $this->command->info("\n=== VERIFICATION ===");
        $this->command->info("Total enrollments: " . StudentEnrollment::count());
        $this->command->info("Enrollments with matching class codes: " . StudentEnrollment::whereHas('classSchedule')->count());
        
        // Show sample of updated enrollments
        $this->command->info("\n=== SAMPLE UPDATED ENROLLMENTS ===");
        $sampleEnrollments = StudentEnrollment::with('classSchedule', 'subject')
            ->whereNotNull('class_code')
            ->limit(10)
            ->get();
            
        foreach ($sampleEnrollments as $enrollment) {
            $classCode = $enrollment->classSchedule ? $enrollment->classSchedule->class_code : 'No class';
            $this->command->info("- {$enrollment->subject->code}: {$enrollment->class_code} ({$classCode})");
        }
    }
}
