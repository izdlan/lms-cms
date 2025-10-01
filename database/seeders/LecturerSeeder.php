<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lecturer;

class LecturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lecturers = [
            [
                'staff_id' => 'LEC001',
                'name' => 'Dr. Sarah Johnson',
                'email' => 'sarah.johnson@olympia.edu',
                'phone' => '+60123456789',
                'department' => 'Human Resource Management',
                'specialization' => 'Strategic HRM, Organizational Development',
                'bio' => 'Dr. Sarah Johnson is a renowned expert in Strategic Human Resource Management with over 15 years of experience.',
                'is_active' => true
            ],
            [
                'staff_id' => 'LEC002',
                'name' => 'Prof. Michael Chen',
                'email' => 'michael.chen@olympia.edu',
                'phone' => '+60123456790',
                'department' => 'Organizational Behavior',
                'specialization' => 'Organizational Psychology, Leadership',
                'bio' => 'Prof. Michael Chen specializes in organizational behavior and leadership development.',
                'is_active' => true
            ],
            [
                'staff_id' => 'LEC003',
                'name' => 'Dr. Lisa Wong',
                'email' => 'lisa.wong@olympia.edu',
                'phone' => '+60123456791',
                'department' => 'Strategic Management',
                'specialization' => 'Strategic Planning, Competitive Analysis',
                'bio' => 'Dr. Lisa Wong is an expert in strategic management and competitive strategy.',
                'is_active' => true
            ],
            [
                'staff_id' => 'LEC004',
                'name' => 'Prof. Ahmad Rahman',
                'email' => 'ahmad.rahman@olympia.edu',
                'phone' => '+60123456792',
                'department' => 'Marketing',
                'specialization' => 'Digital Marketing, Brand Management',
                'bio' => 'Prof. Ahmad Rahman specializes in strategic marketing and digital transformation.',
                'is_active' => true
            ],
            [
                'staff_id' => 'LEC005',
                'name' => 'Dr. Emily Davis',
                'email' => 'emily.davis@olympia.edu',
                'phone' => '+60123456793',
                'department' => 'Finance',
                'specialization' => 'Corporate Finance, Investment Analysis',
                'bio' => 'Dr. Emily Davis is a finance expert with extensive experience in corporate finance.',
                'is_active' => true
            ],
            [
                'staff_id' => 'LEC006',
                'name' => 'Prof. David Lee',
                'email' => 'david.lee@olympia.edu',
                'phone' => '+60123456794',
                'department' => 'Business Analytics',
                'specialization' => 'Data Science, Business Intelligence',
                'bio' => 'Prof. David Lee specializes in business analytics and data-driven decision making.',
                'is_active' => true
            ],
            [
                'staff_id' => 'LEC007',
                'name' => 'Dr. Maria Rodriguez',
                'email' => 'maria.rodriguez@olympia.edu',
                'phone' => '+60123456795',
                'department' => 'Economics',
                'specialization' => 'Microeconomics, Macroeconomics',
                'bio' => 'Dr. Maria Rodriguez is an economics expert with focus on business applications.',
                'is_active' => true
            ],
            [
                'staff_id' => 'LEC008',
                'name' => 'Prof. James Wilson',
                'email' => 'james.wilson@olympia.edu',
                'phone' => '+60123456796',
                'department' => 'Digital Business',
                'specialization' => 'E-commerce, Digital Transformation',
                'bio' => 'Prof. James Wilson specializes in digital business and technology innovation.',
                'is_active' => true
            ],
            [
                'staff_id' => 'LEC009',
                'name' => 'Dr. Anna Thompson',
                'email' => 'anna.thompson@olympia.edu',
                'phone' => '+60123456797',
                'department' => 'Innovation & Entrepreneurship',
                'specialization' => 'Innovation Management, Startups',
                'bio' => 'Dr. Anna Thompson is an expert in innovation and technology entrepreneurship.',
                'is_active' => true
            ],
            [
                'staff_id' => 'LEC010',
                'name' => 'Prof. Robert Kim',
                'email' => 'robert.kim@olympia.edu',
                'phone' => '+60123456798',
                'department' => 'International Business',
                'specialization' => 'Global Strategy, Cross-cultural Management',
                'bio' => 'Prof. Robert Kim specializes in international business and global management.',
                'is_active' => true
            ],
            [
                'staff_id' => 'LEC011',
                'name' => 'Dr. Jennifer Brown',
                'email' => 'jennifer.brown@olympia.edu',
                'phone' => '+60123456799',
                'department' => 'Research Methods',
                'specialization' => 'Quantitative Research, Qualitative Research',
                'bio' => 'Dr. Jennifer Brown is an expert in research methodology and academic writing.',
                'is_active' => true
            ],
            [
                'staff_id' => 'LEC012',
                'name' => 'Prof. Kevin Tan',
                'email' => 'kevin.tan@olympia.edu',
                'phone' => '+60123456800',
                'department' => 'Project Management',
                'specialization' => 'Strategic Projects, Capstone Supervision',
                'bio' => 'Prof. Kevin Tan specializes in project management and capstone project supervision.',
                'is_active' => true
            ]
        ];

        foreach ($lecturers as $lecturer) {
            Lecturer::create($lecturer);
        }
    }
}
