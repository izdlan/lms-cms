<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseLearningOutcome extends Model
{
    protected $fillable = [
        'program_id',
        'course_name',
        'clo_code',
        'description',
        'mqf_domain',
        'mqf_code',
        'topics_covered',
        'assessment_methods',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'topics_covered' => 'array',
        'assessment_methods' => 'array'
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('clo_code');
    }
}
