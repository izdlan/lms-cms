<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CourseMaterial;

class CourseMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            // Strategic Human Resource Management Materials
            [
                'subject_code' => 'EMBA7101',
                'class_code' => 'EMBA7101-001',
                'title' => 'Introduction to Strategic HRM - Lecture Notes',
                'description' => 'Comprehensive lecture notes covering the fundamentals of strategic human resource management, including key concepts, theories, and practical applications.',
                'material_type' => 'document',
                'file_path' => 'materials/EMBA7101/lecture-notes-1.pdf',
                'file_name' => 'Strategic_HRM_Lecture_1.pdf',
                'file_size' => '2048576', // 2MB
                'file_extension' => 'pdf',
                'author_name' => 'Dr. Sarah Johnson',
                'author_email' => 'sarah.johnson@olympia.edu',
                'is_active' => true,
                'is_public' => true,
                'published_at' => now(),
            ],
            [
                'subject_code' => 'EMBA7101',
                'class_code' => 'EMBA7101-001',
                'title' => 'HR Strategy Case Study - Google',
                'description' => 'Detailed case study analysis of Google\'s innovative HR strategies and their impact on organizational performance.',
                'material_type' => 'document',
                'file_path' => 'materials/EMBA7101/case-study-google.pdf',
                'file_name' => 'Google_HR_Case_Study.pdf',
                'file_size' => '1536000', // 1.5MB
                'file_extension' => 'pdf',
                'author_name' => 'Dr. Sarah Johnson',
                'author_email' => 'sarah.johnson@olympia.edu',
                'is_active' => true,
                'is_public' => true,
                'published_at' => now(),
            ],
            [
                'subject_code' => 'EMBA7101',
                'class_code' => 'EMBA7101-001',
                'title' => 'Strategic HRM Video Lecture',
                'description' => 'Video lecture covering strategic human resource management concepts and real-world applications.',
                'material_type' => 'video',
                'external_url' => 'https://www.youtube.com/watch?v=example-hrm-video',
                'author_name' => 'Dr. Sarah Johnson',
                'author_email' => 'sarah.johnson@olympia.edu',
                'is_active' => true,
                'is_public' => true,
                'published_at' => now(),
            ],
            [
                'subject_code' => 'EMBA7101',
                'class_code' => 'EMBA7101-001',
                'title' => 'HR Analytics Dashboard Template',
                'description' => 'Excel template for creating HR analytics dashboards and tracking key performance indicators.',
                'material_type' => 'document',
                'file_path' => 'materials/EMBA7101/hr-analytics-template.xlsx',
                'file_name' => 'HR_Analytics_Dashboard_Template.xlsx',
                'file_size' => '512000', // 500KB
                'file_extension' => 'xlsx',
                'author_name' => 'Dr. Sarah Johnson',
                'author_email' => 'sarah.johnson@olympia.edu',
                'is_active' => true,
                'is_public' => true,
                'published_at' => now(),
            ],

            // Organisational Behaviour Materials
            [
                'subject_code' => 'EMBA7102',
                'class_code' => 'EMBA7102-001',
                'title' => 'Organizational Culture and Behavior - Slides',
                'description' => 'PowerPoint presentation covering organizational culture, behavior patterns, and their impact on performance.',
                'material_type' => 'document',
                'file_path' => 'materials/EMBA7102/org-culture-slides.pptx',
                'file_name' => 'Organizational_Culture_Slides.pptx',
                'file_size' => '3072000', // 3MB
                'file_extension' => 'pptx',
                'author_name' => 'Dr. Sarah Johnson',
                'author_email' => 'sarah.johnson@olympia.edu',
                'is_active' => true,
                'is_public' => true,
                'published_at' => now(),
            ],
            [
                'subject_code' => 'EMBA7102',
                'class_code' => 'EMBA7102-001',
                'title' => 'Team Dynamics Assessment Tool',
                'description' => 'Interactive assessment tool for evaluating team dynamics and identifying areas for improvement.',
                'material_type' => 'link',
                'external_url' => 'https://www.teamdynamics-assessment.com/assessment',
                'author_name' => 'Dr. Sarah Johnson',
                'author_email' => 'sarah.johnson@olympia.edu',
                'is_active' => true,
                'is_public' => true,
                'published_at' => now(),
            ],
            [
                'subject_code' => 'EMBA7102',
                'class_code' => 'EMBA7102-001',
                'title' => 'Leadership Styles Infographic',
                'description' => 'Visual infographic showing different leadership styles and their characteristics.',
                'material_type' => 'image',
                'file_path' => 'materials/EMBA7102/leadership-styles-infographic.png',
                'file_name' => 'Leadership_Styles_Infographic.png',
                'file_size' => '1024000', // 1MB
                'file_extension' => 'png',
                'author_name' => 'Dr. Sarah Johnson',
                'author_email' => 'sarah.johnson@olympia.edu',
                'is_active' => true,
                'is_public' => true,
                'published_at' => now(),
            ],

            // Strategic Management Materials
            [
                'subject_code' => 'EMBA7103',
                'class_code' => 'EMBA7103-001',
                'title' => 'Strategic Planning Framework',
                'description' => 'Comprehensive framework for strategic planning and implementation in modern organizations.',
                'material_type' => 'document',
                'file_path' => 'materials/EMBA7103/strategic-planning-framework.pdf',
                'file_name' => 'Strategic_Planning_Framework.pdf',
                'file_size' => '2560000', // 2.5MB
                'file_extension' => 'pdf',
                'author_name' => 'Dr. Michael Chen',
                'author_email' => 'michael.chen@olympia.edu',
                'is_active' => true,
                'is_public' => true,
                'published_at' => now(),
            ],
            [
                'subject_code' => 'EMBA7103',
                'class_code' => 'EMBA7103-001',
                'title' => 'SWOT Analysis Template',
                'description' => 'Professional SWOT analysis template for strategic planning exercises.',
                'material_type' => 'document',
                'file_path' => 'materials/EMBA7103/swot-analysis-template.docx',
                'file_name' => 'SWOT_Analysis_Template.docx',
                'file_size' => '256000', // 250KB
                'file_extension' => 'docx',
                'author_name' => 'Dr. Michael Chen',
                'author_email' => 'michael.chen@olympia.edu',
                'is_active' => true,
                'is_public' => true,
                'published_at' => now(),
            ],
        ];

        foreach ($materials as $material) {
            CourseMaterial::create($material);
        }
    }
}
