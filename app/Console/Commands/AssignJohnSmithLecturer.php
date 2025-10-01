<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\StudentEnrollment;
use App\Models\Subject;

class AssignJohnSmithLecturer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lecturer:assign-john-smith';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign John Smith as lecturer for all subjects and classes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== ASSIGNING JOHN SMITH AS LECTURER ===");
        
        // Find John Smith in users table
        $johnUser = User::where('name', 'like', '%John Smith%')->first();
        if (!$johnUser) {
            $this->error("John Smith not found in users table!");
            return;
        }
        
        $this->info("Found John Smith user: {$johnUser->name} (ID: {$johnUser->id}, Role: {$johnUser->role})");
        
        // Find or create John Smith in lecturers table
        $johnLecturer = Lecturer::where('name', 'like', '%John Smith%')->first();
        if (!$johnLecturer) {
            // Create lecturer profile for John Smith
            $johnLecturer = Lecturer::create([
                'user_id' => $johnUser->id,
                'staff_id' => 'LEC001',
                'name' => $johnUser->name,
                'email' => $johnUser->email,
                'phone' => '0123456789',
                'department' => 'Business Administration',
                'position' => 'Senior Lecturer',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->info("Created lecturer profile for John Smith (ID: {$johnLecturer->id})");
        } else {
            $this->info("Found existing lecturer profile: {$johnLecturer->name} (ID: {$johnLecturer->id})");
        }
        
        // Update all student enrollments to use John Smith as lecturer
        $enrollments = StudentEnrollment::all();
        $this->info("Found {$enrollments->count()} total enrollments");
        
        $updatedCount = 0;
        $bar = $this->output->createProgressBar($enrollments->count());
        $bar->start();
        
        foreach ($enrollments as $enrollment) {
            $enrollment->lecturer_id = $johnLecturer->id;
            $enrollment->save();
            $updatedCount++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Successfully updated {$updatedCount} enrollments with John Smith as lecturer");
        
        // Show summary by subject
        $this->info("\n=== ENROLLMENT SUMMARY BY SUBJECT ===");
        $subjects = Subject::where('program_code', 'EMBA')->get();
        
        foreach ($subjects as $subject) {
            $enrollmentCount = StudentEnrollment::where('subject_code', $subject->code)
                ->where('lecturer_id', $johnLecturer->id)
                ->count();
            $this->line("{$subject->code} - {$subject->name}: {$enrollmentCount} students");
        }
        
        // Show John Smith's total teaching load
        $totalStudents = StudentEnrollment::where('lecturer_id', $johnLecturer->id)->count();
        $uniqueSubjects = StudentEnrollment::where('lecturer_id', $johnLecturer->id)
            ->distinct('subject_code')
            ->count('subject_code');
        
        $this->info("\n=== JOHN SMITH'S TEACHING LOAD ===");
        $this->info("Total students: {$totalStudents}");
        $this->info("Subjects teaching: {$uniqueSubjects}");
        $this->info("Average students per subject: " . round($totalStudents / $uniqueSubjects, 1));
    }
}