<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'code' => 'EMBA7101',
                'name' => 'Strategic Human Resource Management',
                'description' => 'This course provides an advanced understanding of Strategic Human Resource Management (SHRM) and its role in driving organizational performance.',
                'classification' => 'Common Core',
                'credit_hours' => 3,
                'program_code' => 'EMBA',
                'is_active' => true
            ],
            [
                'code' => 'EMBA7102',
                'name' => 'Organisational Behaviour',
                'description' => 'This course explores the psychological, social, and structural dimensions of behaviour within organizations.',
                'classification' => 'Common Core',
                'credit_hours' => 3,
                'program_code' => 'EMBA',
                'is_active' => true
            ],
            [
                'code' => 'EMBA7103',
                'name' => 'Strategic Management',
                'description' => 'This course focuses on the formulation, implementation, and evaluation of strategic decisions that enable organizations to achieve sustainable competitive advantage.',
                'classification' => 'Common Core',
                'credit_hours' => 3,
                'program_code' => 'EMBA',
                'is_active' => true
            ],
            [
                'code' => 'EMBA7104',
                'name' => 'Strategic Marketing',
                'description' => 'This course explores advanced marketing strategies that drive organizational competitiveness in dynamic and global markets.',
                'classification' => 'Common Core',
                'credit_hours' => 3,
                'program_code' => 'EMBA',
                'is_active' => true
            ],
            [
                'code' => 'EMBA7105',
                'name' => 'Accounting & Finance for Decision Making',
                'description' => 'This course equips learners with the essential financial and accounting knowledge to support strategic business decisions.',
                'classification' => 'Common Core',
                'credit_hours' => 3,
                'program_code' => 'EMBA',
                'is_active' => true
            ],
            [
                'code' => 'EMBA7106',
                'name' => 'Business Analytics',
                'description' => 'This course introduces learners to data-driven decision-making through business analytics.',
                'classification' => 'Common Core',
                'credit_hours' => 3,
                'program_code' => 'EMBA',
                'is_active' => true
            ],
            [
                'code' => 'EMBA7107',
                'name' => 'Business Economics',
                'description' => 'This course provides a foundation in microeconomic and macroeconomic principles relevant to managerial decision-making.',
                'classification' => 'Common Core',
                'credit_hours' => 3,
                'program_code' => 'EMBA',
                'is_active' => true
            ],
            [
                'code' => 'EMBA7108',
                'name' => 'Digital Business',
                'description' => 'This course explores the transformation of traditional business models through digital technologies, platforms, and innovation.',
                'classification' => 'Common Core',
                'credit_hours' => 3,
                'program_code' => 'EMBA',
                'is_active' => true
            ],
            [
                'code' => 'EMBA7109',
                'name' => 'Innovation and Technology Entrepreneurship',
                'description' => 'This course explores the intersection of innovation, entrepreneurship, and emerging technologies in creating new business ventures.',
                'classification' => 'Common Core',
                'credit_hours' => 3,
                'program_code' => 'EMBA',
                'is_active' => true
            ],
            [
                'code' => 'EMBA7110',
                'name' => 'International Business Management & Policy',
                'description' => 'This course provides an in-depth understanding of global business dynamics and the policy frameworks affecting international operations.',
                'classification' => 'Common Core',
                'credit_hours' => 3,
                'program_code' => 'EMBA',
                'is_active' => true
            ],
            [
                'code' => 'EMBA7111',
                'name' => 'Research Methodology',
                'description' => 'This course equips learners with the fundamental knowledge and skills required to conduct rigorous business research.',
                'classification' => 'Common Core',
                'credit_hours' => 2,
                'program_code' => 'EMBA',
                'is_active' => true
            ],
            [
                'code' => 'EMBA7112',
                'name' => 'Strategic Capstone Project',
                'description' => 'This capstone project subject enables learners to demonstrate mastery of business administration concepts by solving real-world business challenges.',
                'classification' => 'Project',
                'credit_hours' => 8,
                'program_code' => 'EMBA',
                'is_active' => true
            ]
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
