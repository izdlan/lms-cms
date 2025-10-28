<?php

namespace App\Services;

use App\Models\UserActivity;
use App\Models\User;
use Illuminate\Http\Request;

class UserActivityService
{
    /**
     * Log user login activity
     */
    public static function logLogin(User $user, Request $request, string $loginMethod = 'email', string $status = UserActivity::STATUS_SUCCESS, string $notes = null)
    {
        UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => UserActivity::TYPE_LOGIN,
            'user_role' => $user->role,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_method' => $loginMethod,
            'status' => $status,
            'notes' => $notes,
        ]);
    }

    /**
     * Log user logout activity
     */
    public static function logLogout(User $user, Request $request, string $notes = null)
    {
        UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => UserActivity::TYPE_LOGOUT,
            'user_role' => $user->role,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => UserActivity::STATUS_SUCCESS,
            'notes' => $notes,
        ]);
    }

    /**
     * Log failed login attempt
     */
    public static function logFailedLogin(Request $request, string $loginMethod = 'email', string $identifier = null, string $reason = null)
    {
        // Try to find user by identifier
        $user = null;
        if ($identifier) {
            if ($loginMethod === 'ic') {
                $user = User::where('ic', $identifier)->first();
            } else {
                $user = User::where('email', $identifier)->first();
            }
        }

        UserActivity::create([
            'user_id' => $user ? $user->id : null,
            'activity_type' => UserActivity::TYPE_FAILED_LOGIN,
            'user_role' => $user ? $user->role : 'unknown',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_method' => $loginMethod,
            'status' => UserActivity::STATUS_FAILED,
            'notes' => $reason ?: 'Invalid credentials',
        ]);
    }

    /**
     * Get recent login activities for a user
     */
    public static function getRecentLogins(User $user, int $days = 30)
    {
        return UserActivity::where('user_id', $user->id)
            ->where('activity_type', UserActivity::TYPE_LOGIN)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all recent activities
     */
    public static function getAllRecentActivities(int $days = 30, int $limit = 100)
    {
        return UserActivity::with('user')
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get login statistics
     */
    public static function getLoginStats(int $days = 30)
    {
        $startDate = now()->subDays($days);
        
        return [
            'total_logins' => UserActivity::logins()
                ->where('created_at', '>=', $startDate)
                ->count(),
            'total_logouts' => UserActivity::logouts()
                ->where('created_at', '>=', $startDate)
                ->count(),
            'failed_logins' => UserActivity::failedLogins()
                ->where('created_at', '>=', $startDate)
                ->count(),
            'unique_users' => UserActivity::logins()
                ->where('created_at', '>=', $startDate)
                ->distinct('user_id')
                ->count(),
        ];
    }
}
