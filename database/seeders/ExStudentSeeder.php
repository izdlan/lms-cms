<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExStudent;
use Carbon\Carbon;

class ExStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample ex-student data
        $exStudents = [
            [
                'student_id' => '670219-08-6113',
                'name' => 'Ahmad bin Abdullah',
                'email' => 'ahmad.abdullah@example.com',
                'phone' => '+60123456789',
                'program' => 'Bachelor of Computer Science',
                'graduation_year' => '2023',
                'graduation_month' => '06',
                'cgpa' => 3.75,
                'academic_records' => [
                    'year_1' => [
                        'semester_1' => [
                            ['code' => 'CS101', 'name' => 'Introduction to Programming', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                            ['code' => 'MATH101', 'name' => 'Calculus I', 'credits' => 4, 'grade' => 'B+', 'points' => 3.33],
                            ['code' => 'ENG101', 'name' => 'English Communication', 'credits' => 3, 'grade' => 'A-', 'points' => 3.67],
                            ['code' => 'PHY101', 'name' => 'Physics I', 'credits' => 4, 'grade' => 'B', 'points' => 3.00],
                            ['code' => 'CS102', 'name' => 'Computer Fundamentals', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                        ],
                        'semester_2' => [
                            ['code' => 'CS201', 'name' => 'Data Structures', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                            ['code' => 'MATH201', 'name' => 'Calculus II', 'credits' => 4, 'grade' => 'B+', 'points' => 3.33],
                            ['code' => 'ENG201', 'name' => 'Technical Writing', 'credits' => 3, 'grade' => 'A-', 'points' => 3.67],
                            ['code' => 'PHY201', 'name' => 'Physics II', 'credits' => 4, 'grade' => 'B', 'points' => 3.00],
                            ['code' => 'CS203', 'name' => 'Database Systems', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                        ],
                    ],
                    'year_2' => [
                        'semester_3' => [
                            ['code' => 'CS301', 'name' => 'Software Engineering', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                            ['code' => 'CS302', 'name' => 'Computer Networks', 'credits' => 3, 'grade' => 'A-', 'points' => 3.67],
                            ['code' => 'MATH301', 'name' => 'Discrete Mathematics', 'credits' => 3, 'grade' => 'B+', 'points' => 3.33],
                            ['code' => 'CS303', 'name' => 'Web Development', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                            ['code' => 'CS304', 'name' => 'Operating Systems', 'credits' => 4, 'grade' => 'B+', 'points' => 3.33],
                        ],
                        'semester_4' => [
                            ['code' => 'CS401', 'name' => 'Final Year Project', 'credits' => 6, 'grade' => 'A', 'points' => 4.00],
                            ['code' => 'CS402', 'name' => 'Machine Learning', 'credits' => 3, 'grade' => 'A-', 'points' => 3.67],
                            ['code' => 'CS403', 'name' => 'Cybersecurity', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                            ['code' => 'CS404', 'name' => 'Mobile App Development', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                            ['code' => 'CS405', 'name' => 'Cloud Computing', 'credits' => 3, 'grade' => 'A-', 'points' => 3.67],
                        ],
                    ],
                ],
                'certificate_data' => [
                    'degree' => 'Bachelor of Computer Science',
                    'honors' => 'Cum Laude',
                    'issue_date' => '2023-06-15',
                    'verification_code' => 'VERIFY-2023-001',
                ],
            ],
            [
                'student_id' => '670220-09-7224',
                'name' => 'Siti Nurhaliza binti Mohd',
                'email' => 'siti.nurhaliza@example.com',
                'phone' => '+60198765432',
                'program' => 'Bachelor of Business Administration',
                'graduation_year' => '2022',
                'graduation_month' => '12',
                'cgpa' => 3.85,
                'academic_records' => [
                    'year_1' => [
                        'semester_1' => [
                            ['code' => 'BUS101', 'name' => 'Principles of Management', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                            ['code' => 'ECON101', 'name' => 'Microeconomics', 'credits' => 3, 'grade' => 'A-', 'points' => 3.67],
                            ['code' => 'MATH101', 'name' => 'Business Mathematics', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                            ['code' => 'ENG101', 'name' => 'Business Communication', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                        ],
                        'semester_2' => [
                            ['code' => 'BUS201', 'name' => 'Marketing Principles', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                            ['code' => 'ECON201', 'name' => 'Macroeconomics', 'credits' => 3, 'grade' => 'A-', 'points' => 3.67],
                            ['code' => 'ACC201', 'name' => 'Financial Accounting', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                            ['code' => 'BUS202', 'name' => 'Organizational Behavior', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                        ],
                    ],
                ],
                'certificate_data' => [
                    'degree' => 'Bachelor of Business Administration',
                    'honors' => 'Magna Cum Laude',
                    'issue_date' => '2022-12-20',
                    'verification_code' => 'VERIFY-2022-002',
                ],
            ],
        ];

        foreach ($exStudents as $studentData) {
            ExStudent::createExStudent($studentData);
        }

        $this->command->info('Ex-student data created successfully!');
    }
}