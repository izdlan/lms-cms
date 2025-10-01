<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClassSchedule;
use App\Models\Subject;
use App\Models\Lecturer;

class CreateClassSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'classes:create-schedules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create class schedules for all EMBA subjects with John Smith as lecturer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== CREATING CLASS SCHEDULES ===");
        
        // Get John Smith lecturer
        $johnSmith = Lecturer::where('name', 'like', '%John Smith%')->first();
        if (!$johnSmith) {
            $this->error("John Smith lecturer not found!");
            return;
        }
        
        $this->info("Using lecturer: {$johnSmith->name} (ID: {$johnSmith->id})");
        
        // Get all EMBA subjects
        $subjects = Subject::where('program_code', 'EMBA')
            ->where('is_active', true)
            ->get();
            
        if ($subjects->isEmpty()) {
            $this->error("No EMBA subjects found!");
            return;
        }
        
        $this->info("Found {$subjects->count()} EMBA subjects");
        
        // Create class schedules for each subject
        $createdCount = 0;
        $bar = $this->output->createProgressBar($subjects->count());
        $bar->start();
        
        foreach ($subjects as $subject) {
            // Check if class schedule already exists
            $existingSchedule = ClassSchedule::where('subject_code', $subject->code)
                ->where('program_code', 'EMBA')
                ->first();
                
            if ($existingSchedule) {
                $this->line("\nClass schedule already exists for {$subject->code}");
                $bar->advance();
                continue;
            }
            
            // Create class schedule
            $classCode = $subject->code . '2025A';
            $className = $subject->name . ' - Class A';
            
            ClassSchedule::create([
                'class_code' => $classCode,
                'subject_code' => $subject->code,
                'lecturer_id' => $johnSmith->id,
                'program_code' => 'EMBA',
                'class_name' => $className,
                'description' => "Main class for {$subject->name} - EMBA Program",
                'venue' => 'Lecture Hall 1',
                'day_of_week' => 'Monday',
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'start_date' => now()->addDays(7)->toDateString(), // Start next week
                'end_date' => now()->addWeeks(4)->toDateString(), // 4 weeks duration
                'max_students' => 100,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $createdCount++;
            $this->line("\nCreated class: {$classCode} for {$subject->name}");
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Successfully created {$createdCount} class schedules");
        
        // Show summary
        $this->info("\n=== CLASS SCHEDULE SUMMARY ===");
        $schedules = ClassSchedule::where('program_code', 'EMBA')->get();
        
        foreach ($schedules as $schedule) {
            $this->line("{$schedule->class_code} - {$schedule->class_name}");
            $this->line("  Subject: {$schedule->subject_code}");
            $this->line("  Lecturer: {$schedule->lecturer->name}");
            $this->line("  Day: {$schedule->day_of_week} {$schedule->start_time} - {$schedule->end_time}");
            $this->line("  Venue: {$schedule->venue}");
            $this->line("  Duration: {$schedule->start_date} to {$schedule->end_date}");
            $this->line("  Max Students: {$schedule->max_students}");
            $this->line("");
        }
        
        // Update student enrollments with class codes
        $this->info("=== UPDATING STUDENT ENROLLMENTS ===");
        $this->updateStudentEnrollments();
    }
    
    private function updateStudentEnrollments()
    {
        $schedules = ClassSchedule::where('program_code', 'EMBA')->get();
        $updatedCount = 0;
        
        foreach ($schedules as $schedule) {
            $enrollments = \App\Models\StudentEnrollment::where('subject_code', $schedule->subject_code)
                ->where('program_code', 'EMBA')
                ->get();
                
            foreach ($enrollments as $enrollment) {
                $enrollment->class_code = $schedule->class_code;
                $enrollment->save();
                $updatedCount++;
            }
        }
        
        $this->info("Updated {$updatedCount} student enrollments with class codes");
    }
}