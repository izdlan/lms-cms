<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    protected $fillable = [
        'user_id',
        'activity_type',
        'user_role',
        'ip_address',
        'user_agent',
        'login_method',
        'status',
        'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Activity types
    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';
    const TYPE_FAILED_LOGIN = 'failed_login';

    // Status types
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_BLOCKED = 'blocked';

    // Login methods
    const METHOD_IC = 'ic';
    const METHOD_EMAIL = 'email';

    /**
     * Get the user that owns the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for login activities
     */
    public function scopeLogins($query)
    {
        return $query->where('activity_type', self::TYPE_LOGIN);
    }

    /**
     * Scope for logout activities
     */
    public function scopeLogouts($query)
    {
        return $query->where('activity_type', self::TYPE_LOGOUT);
    }

    /**
     * Scope for failed login attempts
     */
    public function scopeFailedLogins($query)
    {
        return $query->where('activity_type', self::TYPE_FAILED_LOGIN);
    }

    /**
     * Scope for recent activities
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
