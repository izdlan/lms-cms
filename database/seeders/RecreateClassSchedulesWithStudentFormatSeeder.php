<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClassSchedule;
use App\Models\Subject;
use App\Models\Lecturer;
use App\Models\StudentEnrollment;

class RecreateClassSchedulesWithStudentFormatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Recreating class schedules with student format (EMBA71012025A)...');

        // Get all EMBA subjects
        $subjects = Subject::where('program_code', 'EMBA')->get();
        
        $deletedCount = 0;
        $createdCount = 0;

        foreach ($subjects as $subject) {
            // Delete existing class schedules for this subject
            $existingClasses = ClassSchedule::where('subject_code', $subject->code)->get();
            foreach ($existingClasses as $class) {
                $class->delete();
                $deletedCount++;
            }

            // Get the lecturer assigned to this subject
            $lecturer = Lecturer::whereHas('classSchedules', function($query) use ($subject) {
                $query->where('subject_code', $subject->code);
            })->first();

            if (!$lecturer) {
                $this->command->warn("No lecturer found for subject: {$subject->name}");
                continue;
            }

            // Create 3 new class schedules with student format
            $classConfigs = [
                'A' => [
                    'day_of_week' => 'Monday',
                    'start_time' => '09:00:00',
                    'end_time' => '12:00:00',
                    'venue' => 'Room 101',
                    'start_date' => '2025-01-15',
                    'end_date' => '2025-05-15'
                ],
                'B' => [
                    'day_of_week' => 'Wednesday',
                    'start_time' => '14:00:00',
                    'end_time' => '17:00:00',
                    'venue' => 'Room 102',
                    'start_date' => '2025-01-15',
                    'end_date' => '2025-05-15'
                ],
                'C' => [
                    'day_of_week' => 'Friday',
                    'start_time' => '09:00:00',
                    'end_time' => '12:00:00',
                    'venue' => 'Room 103',
                    'start_date' => '2025-01-15',
                    'end_date' => '2025-05-15'
                ]
            ];

            foreach ($classConfigs as $classLetter => $config) {
                $classCode = $subject->code . '2025' . $classLetter; // EMBA71012025A format
                
                ClassSchedule::create([
                    'subject_code' => $subject->code,
                    'lecturer_id' => $lecturer->id,
                    'class_code' => $classCode,
                    'class_name' => $subject->name . ' - Class ' . $classLetter,
                    'program_code' => 'EMBA',
                    'description' => 'Class ' . $classLetter . ' for ' . $subject->name,
                    'venue' => $config['venue'],
                    'day_of_week' => $config['day_of_week'],
                    'start_time' => $config['start_time'],
                    'end_time' => $config['end_time'],
                    'start_date' => $config['start_date'],
                    'end_date' => $config['end_date'],
                    'max_students' => 30,
                    'is_active' => true
                ]);

                $this->command->info("✓ Created: {$classCode} - {$subject->name} Class {$classLetter}");
                $createdCount++;
            }
        }

        $this->command->info("\n✅ Class schedules recreated with student format!");
        $this->command->info("Deleted: {$deletedCount} old class schedules");
        $this->command->info("Created: {$createdCount} new class schedules");
        
        // Now update student enrollments to use the correct class codes
        $this->command->info("\n=== UPDATING STUDENT ENROLLMENTS ===");
        $this->updateStudentEnrollments();
        
        // Verify the fix
        $this->command->info("\n=== VERIFICATION ===");
        $this->command->info("Total class schedules: " . ClassSchedule::count());
        $this->command->info("Total student enrollments: " . StudentEnrollment::count());
        $this->command->info("Enrollments with matching class codes: " . StudentEnrollment::whereHas('classSchedule')->count());
    }

    private function updateStudentEnrollments()
    {
        $enrollments = StudentEnrollment::all();
        $updatedCount = 0;

        foreach ($enrollments as $enrollment) {
            // Find the corresponding class schedule for this subject
            $classSchedule = ClassSchedule::where('subject_code', $enrollment->subject_code)
                ->where('lecturer_id', $enrollment->lecturer_id)
                ->first();

            if ($classSchedule) {
                $oldClassCode = $enrollment->class_code;
                $enrollment->update([
                    'class_code' => $classSchedule->class_code
                ]);
                
                if ($oldClassCode !== $classSchedule->class_code) {
                    $this->command->info("✓ Updated enrollment: {$enrollment->subject_code} - {$oldClassCode} → {$classSchedule->class_code}");
                    $updatedCount++;
                }
            }
        }

        $this->command->info("Updated: {$updatedCount} student enrollments");
    }
}
