<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class StrongPassword implements Rule
{
    private $message = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check minimum length
        if (strlen($value) < 8) {
            $this->message = 'Password must be at least 8 characters long.';
            return false;
        }

        // Check for uppercase letter
        if (!preg_match('/[A-Z]/', $value)) {
            $this->message = 'Password must contain at least one uppercase letter.';
            return false;
        }

        // Check for lowercase letter
        if (!preg_match('/[a-z]/', $value)) {
            $this->message = 'Password must contain at least one lowercase letter.';
            return false;
        }

        // Check for number
        if (!preg_match('/[0-9]/', $value)) {
            $this->message = 'Password must contain at least one number.';
            return false;
        }

        // Check for special character
        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
            $this->message = 'Password must contain at least one special character (!@#$%^&*).';
            return false;
        }

        // Check against common weak passwords
        $commonPasswords = [
            'password', '123456', '12345678', 'qwerty', 'abc123', 'password123',
            'admin', 'letmein', 'welcome', 'monkey', '1234567890', 'password1',
            'qwerty123', 'dragon', 'master', 'hello', 'freedom', 'whatever',
            'qazwsx', 'trustno1', 'jordan23', 'harley', 'ranger', 'jennifer',
            'hunter', 'fuck', '2000', 'test', 'batman', 'thomas', 'hockey',
            'ranger', 'daniel', 'hannah', 'maggie', 'jessica', 'charlie',
            'jordan', 'michelle', 'andrew', 'joshua', 'superman', 'harley',
            'password123', '123456789', 'qwertyuiop', 'asdfghjkl', 'zxcvbnm'
        ];

        if (in_array(strtolower($value), $commonPasswords)) {
            $this->message = 'This password is too common. Please choose a more secure password.';
            return false;
        }

        // Check for repeated characters (more than 2 in a row)
        if (preg_match('/(.)\1{2,}/', $value)) {
            $this->message = 'Password cannot contain more than 2 consecutive identical characters.';
            return false;
        }

        // Check for sequential characters (123, abc, etc.)
        if (preg_match('/(?:012|123|234|345|456|567|678|789|890|abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz)/i', $value)) {
            $this->message = 'Password cannot contain sequential characters (123, abc, etc.).';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}