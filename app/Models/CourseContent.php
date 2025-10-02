<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseContent extends Model
{
    protected $fillable = [
        'subject_code',
        'class_code',
        'title',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by_name',
        'uploaded_by_email',
        'is_active',
        'download_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer'
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_code', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSubject($query, $subjectCode)
    {
        return $query->where('subject_code', $subjectCode);
    }

    public function scopeForClass($query, $classCode)
    {
        return $query->where('class_code', $classCode);
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) return 'Unknown';
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileIconAttribute()
    {
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'pdf':
                return 'fas fa-file-pdf text-danger';
            case 'doc':
            case 'docx':
                return 'fas fa-file-word text-primary';
            case 'xls':
            case 'xlsx':
                return 'fas fa-file-excel text-success';
            case 'ppt':
            case 'pptx':
                return 'fas fa-file-powerpoint text-warning';
            case 'zip':
            case 'rar':
                return 'fas fa-file-archive text-secondary';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'fas fa-file-image text-info';
            default:
                return 'fas fa-file text-muted';
        }
    }
}



