<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SyncActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'status',
        'message',
        'created_count',
        'updated_count',
        'error_count',
        'processed_sheets',
        'source'
    ];

    protected $casts = [
        'processed_sheets' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get recent activities (last 24 hours)
     */
    public static function getRecentActivities($hours = 24)
    {
        return self::where('created_at', '>=', Carbon::now()->subHours($hours))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Clean up old activities (older than specified hours)
     */
    public static function cleanupOldActivities($hours = 24)
    {
        return self::where('created_at', '<', Carbon::now()->subHours($hours))
            ->delete();
    }

    /**
     * Log a sync activity
     */
    public static function logActivity($type, $status, $message, $data = [])
    {
        return self::create([
            'type' => $type,
            'status' => $status,
            'message' => $message,
            'created_count' => $data['created'] ?? 0,
            'updated_count' => $data['updated'] ?? 0,
            'error_count' => $data['errors'] ?? 0,
            'processed_sheets' => $data['processed_sheets'] ?? null,
            'source' => $data['source'] ?? 'google_drive'
        ]);
    }

    /**
     * Get formatted time for display
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('g:i:s A');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'success' => 'success',
            'error' => 'danger',
            'warning' => 'warning',
            default => 'secondary'
        };
    }
}