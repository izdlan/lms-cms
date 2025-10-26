<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhdLearningOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'plo_code',
        'description',
        'mqf_domain',
        'mqf_code',
        'mapped_courses',
        'assessment_methods',
        'original_research',
        'advanced_research_methods',
        'theoretical_contribution',
        'publication_requirements',
        'supervision_skills',
        'dissertation_defense',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'assessment_methods' => 'array',
        'original_research' => 'array',
        'advanced_research_methods' => 'array',
        'theoretical_contribution' => 'array',
        'publication_requirements' => 'array',
        'supervision_skills' => 'array',
        'dissertation_defense' => 'array'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('plo_code');
    }
}