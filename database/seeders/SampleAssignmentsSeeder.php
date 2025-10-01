<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Assignment;
use App\Models\Subject;
use App\Models\ClassSchedule;
use App\Models\Lecturer;

class SampleAssignmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating sample assignments...');

        // Get all EMBA subjects and their classes
        $subjects = Subject::where('program_code', 'EMBA')->get();
        
        $assignmentTemplates = [
            [
                'title' => 'Research Proposal Assignment',
                'description' => 'Develop a comprehensive research proposal for your chosen topic. Include problem statement, objectives, methodology, and expected outcomes.',
                'type' => 'individual',
                'total_marks' => 100.00,
                'passing_marks' => 50.00,
                'instructions' => 'Submit your research proposal in PDF format. Include proper citations and references. Maximum 3000 words.',
                'allow_late_submission' => true,
                'late_penalty_percentage' => 10
            ],
            [
                'title' => 'Literature Review Report',
                'description' => 'Conduct a thorough literature review on your research topic. Analyze and synthesize findings from at least 15 academic sources.',
                'type' => 'individual',
                'total_marks' => 80.00,
                'passing_marks' => 40.00,
                'instructions' => 'Format your literature review according to academic standards. Include proper APA citations. Submit as PDF.',
                'allow_late_submission' => false,
                'late_penalty_percentage' => 0
            ],
            [
                'title' => 'Data Analysis Project',
                'description' => 'Analyze provided dataset using appropriate statistical methods. Present findings with charts and interpretations.',
                'type' => 'individual',
                'total_marks' => 90.00,
                'passing_marks' => 45.00,
                'instructions' => 'Use statistical software (SPSS, R, or Excel) for analysis. Include raw data and analysis files. Submit report as PDF.',
                'allow_late_submission' => true,
                'late_penalty_percentage' => 15
            ],
            [
                'title' => 'Case Study Analysis',
                'description' => 'Analyze a real-world business case study. Apply theoretical concepts learned in class to practical scenarios.',
                'type' => 'group',
                'total_marks' => 85.00,
                'passing_marks' => 42.50,
                'instructions' => 'Work in groups of 3-4 students. Present analysis with recommendations. Submit group report as PDF.',
                'allow_late_submission' => true,
                'late_penalty_percentage' => 5
            ],
            [
                'title' => 'Final Research Paper',
                'description' => 'Complete your research project and submit final paper with all components: introduction, methodology, results, discussion, and conclusion.',
                'type' => 'individual',
                'total_marks' => 150.00,
                'passing_marks' => 75.00,
                'instructions' => 'Follow academic writing standards. Include abstract, keywords, and references. Minimum 5000 words. Submit as PDF.',
                'allow_late_submission' => false,
                'late_penalty_percentage' => 0
            ]
        ];

        $createdCount = 0;

        foreach ($subjects as $index => $subject) {
            // Get classes for this subject
            $classes = ClassSchedule::where('subject_code', $subject->code)->get();
            
            foreach ($classes as $classIndex => $class) {
                // Get lecturer for this class
                $lecturer = Lecturer::find($class->lecturer_id);
                
                if (!$lecturer) {
                    continue;
                }

                // Create 2-3 assignments per class
                $assignmentsToCreate = array_slice($assignmentTemplates, 0, 3);
                
                foreach ($assignmentsToCreate as $assignmentIndex => $template) {
                    // Set different due dates
                    $availableFrom = now()->addDays($assignmentIndex * 7);
                    $dueDate = $availableFrom->copy()->addDays(14);
                    
                    $assignment = Assignment::create([
                        'title' => $template['title'] . ' - ' . $subject->code,
                        'description' => $template['description'],
                        'subject_code' => $subject->code,
                        'class_code' => $class->class_code,
                        'lecturer_id' => $lecturer->id,
                        'total_marks' => $template['total_marks'],
                        'passing_marks' => $template['passing_marks'],
                        'due_date' => $dueDate,
                        'available_from' => $availableFrom,
                        'type' => $template['type'],
                        'status' => 'published', // Publish immediately for testing
                        'instructions' => $template['instructions'],
                        'allow_late_submission' => $template['allow_late_submission'],
                        'late_penalty_percentage' => $template['late_penalty_percentage'],
                        'is_active' => true
                    ]);

                    $createdCount++;
                    $this->command->info("✓ Created assignment: {$assignment->title} for {$class->class_code}");
                }
            }
        }

        $this->command->info("\n✅ Successfully created {$createdCount} sample assignments!");
        $this->command->info("All assignments are published and available for students to view and submit.");
    }
}
