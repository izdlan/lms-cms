<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'level',
        'duration_months',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_months' => 'integer'
    ];

    // Scope for active programs
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get full program name (code + name)
    public function getFullNameAttribute()
    {
        return $this->code . ' - ' . $this->name;
    }

    // Relationships
    public function programLearningOutcomes(): HasMany
    {
        return $this->hasMany(ProgramLearningOutcome::class);
    }

    public function courseLearningOutcomes(): HasMany
    {
        return $this->hasMany(CourseLearningOutcome::class);
    }

    public function programSubjects(): HasMany
    {
        return $this->hasMany(ProgramSubject::class);
    }
}



