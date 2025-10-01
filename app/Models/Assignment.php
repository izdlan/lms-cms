<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'subject_code',
        'class_code',
        'lecturer_id',
        'total_marks',
        'passing_marks',
        'due_date',
        'available_from',
        'type',
        'status',
        'attachments',
        'instructions',
        'allow_late_submission',
        'late_penalty_percentage',
        'is_active'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'available_from' => 'datetime',
        'attachments' => 'array',
        'allow_late_submission' => 'boolean',
        'is_active' => 'boolean',
        'total_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'late_penalty_percentage' => 'integer'
    ];

    // Relationship with subject
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_code', 'code');
    }

    // Relationship with class schedule
    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class, 'class_code', 'class_code');
    }

    // Relationship with lecturer
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    // Relationship with submissions
    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    // Scope for active assignments
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for published assignments
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Check if assignment is available for submission
    public function isAvailableForSubmission()
    {
        return $this->status === 'published' && 
               $this->available_from <= now() && 
               ($this->due_date >= now() || $this->allow_late_submission);
    }

    // Check if assignment is past due
    public function isPastDue()
    {
        return $this->due_date < now() && !$this->allow_late_submission;
    }
}
