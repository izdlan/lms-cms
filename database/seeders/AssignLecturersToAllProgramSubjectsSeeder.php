<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\Program;
use App\Models\ProgramSubject;
use App\Models\Subject;
use App\Models\ClassSchedule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AssignLecturersToAllProgramSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Assigning lecturers to all subjects across all programs...');
        
        // Get all programs
        $programs = Program::all();
        
        if ($programs->isEmpty()) {
            $this->command->error('No programs found!');
            return;
        }
        
        $totalAssignments = 0;
        
        foreach ($programs as $program) {
            $this->command->info("\n=== Processing Program: {$program->code} ({$program->name}) ===");
            
            // Get all subjects for this program from program_subjects table
            $programSubjects = ProgramSubject::where('program_id', $program->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('subject_name')
                ->get();
            
            if ($programSubjects->isEmpty()) {
                $this->command->warn("No subjects found for program: {$program->code}");
                continue;
            }
            
            $this->command->info("Found {$programSubjects->count()} subjects for {$program->code}");
            
            // Create lecturers for each subject or reuse existing ones
            foreach ($programSubjects as $index => $programSubject) {
                // Try to find existing Subject by code
                $subject = null;
                if ($programSubject->subject_code) {
                    $subject = Subject::where('code', $programSubject->subject_code)->first();
                }
                
                // Generate lecturer email
                $lecturerEmail = strtolower(str_replace(' ', '.', $programSubject->subject_name)) . '@olympia.edu';
                $lecturerEmail = preg_replace('/[^a-z0-9.@]/', '', $lecturerEmail);
                $lecturerName = 'Dr. ' . $this->generateLecturerName($index, $program->code);
                
                // Check if lecturer already exists for this subject
                $existingLecturer = Lecturer::where('email', $lecturerEmail)->first();
                
                if (!$existingLecturer) {
                    // Check if user exists
                    $user = User::where('email', $lecturerEmail)->first();
                    
                    if (!$user) {
                        $user = User::create([
                            'name' => $lecturerName,
                            'email' => $lecturerEmail,
                            'password' => Hash::make('password123'),
                            'role' => 'lecturer',
                            'phone' => '+6012' . str_pad($program->id, 4, '0', STR_PAD_LEFT) . str_pad($index, 4, '0', STR_PAD_LEFT),
                            'must_reset_password' => true,
                            'is_active' => true
                        ]);
                    }
                    
                    // Create lecturer profile
                    $lecturer = Lecturer::create([
                        'user_id' => $user->id,
                        'staff_id' => $program->code . '-LEC' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                        'name' => $lecturerName,
                        'email' => $lecturerEmail,
                        'phone' => '+6012' . str_pad($program->id, 4, '0', STR_PAD_LEFT) . str_pad($index, 4, '0', STR_PAD_LEFT),
                        'department' => $this->getDepartmentForSubject($programSubject->subject_name),
                        'specialization' => $programSubject->subject_name,
                        'bio' => $this->generateBio($programSubject->subject_name, $lecturerName),
                        'is_active' => true
                    ]);
                    
                    $this->command->info("Created new lecturer: {$lecturerName}");
                } else {
                    $lecturer = $existingLecturer;
                    $this->command->info("Using existing lecturer: {$lecturerName}");
                }
                
                // Create ClassSchedule for this lecturer and subject
                // Use subject_code from program_subjects if available, otherwise create one
                $subjectCode = $programSubject->subject_code;
                
                if (!$subjectCode) {
                    // Generate a subject code if none exists
                    $subjectCode = $program->code . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                    
                    // Update program_subject with the generated code
                    $programSubject->update(['subject_code' => $subjectCode]);
                }
                
                // Check if class schedule already exists
                $existingSchedule = ClassSchedule::where('subject_code', $subjectCode)
                    ->where('lecturer_id', $lecturer->id)
                    ->first();
                
                if (!$existingSchedule) {
                    // Generate class code
                    $classCode = $subjectCode . '_CLASS_001';
                    
                    // Create class schedule
                    ClassSchedule::create([
                        'subject_code' => $subjectCode,
                        'lecturer_id' => $lecturer->id,
                        'class_code' => $classCode,
                        'class_name' => $programSubject->subject_name . ' - Class 1',
                        'program_code' => $program->code,
                        'description' => 'Main class for ' . $programSubject->subject_name,
                        'venue' => 'Room ' . (100 + $index % 20),
                        'day_of_week' => $this->getDayOfWeek($index),
                        'start_time' => Carbon::createFromTime(9, 0, 0),
                        'end_time' => Carbon::createFromTime(12, 0, 0),
                        'start_date' => Carbon::now()->addDays(7),
                        'end_date' => Carbon::now()->addMonths(4),
                        'max_students' => 30,
                        'is_active' => true
                    ]);
                    
                    $this->command->info("  ✓ Assigned: {$programSubject->subject_name}");
                    $totalAssignments++;
                } else {
                    $this->command->info("  ⊘ Already assigned: {$programSubject->subject_name}");
                }
            }
        }
        
        $this->command->info("\n=== Summary ===");
        $this->command->info("Total assignments made: {$totalAssignments}");
        $this->command->info("All lecturers can login with password: password123");
    }
    
    /**
     * Generate a lecturer name based on index
     */
    private function generateLecturerName(int $index, string $programCode): string
    {
        $firstNames = ['John', 'Sarah', 'Michael', 'Emily', 'David', 'Lisa', 'James', 'Maria', 'Alex', 'Jennifer', 'Robert', 'Amanda', 'Christopher', 'Patricia', 'Daniel', 'Nicole', 'Matthew', 'Jessica', 'Andrew', 'Michelle', 'Joshua', 'Kimberly', 'Ryan', 'Deborah'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Jones', 'Brown', 'Davis', 'Miller', 'Wilson', 'Moore', 'Taylor', 'Anderson', 'Thomas', 'Jackson', 'White', 'Harris', 'Martin', 'Thompson', 'Garcia', 'Martinez', 'Robinson', 'Clark', 'Rodriguez', 'Lewis', 'Lee'];
        
        $firstName = $firstNames[$index % count($firstNames)];
        $lastName = $lastNames[$index % count($lastNames)];
        
        return $firstName . ' ' . $lastName;
    }
    
    /**
     * Get department based on subject name
     */
    private function getDepartmentForSubject(string $subjectName): string
    {
        $subjectLower = strtolower($subjectName);
        
        if (strpos($subjectLower, 'accounting') !== false || strpos($subjectLower, 'finance') !== false) {
            return 'Finance & Accounting';
        } elseif (strpos($subjectLower, 'marketing') !== false || strpos($subjectLower, 'business') !== false && strpos($subjectLower, 'digital') !== false) {
            return 'Marketing';
        } elseif (strpos($subjectLower, 'management') !== false || strpos($subjectLower, 'organizational') !== false || strpos($subjectLower, 'human resource') !== false) {
            return 'Management';
        } elseif (strpos($subjectLower, 'research') !== false || strpos($subjectLower, 'methodology') !== false) {
            return 'Research & Development';
        } elseif (strpos($subjectLower, 'international') !== false || strpos($subjectLower, 'economics') !== false) {
            return 'International Business';
        } elseif (strpos($subjectLower, 'innovation') !== false || strpos($subjectLower, 'entrepreneurship') !== false) {
            return 'Entrepreneurship & Innovation';
        } elseif (strpos($subjectLower, 'analytics') !== false || strpos($subjectLower, 'statistics') !== false || strpos($subjectLower, 'data') !== false) {
            return 'Data Science & Analytics';
        } elseif (strpos($subjectLower, 'law') !== false || strpos($subjectLower, 'ethics') !== false || strpos($subjectLower, 'governance') !== false) {
            return 'Business Law';
        } elseif (strpos($subjectLower, 'strategic') !== false) {
            return 'Strategy & Leadership';
        } elseif (strpos($subjectLower, 'communication') !== false || strpos($subjectLower, 'professional skills') !== false) {
            return 'Professional Development';
        }
        
        return 'General Business Studies';
    }
    
    /**
     * Generate a bio for lecturer
     */
    private function generateBio(string $subjectName, string $lecturerName): string
    {
        return "{$lecturerName} is an experienced educator specializing in {$subjectName}. With years of expertise in this field, they bring practical insights and academic rigor to the classroom, helping students achieve their learning objectives.";
    }
    
    /**
     * Get day of week based on index
     */
    private function getDayOfWeek(int $index): string
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        return $days[$index % count($days)];
    }
}

