<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseContent;
use App\Models\Subject;
use App\Models\Lecturer;
use Carbon\Carbon;

class CourseContentSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = Subject::all();
        $lecturers = Lecturer::all();

        foreach ($subjects as $subject) {
            $lecturer = $lecturers->random();
            $classCode = $subject->code . '-001';

            // Create course content for each subject
            $courseContents = [
                [
                    'title' => 'Week 1 - Introduction to ' . $subject->name,
                    'description' => 'Introduction materials and overview of the course',
                    'file_name' => 'Week1_Introduction.pdf',
                    'file_path' => '/course-content/' . $subject->code . '/Week1_Introduction.pdf',
                    'file_type' => 'pdf',
                    'file_size' => 2048576, // 2MB
                    'uploaded_by_name' => $lecturer->name,
                    'uploaded_by_email' => $lecturer->email,
                    'created_at' => now()->subDays(7)
                ],
                [
                    'title' => 'Lecture Notes - ' . $subject->name,
                    'description' => 'Comprehensive lecture notes covering all topics',
                    'file_name' => 'Lecture_Notes.docx',
                    'file_path' => '/course-content/' . $subject->code . '/Lecture_Notes.docx',
                    'file_type' => 'docx',
                    'file_size' => 1536000, // 1.5MB
                    'uploaded_by_name' => $lecturer->name,
                    'uploaded_by_email' => $lecturer->email,
                    'created_at' => now()->subDays(6)
                ],
                [
                    'title' => 'Presentation Slides - Week 2',
                    'description' => 'PowerPoint presentation for Week 2 topics',
                    'file_name' => 'Week2_Presentation.pptx',
                    'file_path' => '/course-content/' . $subject->code . '/Week2_Presentation.pptx',
                    'file_type' => 'pptx',
                    'file_size' => 5120000, // 5MB
                    'uploaded_by_name' => $lecturer->name,
                    'uploaded_by_email' => $lecturer->email,
                    'created_at' => now()->subDays(5)
                ],
                [
                    'title' => 'Case Study Analysis',
                    'description' => 'Real-world case studies for practical application',
                    'file_name' => 'Case_Study_Analysis.pdf',
                    'file_path' => '/course-content/' . $subject->code . '/Case_Study_Analysis.pdf',
                    'file_type' => 'pdf',
                    'file_size' => 3072000, // 3MB
                    'uploaded_by_name' => $lecturer->name,
                    'uploaded_by_email' => $lecturer->email,
                    'created_at' => now()->subDays(4)
                ],
                [
                    'title' => 'Additional Reading Materials',
                    'description' => 'Supplementary reading materials and research papers',
                    'file_name' => 'Additional_Readings.zip',
                    'file_path' => '/course-content/' . $subject->code . '/Additional_Readings.zip',
                    'file_type' => 'zip',
                    'file_size' => 10240000, // 10MB
                    'uploaded_by_name' => $lecturer->name,
                    'uploaded_by_email' => $lecturer->email,
                    'created_at' => now()->subDays(3)
                ],
                [
                    'title' => 'Assignment Guidelines',
                    'description' => 'Detailed guidelines and rubrics for assignments',
                    'file_name' => 'Assignment_Guidelines.pdf',
                    'file_path' => '/course-content/' . $subject->code . '/Assignment_Guidelines.pdf',
                    'file_type' => 'pdf',
                    'file_size' => 1024000, // 1MB
                    'uploaded_by_name' => $lecturer->name,
                    'uploaded_by_email' => $lecturer->email,
                    'created_at' => now()->subDays(2)
                ],
                [
                    'title' => 'Video Lecture - Week 3',
                    'description' => 'Recorded video lecture for Week 3 topics',
                    'file_name' => 'Week3_Video_Lecture.mp4',
                    'file_path' => '/course-content/' . $subject->code . '/Week3_Video_Lecture.mp4',
                    'file_type' => 'mp4',
                    'file_size' => 52428800, // 50MB
                    'uploaded_by_name' => $lecturer->name,
                    'uploaded_by_email' => $lecturer->email,
                    'created_at' => now()->subDays(1)
                ],
                [
                    'title' => 'Practice Questions',
                    'description' => 'Practice questions and sample exams',
                    'file_name' => 'Practice_Questions.xlsx',
                    'file_path' => '/course-content/' . $subject->code . '/Practice_Questions.xlsx',
                    'file_type' => 'xlsx',
                    'file_size' => 512000, // 512KB
                    'uploaded_by_name' => $lecturer->name,
                    'uploaded_by_email' => $lecturer->email,
                    'created_at' => now()->subHours(6)
                ]
            ];

            foreach ($courseContents as $contentData) {
                CourseContent::create([
                    'subject_code' => $subject->code,
                    'class_code' => $classCode,
                    'title' => $contentData['title'],
                    'description' => $contentData['description'],
                    'file_name' => $contentData['file_name'],
                    'file_path' => $contentData['file_path'],
                    'file_type' => $contentData['file_type'],
                    'file_size' => $contentData['file_size'],
                    'uploaded_by_name' => $contentData['uploaded_by_name'],
                    'uploaded_by_email' => $contentData['uploaded_by_email'],
                    'is_active' => true,
                    'download_count' => rand(0, 50),
                    'created_at' => $contentData['created_at']
                ]);
            }
        }
    }
}