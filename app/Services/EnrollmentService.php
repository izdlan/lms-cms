<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subject;
use App\Models\ClassSchedule;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\Log;

class EnrollmentService
{
    /**
     * Enroll a single student user into all subjects for their program.
     * Returns summary counts.
     */
    public function enrollUser(User $user): array
    {
        if ($user->role !== 'student') {
            return ['enrolled' => 0, 'skipped' => 0, 'errors' => 1, 'message' => 'Not a student'];
        }

        $programName = $user->programme_name ?? '';
        $subjectProgramCode = $this->mapProgramNameToCode($programName);

        if (!$subjectProgramCode) {
            return ['enrolled' => 0, 'skipped' => 0, 'errors' => 1, 'message' => 'Program not recognized'];
        }

        $subjects = Subject::where('program_code', $subjectProgramCode)->get();
        $enrolled = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($subjects as $subject) {
            $existing = StudentEnrollment::where('user_id', $user->id)
                ->where('subject_code', $subject->code)
                ->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            $classSchedule = ClassSchedule::where('subject_code', $subject->code)
                ->where('program_code', $subjectProgramCode)
                ->first();

            if (!$classSchedule) {
                $errors++;
                Log::warning('No class schedule found for subject during enrollUser', [
                    'user_id' => $user->id,
                    'subject_code' => $subject->code,
                    'program_code' => $subjectProgramCode,
                ]);
                continue;
            }

            StudentEnrollment::create([
                'user_id' => $user->id,
                'program_code' => $subjectProgramCode,
                'subject_code' => $subject->code,
                'lecturer_id' => $classSchedule->lecturer_id,
                'class_code' => $classSchedule->class_code,
                'status' => 'enrolled',
                'enrollment_date' => now(),
            ]);

            $enrolled++;
        }

        return ['enrolled' => $enrolled, 'skipped' => $skipped, 'errors' => $errors];
    }

    /**
     * Enroll all students who have zero enrollments yet (recent/un-enrolled).
     */
    public function enrollAllUnenrolled(): array
    {
        $students = User::where('role', 'student')
            ->whereDoesntHave('enrollments')
            ->get();

        $summary = ['studentsProcessed' => 0, 'totalEnrolled' => 0, 'totalSkipped' => 0, 'totalErrors' => 0];

        foreach ($students as $student) {
            $result = $this->enrollUser($student);
            $summary['studentsProcessed']++;
            $summary['totalEnrolled'] += $result['enrolled'] ?? 0;
            $summary['totalSkipped'] += $result['skipped'] ?? 0;
            $summary['totalErrors'] += $result['errors'] ?? 0;
        }

        return $summary;
    }

    private function mapProgramNameToCode(?string $programName): ?string
    {
        if (!$programName) {
            return null;
        }

        $upper = strtoupper($programName);
        if (str_contains($upper, 'EMBA')) {
            return 'EMBA';
        }
        if (str_contains($upper, 'EBBA')) {
            return 'EBBA';
        }
        if (str_contains($upper, 'EDBA')) {
            return 'EDBA';
        }

        return null;
    }
}


