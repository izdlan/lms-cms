<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lecturer extends Model
{
    protected $fillable = [
        'user_id',
        'staff_id',
        'name',
        'email',
        'phone',
        'department',
        'specialization',
        'bio',
        'profile_picture',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Scope for active lecturers
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relationship with user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with student enrollments
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    // Relationship with class schedules
    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }
}

