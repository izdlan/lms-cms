<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudentEnrollment;
use App\Models\ClassSchedule;
use App\Models\Subject;

class FixStudentEnrollmentClassCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Fixing student enrollment class codes to match lecturer class codes...');

        // Get all student enrollments
        $enrollments = StudentEnrollment::all();
        
        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($enrollments as $enrollment) {
            // Find the corresponding class schedule for this subject
            $classSchedule = ClassSchedule::where('subject_code', $enrollment->subject_code)
                ->where('lecturer_id', $enrollment->lecturer_id)
                ->first();

            if ($classSchedule) {
                // Update the enrollment with the correct class code
                $oldClassCode = $enrollment->class_code;
                $enrollment->update([
                    'class_code' => $classSchedule->class_code
                ]);
                
                $this->command->info("✓ Updated enrollment: {$enrollment->subject_code} - {$oldClassCode} → {$classSchedule->class_code}");
                $updatedCount++;
            } else {
                $this->command->warn("⚠ No class schedule found for subject: {$enrollment->subject_code}");
                $skippedCount++;
            }
        }

        $this->command->info("\n✅ Enrollment class codes fixed!");
        $this->command->info("Updated: {$updatedCount} enrollments");
        $this->command->info("Skipped: {$skippedCount} enrollments");
        
        // Verify the fix
        $this->command->info("\n=== VERIFICATION ===");
        $this->command->info("Total enrollments: " . StudentEnrollment::count());
        $this->command->info("Enrollments with matching class codes: " . StudentEnrollment::whereHas('classSchedule')->count());
    }
}
