<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Lecturer;
use App\Models\ClassSchedule;

class MultipleClassSchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all EMBA subjects
        $subjects = Subject::where('program_code', 'EMBA')->get();
        
        if ($subjects->count() !== 12) {
            $this->command->error('Expected 12 EMBA subjects, found ' . $subjects->count());
            return;
        }

        $this->command->info('Creating multiple classes (A, B, C) for each subject...');

        // Class configurations
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

        foreach ($subjects as $subject) {
            // Get the lecturer assigned to this subject
            $lecturer = Lecturer::whereHas('classSchedules', function($query) use ($subject) {
                $query->where('subject_code', $subject->code);
            })->first();

            if (!$lecturer) {
                $this->command->warn("No lecturer found for subject: {$subject->name}");
                continue;
            }

            // Create classes A, B, C for this subject
            foreach ($classConfigs as $classLetter => $config) {
                $classCode = $subject->code . '_CLASS_' . $classLetter;
                
                // Check if class already exists
                $existingClass = ClassSchedule::where('class_code', $classCode)->first();
                
                if (!$existingClass) {
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

                    $this->command->info("✓ Created {$classCode} - {$subject->name} Class {$classLetter} ({$config['day_of_week']} {$config['start_time']})");
                } else {
                    $this->command->info("→ {$classCode} already exists");
                }
            }
        }

        $this->command->info("\n✅ Successfully created multiple classes for all subjects!");
        $this->command->info("Each subject now has 3 classes: A, B, and C");
    }
}
