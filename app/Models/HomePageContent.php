<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomePageContent extends Model
{
    protected $fillable = [
        'section_name',
        'title',
        'content',
        'image_url',
        'metadata',
        'sort_order',
        'is_active',
        'admin_id'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean'
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
