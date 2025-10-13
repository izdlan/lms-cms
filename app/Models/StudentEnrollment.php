<?php
/**
 * Model: StudentEnrollment
 * Purpose: Tracks a student's enrollment in a subject/class with lecturer and status,
 *          including enrollment/completion dates and awarded grade.
 * Relations: belongsTo User (student), Subject, Lecturer, ClassSchedule.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'program_code',
        'subject_code',
        'lecturer_id',
        'class_code',
        'status',
        'enrollment_date',
        'completion_date',
        'grade'
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'completion_date' => 'date',
        'grade' => 'decimal:2'
    ];

    // Relationship with user (student)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with subject
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_code', 'code');
    }

    // Relationship with lecturer
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    // Relationship with class schedule
    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class, 'class_code', 'class_code');
    }
}

