<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMaterial extends Model
{
    protected $fillable = [
        'subject_code',
        'class_code',
        'title',
        'description',
        'material_type',
        'file_path',
        'file_name',
        'file_size',
        'file_extension',
        'external_url',
        'author_name',
        'author_email',
        'is_active',
        'is_public',
        'download_count',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'download_count' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Get the subject that owns the material
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_code', 'subject_code');
    }

    /**
     * Get the file size in human readable format
     */
    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the file icon based on material type
     */
    public function getFileIconAttribute(): string
    {
        switch ($this->material_type) {
            case 'document':
                return 'fas fa-file-pdf';
            case 'video':
                return 'fas fa-video';
            case 'image':
                return 'fas fa-image';
            case 'audio':
                return 'fas fa-music';
            case 'link':
                return 'fas fa-link';
            default:
                return 'fas fa-file';
        }
    }

    /**
     * Get the download URL for the material
     */
    public function getDownloadUrlAttribute(): string
    {
        if ($this->external_url) {
            return $this->external_url;
        }
        
        return route('materials.download', $this->id);
    }
}


