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
            'login_type' => 'required|in:student,admin',
            'password' => 'required|string',
        ]);

        $loginType = $request->input('login_type');
        
        if ($loginType === 'student') {
            return $this->handleStudentLogin($request);
        } else {
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

    private function handleAdminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
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
        } elseif (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
