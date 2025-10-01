<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClassSchedule;
use App\Models\Subject;

class UpdateClassCodesToMatchStudentFormatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Updating class codes to match student format (EMBA71012025A)...');

        // Get all EMBA subjects
        $subjects = Subject::where('program_code', 'EMBA')->get();
        
        $updatedCount = 0;

        foreach ($subjects as $subject) {
            // Get all class schedules for this subject
            $classSchedules = ClassSchedule::where('subject_code', $subject->code)
                ->where('is_active', true)
                ->get();

            if ($classSchedules->isEmpty()) {
                $this->command->warn("No class schedules found for subject: {$subject->code}");
                continue;
            }

            // Update class codes to match student format
            $classLetters = ['A', 'B', 'C'];
            foreach ($classSchedules as $index => $classSchedule) {
                $classLetter = $classLetters[$index % 3]; // A, B, C
                $newClassCode = $subject->code . '2025' . $classLetter; // EMBA71012025A format
                
                $oldClassCode = $classSchedule->class_code;
                $classSchedule->update([
                    'class_code' => $newClassCode,
                    'class_name' => $subject->name . ' - Class ' . $classLetter
                ]);
                
                $this->command->info("✓ Updated: {$subject->code} - {$oldClassCode} → {$newClassCode}");
                $updatedCount++;
            }
        }

        $this->command->info("\n✅ Class codes updated to match student format!");
        $this->command->info("Updated: {$updatedCount} class schedules");
        
        // Verify the fix
        $this->command->info("\n=== VERIFICATION ===");
        $this->command->info("Total class schedules: " . ClassSchedule::count());
        
        // Show sample of updated class codes
        $this->command->info("\n=== SAMPLE UPDATED CLASS CODES ===");
        $sampleClasses = ClassSchedule::with('subject')
            ->where('is_active', true)
            ->limit(10)
            ->get();
            
        foreach ($sampleClasses as $class) {
            $this->command->info("- {$class->subject->code}: {$class->class_code} - {$class->class_name}");
        }
    }
}
