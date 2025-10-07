<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExamResult;
use App\Models\User;
use App\Models\Subject;
use App\Models\Lecturer;
use App\Models\StudentEnrollment;
use Carbon\Carbon;

class ExamResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all students
        $students = User::where('role', 'student')->get();
        
        // Get all lecturers
        $lecturers = Lecturer::all();
        
        // EMBA subjects
        $subjects = [
            'EMBA7101', 'EMBA7102', 'EMBA7103', 'EMBA7104', 'EMBA7105', 'EMBA7106',
            'EMBA7107', 'EMBA7108', 'EMBA7109', 'EMBA7110', 'EMBA7111', 'EMBA7112'
        ];
        
        // Assessment types that lecturers can use
        $assessmentTypes = [
            'Quiz' => ['max_score' => 10, 'weight' => 0.1],
            'Assignment 1' => ['max_score' => 20, 'weight' => 0.15],
            'Assignment 2' => ['max_score' => 20, 'weight' => 0.15],
            'Midterm Exam' => ['max_score' => 30, 'weight' => 0.25],
            'Final Exam' => ['max_score' => 40, 'weight' => 0.35],
            'Project' => ['max_score' => 25, 'weight' => 0.2],
            'Presentation' => ['max_score' => 15, 'weight' => 0.1],
            'Participation' => ['max_score' => 10, 'weight' => 0.05],
            'Lab Work' => ['max_score' => 20, 'weight' => 0.15],
            'Case Study' => ['max_score' => 25, 'weight' => 0.2]
        ];
        
        $academicYear = '2025';
        $semester = 'Semester 1';
        
        foreach ($students as $student) {
            // Get student's enrolled subjects
            $enrolledSubjects = StudentEnrollment::where('user_id', $student->id)
                ->where('status', 'enrolled')
                ->with('subject')
                ->get();
            
            foreach ($enrolledSubjects as $enrollment) {
                if (in_array($enrollment->subject_code, $subjects)) {
                    // Randomly select 4-6 assessment types for this subject
                    $selectedAssessments = collect($assessmentTypes)
                        ->random(rand(4, 6))
                        ->toArray();
                    
                    // Generate assessment scores
                    $assessments = [];
                    $totalScore = 0;
                    $totalMaxScore = 0;
                    
                    foreach ($selectedAssessments as $name => $config) {
                        // Generate realistic scores (60-95% of max score)
                        $score = rand(
                            (int)($config['max_score'] * 0.6),
                            (int)($config['max_score'] * 0.95)
                        );
                        
                        $assessments[] = [
                            'name' => $name,
                            'max_score' => $config['max_score'],
                            'score' => $score,
                            'weight' => $config['weight'],
                            'percentage' => round(($score / $config['max_score']) * 100, 1)
                        ];
                        
                        $totalScore += $score;
                        $totalMaxScore += $config['max_score'];
                    }
                    
                    // Calculate percentage and grade
                    $percentage = round(($totalScore / $totalMaxScore) * 100, 1);
                    $grade = $this->calculateGrade($percentage);
                    $gpa = $this->calculateGpa($grade);
                    
                    // Get random lecturer
                    $lecturer = $lecturers->random();
                    
                    // Create exam result
                    ExamResult::create([
                        'user_id' => $student->id,
                        'subject_code' => $enrollment->subject_code,
                        'academic_year' => $academicYear,
                        'semester' => $semester,
                        'class_code' => $enrollment->class_code,
                        'lecturer_id' => $lecturer->id,
                        'student_name' => $student->name,
                        'student_ic' => $student->ic ?? 'N/A',
                        'student_id' => $student->student_id ?? 'N/A',
                        'assessments' => $assessments,
                        'total_marks' => $totalScore,
                        'percentage' => $percentage,
                        'grade' => $grade,
                        'gpa' => $gpa,
                        'status' => 'published',
                        'remarks' => $this->getRandomRemarks($grade),
                        'published_at' => Carbon::now()->subDays(rand(1, 30)),
                        'finalized_at' => Carbon::now()->subDays(rand(1, 15))
                    ]);
                }
            }
        }
        
        $this->command->info('Exam results seeded successfully!');
    }
    
    /**
     * Calculate grade based on percentage
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 85) return 'A';
        if ($percentage >= 80) return 'A-';
        if ($percentage >= 75) return 'B+';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 65) return 'B-';
        if ($percentage >= 60) return 'C+';
        if ($percentage >= 55) return 'C';
        if ($percentage >= 50) return 'C-';
        if ($percentage >= 45) return 'D+';
        if ($percentage >= 40) return 'D';
        return 'F';
    }
    
    /**
     * Calculate GPA based on grade
     */
    private function calculateGpa($grade)
    {
        $gradeGpaMap = [
            'A+' => 4.00,
            'A' => 4.00,
            'A-' => 3.67,
            'B+' => 3.33,
            'B' => 3.00,
            'B-' => 2.67,
            'C+' => 2.33,
            'C' => 2.00,
            'C-' => 1.67,
            'D+' => 1.33,
            'D' => 1.00,
            'F' => 0.00
        ];
        
        return $gradeGpaMap[$grade] ?? 0.00;
    }
    
    /**
     * Get random remarks based on grade
     */
    private function getRandomRemarks($grade)
    {
        $remarks = [
            'A+' => ['Excellent work! Outstanding performance.', 'Exceptional understanding of the subject matter.', 'Outstanding achievement!'],
            'A' => ['Very good work! Strong performance.', 'Good understanding of the concepts.', 'Well done!'],
            'A-' => ['Good work! Solid performance.', 'Good grasp of the material.', 'Keep up the good work!'],
            'B+' => ['Above average performance.', 'Good effort shown.', 'Satisfactory work.'],
            'B' => ['Satisfactory performance.', 'Adequate understanding.', 'Good effort.'],
            'B-' => ['Satisfactory work with room for improvement.', 'Basic understanding shown.', 'Keep working hard.'],
            'C+' => ['Passing grade with improvement needed.', 'Some understanding shown.', 'More effort required.'],
            'C' => ['Passing grade but needs improvement.', 'Basic understanding.', 'Consider seeking help.'],
            'C-' => ['Barely passing. Significant improvement needed.', 'Limited understanding.', 'Please seek academic support.'],
            'D+' => ['Below average. Major improvement needed.', 'Insufficient understanding.', 'Consider retaking the course.'],
            'D' => ['Poor performance. Retake recommended.', 'Very limited understanding.', 'Academic support strongly recommended.'],
            'F' => ['Failed. Retake required.', 'No understanding demonstrated.', 'Please meet with academic advisor.']
        ];
        
        $gradeRemarks = $remarks[$grade] ?? ['No remarks available.'];
        return $gradeRemarks[array_rand($gradeRemarks)];
    }
}