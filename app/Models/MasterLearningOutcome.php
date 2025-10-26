<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterLearningOutcome extends Model
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
        'advanced_knowledge',
        'research_methodology',
        'leadership_skills',
        'critical_thinking',
        'dissertation_requirements',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'assessment_methods' => 'array',
        'advanced_knowledge' => 'array',
        'research_methodology' => 'array',
        'leadership_skills' => 'array',
        'critical_thinking' => 'array',
        'dissertation_requirements' => 'array'
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