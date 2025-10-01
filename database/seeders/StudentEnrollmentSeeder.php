<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Models\Lecturer;
use Carbon\Carbon;

class StudentEnrollmentSeeder extends Seeder
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
        
        // Class codes for each subject
        $classCodes = [
            'EMBA7101' => 'HRM-001',
            'EMBA7102' => 'OB-001', 
            'EMBA7103' => 'SM-001',
            'EMBA7104' => 'MKT-001',
            'EMBA7105' => 'FIN-001',
            'EMBA7106' => 'BA-001',
            'EMBA7107' => 'ECON-001',
            'EMBA7108' => 'DB-001',
            'EMBA7109' => 'ITE-001',
            'EMBA7110' => 'IBM-001',
            'EMBA7111' => 'RM-001',
            'EMBA7112' => 'PROJ-001'
        ];
        
        foreach ($students as $student) {
            foreach ($subjects as $index => $subjectCode) {
                // Assign lecturer based on subject index
                $lecturer = $lecturers->get($index % $lecturers->count());
                
                StudentEnrollment::create([
                    'user_id' => $student->id,
                    'program_code' => 'EMBA',
                    'subject_code' => $subjectCode,
                    'lecturer_id' => $lecturer->id,
                    'class_code' => $classCodes[$subjectCode],
                    'status' => 'enrolled',
                    'enrollment_date' => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
