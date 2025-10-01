<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id',
        'user_id',
        'submission_text',
        'attachments',
        'marks_obtained',
        'feedback',
        'status',
        'is_late',
        'submitted_at',
        'graded_at',
        'graded_by'
    ];

    protected $casts = [
        'attachments' => 'array',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'is_late' => 'boolean',
        'marks_obtained' => 'decimal:2'
    ];

    // Relationship with assignment
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    // Relationship with user (student)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with grader (lecturer)
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // Scope for submitted assignments
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    // Scope for graded assignments
    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }

    // Check if submission is late
    public function isLate()
    {
        return $this->is_late || $this->submitted_at > $this->assignment->due_date;
    }

    // Get grade percentage
    public function getGradePercentageAttribute()
    {
        if (!$this->marks_obtained || !$this->assignment) {
            return 0;
        }
        
        return ($this->marks_obtained / $this->assignment->total_marks) * 100;
    }
}
