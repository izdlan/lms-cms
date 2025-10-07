<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminFinanceAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.admin-finance-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_type' => 'required|in:admin,finance_admin',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $loginType = $request->input('login_type');
        
        if ($loginType === 'admin') {
            return $this->handleAdminLogin($request);
        } elseif ($loginType === 'finance_admin') {
            return $this->handleFinanceAdminLogin($request);
        }
    }

    private function handleAdminLogin(Request $request)
    {
        // Find user by email and role
        $user = User::where('email', $request->input('email'))
                   ->where('role', 'admin')
                   ->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
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

    private function handleFinanceAdminLogin(Request $request)
    {
        // Find user by email and role
        $user = User::where('email', $request->input('email'))
                   ->where('role', 'finance_admin')
                   ->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            
            // Store user role in session
            $request->session()->put('user_role', 'finance_admin');
            
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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin-finance.login');
    }
}
