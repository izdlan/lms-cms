<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the student dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ensure user is a student
        if ($user->role !== 'student') {
            return redirect()->route('login.selection')->with('error', 'Access denied. Please login as a student.');
        }

        return view('student.dashboard');
    }

    /**
     * Show student courses
     */
    public function courses()
    {
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            return redirect()->route('login.selection')->with('error', 'Access denied. Please login as a student.');
        }

        return view('student.courses');
    }

    /**
     * Show student assignments
     */
    public function assignments()
    {
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            return redirect()->route('login.selection')->with('error', 'Access denied. Please login as a student.');
        }

        return view('student.assignments');
    }

    /**
     * Show student profile
     */
    public function profile()
    {
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            return redirect()->route('login.selection')->with('error', 'Access denied. Please login as a student.');
        }

        return view('student.profile');
    }

    /**
     * Update student profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            return redirect()->route('login.selection')->with('error', 'Access denied. Please login as a student.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'previous_university' => 'nullable|string|max:255',
            'col_ref_no' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'previous_university' => $request->previous_university,
                'col_ref_no' => $request->col_ref_no,
            ]);

            return back()->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Show password reset form
     */
    public function showPasswordResetForm()
    {
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            return redirect()->route('login.selection')->with('error', 'Access denied. Please login as a student.');
        }

        return view('auth.student-password-reset');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            return redirect()->route('login.selection')->with('error', 'Access denied. Please login as a student.');
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
                'must_reset_password' => false,
            ]);

            return back()->with('success', 'Password updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update password. Please try again.');
        }
    }

    /**
     * Get student statistics for dashboard
     */
    public function getStats()
    {
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $stats = [
            'courses_count' => count($user->courses ?? []),
            'assignments_pending' => 0, // This would come from assignments table
            'assignments_submitted' => 0, // This would come from assignments table
            'assignments_graded' => 0, // This would come from assignments table
            'certificates_count' => 0, // This would come from certificates table
        ];

        return response()->json($stats);
    }
}


