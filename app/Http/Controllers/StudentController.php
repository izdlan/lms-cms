<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    /**
     * Show the student dashboard
     */
    public function dashboard()
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        // Ensure user is a student
        if ($user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Access denied. Please login as a student.');
        }

        return view('student.dashboard');
    }

    /**
     * Show student courses
     */
    public function courses()
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        if ($user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Access denied. Please login as a student.');
        }

        return view('student.courses');
    }

    /**
     * Show student assignments
     */
    public function assignments()
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        if ($user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Access denied. Please login as a student.');
        }

        return view('student.assignments');
    }

    /**
     * Show student profile
     */
    public function profile()
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        if ($user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Access denied. Please login as a student.');
        }

        return view('student.profile');
    }

    /**
     * Update student profile
     */
    public function updateProfile(Request $request)
    {
        Log::info('Profile update request received', $request->all());
        
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        if ($user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Access denied. Please login as a student.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            Log::error('Profile validation failed', $validator->errors()->toArray());
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return back()->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Show password reset form
     */
    public function showPasswordResetForm()
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        if ($user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Access denied. Please login as a student.');
        }

        return view('auth.student-password-reset');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        if ($user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Access denied. Please login as a student.');
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
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Please login to access this page.'], 401);
        }
        
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

    /**
     * Upload profile picture
     */
    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Delete old profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new profile picture
        $path = $request->file('profile_picture')->store('profile-pictures', 'public');
        
        // Update user profile picture
        $user->update(['profile_picture' => $path]);

        return redirect()->route('student.profile')->with('success', 'Profile picture uploaded successfully!');
    }

    /**
     * Delete profile picture
     */
    public function deleteProfilePicture()
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->update(['profile_picture' => null]);
        }

        return redirect()->route('student.profile')->with('success', 'Profile picture deleted successfully!');
    }

    /**
     * Show student bills page
     */
    public function bills()
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        return view('student.bills');
    }

    /**
     * Show payment page for unpaid bills
     */
    public function payment(Request $request)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        return view('student.payment');
    }

    /**
     * Show receipt page for paid bills
     */
    public function receipt(Request $request)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        return view('student.receipt');
    }
}


