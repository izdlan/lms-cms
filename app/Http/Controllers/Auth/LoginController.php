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
        $loginType = $request->input('login_type');
        
        // Debug: Check what's being submitted
        if (empty($loginType)) {
            return back()->withErrors([
                'login_type' => 'Please select a login type.',
            ])->withInput();
        }
        
        // Validate based on login type
        if ($loginType === 'student') {
            $request->validate([
                'login_type' => 'required|in:student,staff,admin',
                'ic' => 'required|string',
                'password' => 'required|string',
            ]);
            return $this->handleStudentLogin($request);
        } elseif ($loginType === 'staff') {
            // Debug: Log staff login attempt
            \Log::info('Staff login attempt', [
                'login_type' => $loginType,
                'email' => $request->input('email'),
                'has_email' => !empty($request->input('email'))
            ]);
            
            $request->validate([
                'login_type' => 'required|in:student,staff,admin',
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
            return $this->handleStaffLogin($request);
        } else {
            $request->validate([
                'login_type' => 'required|in:student,staff,admin',
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
            return $this->handleAdminLogin($request);
        }
    }

    private function handleStudentLogin(Request $request)
    {

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

    private function handleStaffLogin(Request $request)
    {

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        // First try to authenticate with the default guard
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Check if the user has staff role
            if ($user->role === 'staff') {
                // Keep using the default guard but store the role in session
                $request->session()->put('user_role', 'staff');
                $request->session()->regenerate();
                return redirect()->intended(route('staff.dashboard'));
            } else {
                // User exists but doesn't have staff role
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. This account does not have staff privileges.',
                ])->withInput($request->only('email', 'login_type'));
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'login_type'));
    }

    private function handleAdminLogin(Request $request)
    {

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        // First try to authenticate with the default guard
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Check if the user has admin role
            if ($user->role === 'admin') {
                // Keep using the default guard but store the role in session
                $request->session()->put('user_role', 'admin');
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            } else {
                // User exists but doesn't have admin role
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. This account does not have admin privileges.',
                ])->withInput($request->only('email', 'login_type'));
            }
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
        } elseif (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
