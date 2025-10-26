<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeLearningOutcome extends Model
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
        'theoretical_foundation',
        'research_skills',
        'professional_competencies',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'assessment_methods' => 'array',
        'theoretical_foundation' => 'array',
        'research_skills' => 'array',
        'professional_competencies' => 'array'
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