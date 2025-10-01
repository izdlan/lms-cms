<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'classification',
        'credit_hours',
        'program_code',
        'is_active',
        'image'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_hours' => 'integer'
    ];

    // Scope for active subjects
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relationship with student enrollments
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class, 'subject_code', 'code');
    }

    // Relationship with class schedules
    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'subject_code', 'code');
    }

    // Relationship with course CLOs
    public function clos(): HasMany
    {
        return $this->hasMany(CourseClo::class, 'subject_code', 'code')->orderBy('order');
    }

    // Relationship with course topics
    public function topics(): HasMany
    {
        return $this->hasMany(CourseTopic::class, 'subject_code', 'code')->orderBy('order');
    }
}

