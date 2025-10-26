<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramSubject extends Model
{
    protected $fillable = [
        'program_id',
        'subject_name',
        'subject_code',
        'description',
        'classification',
        'credit_hours',
        'teaching_hours',
        'assessment_methods',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'credit_hours' => 'integer',
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
        return $query->orderBy('sort_order')->orderBy('subject_name');
    }
}
