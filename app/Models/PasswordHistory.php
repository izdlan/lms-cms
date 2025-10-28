<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class PasswordHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'password_hash',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Check if a password has been used recently
     */
    public static function hasUsedPassword($userId, $password, $lastCount = 5)
    {
        $recentPasswords = self::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($lastCount)
            ->get();

        foreach ($recentPasswords as $passwordHistory) {
            if (Hash::check($password, $passwordHistory->password_hash)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Store a new password in history
     */
    public static function storePassword($userId, $password)
    {
        self::create([
            'user_id' => $userId,
            'password_hash' => Hash::make($password),
        ]);

        // Keep only the last 10 passwords
        $userPasswords = self::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($userPasswords->count() > 10) {
            $passwordsToDelete = $userPasswords->slice(10);
            foreach ($passwordsToDelete as $passwordToDelete) {
                $passwordToDelete->delete();
            }
        }
    }
}