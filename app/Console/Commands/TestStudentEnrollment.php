<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\StudentEnrollment;
use App\Models\Subject;

class TestStudentEnrollment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:student-enrollment {ic} {subject_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test student enrollment for a specific subject';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ic = $this->argument('ic');
        $subjectCode = $this->argument('subject_code');
        
        $this->info("Testing enrollment for student IC: {$ic}, Subject: {$subjectCode}");
        
        // Find student
        $student = User::where('ic', $ic)->first();
        if (!$student) {
            $this->error("Student with IC {$ic} not found!");
            return;
        }
        
        $this->info("Student found: {$student->name} (ID: {$student->id})");
        
        // Check enrollment
        $enrollment = StudentEnrollment::where('user_id', $student->id)
            ->where('subject_code', $subjectCode)
            ->where('status', 'enrolled')
            ->first();
            
        if (!$enrollment) {
            $this->error("No enrollment found for {$subjectCode}");
            
            // Check if there are any enrollments for this student
            $allEnrollments = StudentEnrollment::where('user_id', $student->id)->get();
            $this->info("Total enrollments for this student: {$allEnrollments->count()}");
            
            if ($allEnrollments->count() > 0) {
                $this->info("Student's enrollments:");
                foreach ($allEnrollments as $enroll) {
                    $this->line("  - {$enroll->subject_code} ({$enroll->status})");
                }
            }
            return;
        }
        
        $this->info("Enrollment found: {$enrollment->subject_code} - {$enrollment->status}");
        $this->info("Class: {$enrollment->class_code}");
        $this->info("Lecturer ID: {$enrollment->lecturer_id}");
        
        // Test subject with CLOs
        $subject = Subject::with('clos')->where('code', $subjectCode)->first();
        if ($subject) {
            $this->info("Subject found: {$subject->name}");
            $this->info("CLOs count: {$subject->clos->count()}");
            
            if ($subject->clos->count() > 0) {
                $this->info("CLOs:");
                foreach ($subject->clos as $clo) {
                    $this->line("  - {$clo->clo_code}: {$clo->description}");
                }
            }
        } else {
            $this->error("Subject {$subjectCode} not found!");
        }
    }
}