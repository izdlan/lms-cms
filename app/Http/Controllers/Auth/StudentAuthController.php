<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class StudentAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.student-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'ic' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by IC number only (original LMS_Olympia logic)
        $user = User::where('ic', $request->ic)
                   ->where('role', 'student')
                   ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->filled('remember'));
            
            if ($user->must_reset_password) {
                return redirect()->route('student.password.reset');
            }
            
            return redirect()->intended(route('student.dashboard'));
        }

        return back()->withErrors([
            'ic' => 'The provided credentials do not match our records.',
        ])->onlyInput('ic');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('student.login');
    }

    public function showPasswordResetForm()
    {
        return view('auth.student-password-reset');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)
                   ->where('role', 'student')
                   ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email not found.']);
        }

        $token = Password::createToken($user);
        $user->sendPasswordResetNotification($token);

        return back()->with('status', 'Password reset link sent to your email.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'must_reset_password' => false,
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('student.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function showPasswordChangeForm()
    {
        return view('student.password-change');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Password changed successfully!');
    }
}