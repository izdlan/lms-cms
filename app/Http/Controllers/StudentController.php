<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Program;
use App\Models\Subject;
use App\Models\Announcement;
use App\Models\CourseContent;
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

        $programs = Program::active()->where('code', 'EMBA')->get();
        
        // Get student's enrolled subjects with lecturer and class information
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->get();
        
        return view('student.dashboard', compact('user', 'programs', 'enrolledSubjects'));
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

        $programs = Program::active()->where('code', 'EMBA')->get();
        
        // Get student's enrolled subjects with lecturer and class information
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->get();
        
        return view('student.courses', compact('user', 'programs', 'enrolledSubjects'));
    }

    /**
     * Show course summary for a specific program
     */
    public function courseSummary($program)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        if ($user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Access denied. Please login as a student.');
        }

        $program = Program::where('code', strtoupper($program))->first();
        
        if (!$program) {
            return redirect()->route('student.courses')->with('error', 'Program not found.');
        }

        // Get student's enrolled subjects with lecturer and class information
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->get();

        // Get all EMBA subjects from database (compulsory for all EMBA students)
        $subjects = Subject::where('program_code', 'EMBA')
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(function($subject) {
                return [
                    'code' => $subject->code,
                    'name' => $subject->name,
                    'classification' => $subject->classification,
                    'credit' => $subject->credit_hours
                ];
            })
            ->toArray();

        return view('student.course-summary', compact('user', 'program', 'subjects', 'enrolledSubjects'));
    }

    /**
     * Show individual course class page
     */
    public function courseClass($subjectCode)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        if ($user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Access denied. Please login as a student.');
        }

        // Get student's enrolled subjects with lecturer and class information
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->get();

        // Find the specific subject enrollment
        $enrollment = $user->enrolledSubjects()
            ->where('subject_code', $subjectCode)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return redirect()->route('student.courses')->with('error', 'You are not enrolled in this course.');
        }

        // Get course details from database
        $subject = Subject::with(['clos', 'topics'])->where('code', $subjectCode)->first();
        
        if (!$subject) {
            return redirect()->route('student.courses')->with('error', 'Course not found.');
        }

        // Get announcements for this subject and class
        $announcements = Announcement::active()
            ->forSubject($subjectCode)
            ->forClass($enrollment->class_code)
            ->orderBy('published_at', 'desc')
            ->orderBy('is_important', 'desc')
            ->get();

        // Get course content for this subject and class
        $courseContents = CourseContent::active()
            ->forSubject($subjectCode)
            ->forClass($enrollment->class_code)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get course materials for this subject and class
        $courseMaterials = \App\Models\CourseMaterial::where('subject_code', $subjectCode)
            ->where('class_code', $enrollment->class_code)
            ->where('is_active', true)
            ->where('is_public', true)
            ->orderBy('published_at', 'desc')
            ->get();

        // Format data for the view
        $subjectDetails = [
            'name' => $subject->name,
            'description' => $subject->description,
            'assessment' => $subject->assessment_methods,
            'duration' => $subject->duration,
            'clos' => $subject->clos->map(function($clo) {
                return [
                    'clo' => $clo->clo_code,
                    'description' => $clo->description,
                    'mqf' => $clo->mqf_alignment
                ];
            })->toArray(),
            'topics' => $subject->topics->map(function($topic) {
                return [
                    'clo' => $topic->clo_code,
                    'topic' => $topic->topic_title
                ];
            })->toArray()
        ];


        return view('student.course-class', compact('user', 'enrolledSubjects', 'enrollment', 'subjectDetails', 'announcements', 'courseContents', 'courseMaterials'));
    }

    /**
     * Download course material
     */
    public function downloadMaterial($id)
    {
        $user = Auth::guard('student')->user();
        if (!$user || $user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $material = \App\Models\CourseMaterial::findOrFail($id);
        
        // Check if student is enrolled in this subject and class
        $enrollment = $user->enrolledSubjects()
            ->where('subject_code', $material->subject_code)
            ->where('class_code', $material->class_code)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

        if ($material->external_url) {
            return redirect($material->external_url);
        }

        if (!$material->file_path || !file_exists(storage_path('app/public/' . $material->file_path))) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Increment download count
        $material->increment('download_count');

        return response()->download(storage_path('app/public/' . $material->file_path), $material->file_name);
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

        // Get student's enrolled subjects with lecturer and class information
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->get();

        return view('student.assignments', compact('user', 'enrolledSubjects'));
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

        // Get student's enrolled subjects with lecturer and class information
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->get();

        return view('student.profile', compact('user', 'enrolledSubjects'));
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

        // Get student's enrolled subjects with lecturer and class information
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->get();

        return view('auth.student-password-reset', compact('user', 'enrolledSubjects'));
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
        
        // Get student's enrolled subjects with lecturer and class information
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->get();
        
        return view('student.bills', compact('user', 'enrolledSubjects'));
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
        
        // Get student's enrolled subjects with lecturer and class information
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->get();
        
        return view('student.payment', compact('user', 'enrolledSubjects'));
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
        
        // Get student's enrolled subjects with lecturer and class information
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->get();
        
        return view('student.receipt', compact('user', 'enrolledSubjects'));
    }
}
