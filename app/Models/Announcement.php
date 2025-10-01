<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'subject_code',
        'class_code',
        'title',
        'content',
        'author_name',
        'author_email',
        'is_important',
        'is_active',
        'published_at'
    ];

    protected $casts = [
        'is_important' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime'
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_code', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    public function scopeForSubject($query, $subjectCode)
    {
        return $query->where('subject_code', $subjectCode);
    }

    public function scopeForClass($query, $classCode)
    {
        return $query->where('class_code', $classCode);
    }
}

