<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'ic' => 'required|string',
            'password' => 'required|string',
        ]);

        $student = Student::where('ic_number', $request->ic)
                         ->where('status', 'active')
                         ->first();

        if ($student && Hash::check($request->password, $student->password)) {
            Auth::guard('student')->login($student, $request->filled('remember'));
            
            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'ic' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
