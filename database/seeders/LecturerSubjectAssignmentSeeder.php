<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\Subject;
use App\Models\ClassSchedule;
use Illuminate\Support\Facades\Hash;

class LecturerSubjectAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all subjects
        $subjects = Subject::where('program_code', 'EMBA')->get();
        
        if ($subjects->count() !== 12) {
            $this->command->error('Expected 12 EMBA subjects, found ' . $subjects->count());
            return;
        }

        // Lecturer data with specializations matching subjects
        $lecturers = [
            [
                'name' => 'Dr. Sarah Johnson',
                'email' => 'sarah.johnson@olympia.edu',
                'specialization' => 'Strategic Human Resource Management',
                'department' => 'Management',
                'bio' => 'Dr. Sarah Johnson is a distinguished professor with over 15 years of experience in Strategic Human Resource Management. She holds a PhD in Organizational Psychology and has published extensively in top-tier journals.',
                'subject_code' => 'EMBA7101'
            ],
            [
                'name' => 'Prof. Michael Chen',
                'email' => 'michael.chen@olympia.edu',
                'specialization' => 'Organisational Behaviour',
                'department' => 'Psychology & Management',
                'bio' => 'Prof. Michael Chen specializes in organizational behavior and workplace dynamics. With a background in industrial psychology, he brings practical insights to theoretical concepts.',
                'subject_code' => 'EMBA7102'
            ],
            [
                'name' => 'Dr. Emily Rodriguez',
                'email' => 'emily.rodriguez@olympia.edu',
                'specialization' => 'Strategic Management',
                'department' => 'Strategy & Leadership',
                'bio' => 'Dr. Emily Rodriguez is a strategic management expert with extensive consulting experience. She has helped numerous organizations develop and implement successful strategic initiatives.',
                'subject_code' => 'EMBA7103'
            ],
            [
                'name' => 'Prof. David Thompson',
                'email' => 'david.thompson@olympia.edu',
                'specialization' => 'Strategic Marketing',
                'department' => 'Marketing',
                'bio' => 'Prof. David Thompson is a marketing strategist with over 20 years of industry experience. He has worked with Fortune 500 companies and startups alike, bringing real-world marketing challenges to the classroom.',
                'subject_code' => 'EMBA7104'
            ],
            [
                'name' => 'Dr. Lisa Wang',
                'email' => 'lisa.wang@olympia.edu',
                'specialization' => 'Accounting & Finance',
                'department' => 'Finance & Accounting',
                'bio' => 'Dr. Lisa Wang is a certified public accountant and finance expert. She combines academic rigor with practical financial analysis skills, helping students understand complex financial concepts.',
                'subject_code' => 'EMBA7105'
            ],
            [
                'name' => 'Prof. James Anderson',
                'email' => 'james.anderson@olympia.edu',
                'specialization' => 'Business Analytics',
                'department' => 'Data Science & Analytics',
                'bio' => 'Prof. James Anderson is a data science expert who bridges the gap between technical analytics and business decision-making. He has led analytics teams at major corporations.',
                'subject_code' => 'EMBA7106'
            ],
            [
                'name' => 'Dr. Maria Garcia',
                'email' => 'maria.garcia@olympia.edu',
                'specialization' => 'Business Economics',
                'department' => 'Economics',
                'bio' => 'Dr. Maria Garcia is an economist with expertise in both micro and macroeconomics. She has advised government agencies and private sector organizations on economic policy and market analysis.',
                'subject_code' => 'EMBA7107'
            ],
            [
                'name' => 'Prof. Alex Kumar',
                'email' => 'alex.kumar@olympia.edu',
                'specialization' => 'Digital Business',
                'department' => 'Information Technology',
                'bio' => 'Prof. Alex Kumar is a digital transformation expert who has helped organizations navigate the digital landscape. He combines technical knowledge with business strategy.',
                'subject_code' => 'EMBA7108'
            ],
            [
                'name' => 'Dr. Jennifer Lee',
                'email' => 'jennifer.lee@olympia.edu',
                'specialization' => 'Innovation and Technology Entrepreneurship',
                'department' => 'Entrepreneurship & Innovation',
                'bio' => 'Dr. Jennifer Lee is a serial entrepreneur and innovation expert. She has founded multiple tech startups and now shares her experience with aspiring entrepreneurs.',
                'subject_code' => 'EMBA7109'
            ],
            [
                'name' => 'Prof. Robert Brown',
                'email' => 'robert.brown@olympia.edu',
                'specialization' => 'International Business Management',
                'department' => 'International Business',
                'bio' => 'Prof. Robert Brown has extensive international business experience across multiple continents. He brings global perspectives to local business challenges.',
                'subject_code' => 'EMBA7110'
            ],
            [
                'name' => 'Dr. Amanda Taylor',
                'email' => 'amanda.taylor@olympia.edu',
                'specialization' => 'Research Methodology',
                'department' => 'Research & Development',
                'bio' => 'Dr. Amanda Taylor is a research methodology expert who has guided hundreds of students through their research projects. She is known for making complex research methods accessible.',
                'subject_code' => 'EMBA7111'
            ],
            [
                'name' => 'Prof. Christopher Wilson',
                'email' => 'christopher.wilson@olympia.edu',
                'specialization' => 'Strategic Capstone Project',
                'department' => 'Strategic Studies',
                'bio' => 'Prof. Christopher Wilson is a senior faculty member who oversees capstone projects. He brings decades of industry experience to guide students through their final strategic projects.',
                'subject_code' => 'EMBA7112'
            ]
        ];

        $this->command->info('Creating 12 lecturers and assigning them to subjects...');

        foreach ($lecturers as $index => $lecturerData) {
            // Check if user already exists
            $user = User::where('email', $lecturerData['email'])->first();
            
            if (!$user) {
                // Create user account for lecturer
                $user = User::create([
                    'name' => $lecturerData['name'],
                    'email' => $lecturerData['email'],
                    'password' => Hash::make('lecturer123'), // Default password
                    'role' => 'lecturer',
                    'phone' => '+6012345678' . $index, // Unique phone numbers
                    'must_reset_password' => true,
                    'is_active' => true
                ]);
            }

            // Check if lecturer profile already exists
            $lecturer = Lecturer::where('email', $lecturerData['email'])->first();
            
            if (!$lecturer) {
                // Create lecturer profile
                $lecturer = Lecturer::create([
                    'user_id' => $user->id,
                    'staff_id' => 'LEC' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'name' => $lecturerData['name'],
                    'email' => $lecturerData['email'],
                    'phone' => '+6012345678' . $index,
                    'department' => $lecturerData['department'],
                    'specialization' => $lecturerData['specialization'],
                    'bio' => $lecturerData['bio'],
                    'is_active' => true
                ]);
            } else {
                // Update existing lecturer with new information
                $lecturer->update([
                    'department' => $lecturerData['department'],
                    'specialization' => $lecturerData['specialization'],
                    'bio' => $lecturerData['bio'],
                    'is_active' => true
                ]);
            }

            // Find the corresponding subject
            $subject = $subjects->where('code', $lecturerData['subject_code'])->first();
            
            if ($subject) {
                // Check if class schedule already exists
                $existingSchedule = ClassSchedule::where('subject_code', $subject->code)
                    ->where('lecturer_id', $lecturer->id)
                    ->first();
                
                if (!$existingSchedule) {
                    // Create class schedule for this lecturer and subject
                    ClassSchedule::create([
                        'subject_code' => $subject->code,
                        'lecturer_id' => $lecturer->id,
                        'class_code' => $subject->code . '_CLASS_001',
                        'class_name' => $subject->name . ' - Class 1',
                        'program_code' => 'EMBA',
                        'description' => 'Main class for ' . $subject->name,
                        'venue' => 'Room ' . (100 + $index),
                        'day_of_week' => ['Monday', 'Wednesday', 'Friday'][$index % 3],
                        'start_time' => '09:00:00',
                        'end_time' => '12:00:00',
                        'start_date' => '2025-01-15',
                        'end_date' => '2025-05-15',
                        'max_students' => 30,
                        'is_active' => true
                    ]);
                }

                $this->command->info("✓ Created/Updated lecturer: {$lecturerData['name']} - {$subject->name}");
            } else {
                $this->command->error("✗ Subject not found: {$lecturerData['subject_code']}");
            }
        }

        $this->command->info("\n✅ Successfully created 12 lecturers and assigned them to subjects!");
        $this->command->info("Default password for all lecturers: lecturer123");
        $this->command->info("All lecturers are required to change their password on first login.");
    }
}
