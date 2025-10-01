<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'ic_number',
        'program_code',
        'intake_date',
        'profile_picture',
        'is_active',
    ];

    protected $casts = [
        'intake_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model and register model events
     */
    protected static function boot()
    {
        parent::boot();

        // When a student is created or program_code is updated, auto-enroll in all program subjects
        static::created(function ($student) {
            $student->autoEnrollInProgramSubjects();
        });

        static::updated(function ($student) {
            // Check if program_code was changed
            if ($student->isDirty('program_code')) {
                $student->autoEnrollInProgramSubjects();
            }
        });
    }

    /**
     * Relationship with user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with student enrollments
     */
    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class, 'user_id', 'user_id');
    }

    /**
     * Get enrolled subjects for this student
     */
    public function enrolledSubjects()
    {
        return $this->enrollments()->with(['subject', 'lecturer', 'classSchedule']);
    }

    /**
     * Automatically enroll student in all subjects for their program
     */
    public function autoEnrollInProgramSubjects()
    {
        if (!$this->program_code) {
            Log::warning("Student {$this->id} has no program_code, skipping auto-enrollment");
            return;
        }

        // Get all active subjects for this program
        $subjects = Subject::where('program_code', $this->program_code)
            ->where('is_active', true)
            ->get();

        if ($subjects->isEmpty()) {
            Log::info("No active subjects found for program: {$this->program_code}");
            return;
        }

        $enrolledCount = 0;
        $skippedCount = 0;

        foreach ($subjects as $subject) {
            // Check if student is already enrolled in this subject
            $existingEnrollment = StudentEnrollment::where('user_id', $this->user_id)
                ->where('subject_code', $subject->code)
                ->first();

            if ($existingEnrollment) {
                $skippedCount++;
                Log::info("Student {$this->user_id} already enrolled in subject {$subject->code}");
                continue;
            }

            // Find a lecturer for this subject (if any class schedules exist)
            $classSchedule = ClassSchedule::where('subject_code', $subject->code)
                ->where('program_code', $this->program_code)
                ->where('is_active', true)
                ->first();

            $lecturerId = $classSchedule ? $classSchedule->lecturer_id : null;
            $classCode = $classSchedule ? $classSchedule->class_code : null;

            // Create enrollment
            StudentEnrollment::create([
                'user_id' => $this->user_id,
                'program_code' => $this->program_code,
                'subject_code' => $subject->code,
                'lecturer_id' => $lecturerId,
                'class_code' => $classCode,
                'status' => 'enrolled',
                'enrollment_date' => now()->toDateString(),
            ]);

            $enrolledCount++;
            Log::info("Auto-enrolled student {$this->user_id} in subject {$subject->code}");
        }

        Log::info("Auto-enrollment completed for student {$this->user_id}: {$enrolledCount} enrolled, {$skippedCount} skipped");
    }
}