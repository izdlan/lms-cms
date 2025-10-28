<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserActivity;
use App\Services\UserActivityService;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_type' => 'required|in:student,lecturer,admin,finance_admin',
            'password' => 'required|string',
        ]);

        $loginType = $request->input('login_type');
        
        if ($loginType === 'student') {
            return $this->handleStudentLogin($request);
        } elseif ($loginType === 'lecturer') {
            return $this->handleLecturerLogin($request);
        } elseif ($loginType === 'admin') {
            return $this->handleAdminLogin($request);
        } elseif ($loginType === 'finance_admin') {
            return $this->handleFinanceAdminLogin($request);
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
            
            // Log successful login
            $user = Auth::guard('student')->user();
            UserActivityService::logLogin($user, $request, UserActivity::METHOD_IC);
            
            // Check if password reset is required
            if ($user->must_reset_password) {
                return redirect()->route('student.password.change');
            }
            
            return redirect()->intended(route('student.dashboard'));
        }

        // Log failed login attempt
        UserActivityService::logFailedLogin($request, UserActivity::METHOD_IC, $request->input('ic'), 'Invalid IC or password');

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
            
            // Log successful login
            UserActivityService::logLogin($user, $request, UserActivity::METHOD_EMAIL);
            
            // Store user role in session
            $request->session()->put('user_role', 'lecturer');
            
            // Check if password reset is required
            if ($user->must_reset_password) {
                return redirect()->route('staff.password.change');
            }
            
            return redirect()->intended(route('staff.dashboard'));
        }

        // Log failed login attempt
        UserActivityService::logFailedLogin($request, UserActivity::METHOD_EMAIL, $request->input('email'), 'Invalid email or password');

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
            
            // Log successful login
            UserActivityService::logLogin($user, $request, UserActivity::METHOD_EMAIL);
            
            // Store user role in session
            $request->session()->put('user_role', 'admin');
            
            // Check if password reset is required
            if ($user->must_reset_password) {
                return redirect()->route('admin.password.change');
            }
            
            return redirect()->intended(route('admin.dashboard'));
        }

        // Log failed login attempt
        UserActivityService::logFailedLogin($request, UserActivity::METHOD_EMAIL, $request->input('email'), 'Invalid email or password');

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'login_type'));
    }

    private function handleFinanceAdminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Check if user is finance admin
            if (!$user->isFinanceAdmin() && !$user->isAdmin()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have permission to access the finance admin panel.',
                ])->withInput($request->only('email', 'login_type'));
            }
            
            // Check if user is blocked
            if ($user->isBlocked()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been blocked. Please contact the administrator.',
                ])->withInput($request->only('email', 'login_type'));
            }
            
            $request->session()->regenerate();
            
            // Check if password reset is required
            if ($user->must_reset_password) {
                return redirect()->route('finance-admin.password.change');
            }
            
            return redirect()->intended(route('finance-admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'login_type'));
    }

    public function logout(Request $request)
    {
        $user = null;
        
        // Determine which guard is currently authenticated and log the user before logout
        if (Auth::guard('student')->check()) {
            $user = Auth::guard('student')->user();
            Auth::guard('student')->logout();
        } elseif (Auth::guard('staff')->check()) {
            $user = Auth::guard('staff')->user();
            Auth::guard('staff')->logout();
        } elseif (Auth::guard('finance_admin')->check()) {
            $user = Auth::guard('finance_admin')->user();
            Auth::guard('finance_admin')->logout();
        } else {
            $user = Auth::user();
            Auth::logout();
        }

        // Log logout activity
        if ($user) {
            UserActivityService::logLogout($user, $request);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}