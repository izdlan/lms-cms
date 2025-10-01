<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSchedule extends Model
{
    protected $fillable = [
        'class_code',
        'subject_code',
        'lecturer_id',
        'program_code',
        'class_name',
        'description',
        'venue',
        'day_of_week',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'max_students',
        'is_active'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'start_date' => 'date',
        'end_date' => 'date',
        'max_students' => 'integer',
        'is_active' => 'boolean'
    ];

    // Scope for active class schedules
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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

    // Relationship with student enrollments
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class, 'class_code', 'class_code');
    }
}

