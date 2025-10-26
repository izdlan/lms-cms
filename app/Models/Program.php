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

    // Academic Level Specific Relationships
    public function diplomaLearningOutcomes(): HasMany
    {
        return $this->hasMany(DiplomaLearningOutcome::class);
    }

    public function degreeLearningOutcomes(): HasMany
    {
        return $this->hasMany(DegreeLearningOutcome::class);
    }

    public function masterLearningOutcomes(): HasMany
    {
        return $this->hasMany(MasterLearningOutcome::class);
    }

    public function phdLearningOutcomes(): HasMany
    {
        return $this->hasMany(PhdLearningOutcome::class);
    }

    // Get learning outcomes based on program level
    public function getLearningOutcomesByLevel()
    {
        switch (strtolower($this->level)) {
            case 'diploma':
                return $this->diplomaLearningOutcomes()->active()->ordered()->get();
            case 'degree':
            case 'bachelor':
                return $this->degreeLearningOutcomes()->active()->ordered()->get();
            case 'master':
            case 'masters':
                return $this->masterLearningOutcomes()->active()->ordered()->get();
            case 'phd':
            case 'doctorate':
                return $this->phdLearningOutcomes()->active()->ordered()->get();
            default:
                return $this->programLearningOutcomes()->active()->ordered()->get();
        }
    }
}



