<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_type' => 'required|in:student,lecturer,admin',
            'password' => 'required|string',
        ]);

        $loginType = $request->input('login_type');
        
        if ($loginType === 'student') {
            return $this->handleStudentLogin($request);
        } elseif ($loginType === 'lecturer') {
            return $this->handleLecturerLogin($request);
        } elseif ($loginType === 'admin') {
            return $this->handleAdminLogin($request);
        }
    }

    private function handleStudentLogin(Request $request)
    {
        $request->validate([
            'ic' => 'required|string',
        ]);

        $credentials = [
            'ic' => $request->input('ic'),
            'password' => $request->input('password'),
        ];

        if (Auth::guard('student')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Check if password reset is required
            $user = Auth::guard('student')->user();
            if ($user->must_reset_password) {
                return redirect()->route('student.password.change');
            }
            
            return redirect()->intended(route('student.dashboard'));
        }

        return back()->withErrors([
            'ic' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('ic', 'login_type'));
    }

    private function handleLecturerLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Find user by email and role
        $user = User::where('email', $request->input('email'))
                   ->where('role', 'lecturer')
                   ->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            // Log in using staff guard
            Auth::guard('staff')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            
            // Store user role in session
            $request->session()->put('user_role', 'lecturer');
            
            // Check if password reset is required
            if ($user->must_reset_password) {
                return redirect()->route('staff.password.change');
            }
            
            return redirect()->intended(route('staff.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'login_type'));
    }

    private function handleAdminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Find user by email and role
        $user = User::where('email', $request->input('email'))
                   ->where('role', 'admin')
                   ->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            // Log in using web guard
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            
            // Store user role in session
            $request->session()->put('user_role', 'admin');
            
            // Check if password reset is required
            if ($user->must_reset_password) {
                return redirect()->route('admin.password.change');
            }
            
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'login_type'));
    }

    public function logout(Request $request)
    {
        // Determine which guard is currently authenticated
        if (Auth::guard('student')->check()) {
            Auth::guard('student')->logout();
        } elseif (Auth::guard('staff')->check()) {
            Auth::guard('staff')->logout();
        } else {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}