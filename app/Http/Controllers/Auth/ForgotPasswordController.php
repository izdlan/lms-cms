<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset code via WhatsApp
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            // Find user by email
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return back()->withErrors(['email' => 'No account found with this email address.']);
            }

            // Debug logging
            Log::info("Forgot password attempt for user: {$user->email}, role: {$user->role}, user_id: {$user->id}");

            // Generate reset code
            $resetCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            $token = Str::random(60);

            // Store reset request
            DB::table('password_resets')->insert([
                'email' => $user->email,
                'token' => $token,
                'reset_code' => $resetCode,
                'created_at' => now(),
                'expires_at' => now()->addMinutes(15) // Code expires in 15 minutes
            ]);

            // Send reset code via email
            $emailSent = $this->sendEmailResetCode($user->email, $resetCode);
            
            if ($emailSent) {
                return redirect()->route('reset-password', $token)
                    ->with('success', 'Reset code sent to your email address. Please check your inbox.');
            } else {
                return back()->withErrors(['error' => 'Failed to send reset code. Please try again or contact support.']);
            }

        } catch (\Exception $e) {
            Log::error('Forgot password error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred. Please try again.']);
        }
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm($token)
    {
        $resetRequest = DB::table('password_resets')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetRequest) {
            return redirect()->route('forgot-password')
                ->withErrors(['error' => 'Invalid or expired reset link.']);
        }

        return view('auth.reset-password', compact('token'));
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'reset_code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed'
        ]);

        try {
            $resetRequest = DB::table('password_resets')
                ->where('token', $request->token)
                ->where('reset_code', $request->reset_code)
                ->where('expires_at', '>', now())
                ->first();

            if (!$resetRequest) {
                return back()->withErrors(['reset_code' => 'Invalid or expired reset code.']);
            }

            // Find user and update password
            $user = User::where('email', $resetRequest->email)->first();
            if ($user) {
                $user->update([
                    'password' => Hash::make($request->password)
                ]);

                // Delete reset request
                DB::table('password_resets')
                    ->where('token', $request->token)
                    ->delete();

                return redirect()->route('login')
                    ->with('success', 'Password reset successfully. You can now login with your new password.');
            }

            return back()->withErrors(['error' => 'User not found.']);

        } catch (\Exception $e) {
            Log::error('Reset password error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred. Please try again.']);
        }
    }


    /**
     * Send reset code via email
     */
    private function sendEmailResetCode($email, $code)
    {
        try {
            // Use Laravel's Mail facade for better reliability
            $subject = 'Password Reset Code - Olympia Education';
            $message = "Password Reset Code

Your password reset code is: {$code}

This code will expire in 15 minutes.
If you didn't request this, please ignore this email.

Best regards,
Olympia Education";

            // Try Laravel Mail first
            try {
                Mail::raw($message, function ($mail) use ($email, $subject) {
                    $mail->to($email)
                         ->subject($subject)
                         ->from('kentomomota739@gmail.com', 'Olympia Education');
                });
                
                Log::info("Reset code sent via Laravel Mail to: {$email}");
                return true;
            } catch (\Exception $e) {
                Log::warning("Laravel Mail failed: " . $e->getMessage());
                
                // Fallback to basic mail function
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/plain;charset=UTF-8" . "\r\n";
                $headers .= "From: kentomomota739@gmail.com" . "\r\n";
                $headers .= "Reply-To: kentomomota739@gmail.com" . "\r\n";

                $sent = mail($email, $subject, $message, $headers);
                
                if ($sent) {
                    Log::info("Reset code sent via basic mail to: {$email}");
                    return true;
                } else {
                    Log::error("Both Laravel Mail and basic mail failed for: {$email}");
                    return false;
                }
            }

        } catch (\Exception $e) {
            Log::error('Email send error: ' . $e->getMessage());
            return false;
        }
    }

}
