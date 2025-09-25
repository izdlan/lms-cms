<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class StaffController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'staff') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Get some basic statistics for staff dashboard
        $totalStudents = User::where('role', 'student')->count();
        $totalStaff = User::where('role', 'staff')->count();
        $recentStudents = User::where('role', 'student')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('staff.dashboard', compact('user', 'totalStudents', 'totalStaff', 'recentStudents'));
    }

    public function students()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'staff') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $students = User::where('role', 'student')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('staff.students', compact('user', 'students'));
    }

    public function profile()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'staff') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        return view('staff.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'staff') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('staff.profile')->with('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'staff') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        return view('staff.password-change', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'staff') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!password_verify($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('staff.password.change')->with('success', 'Password changed successfully.');
    }

    public function courses()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'staff') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Mock course data - in real app, this would come from database
        $courses = [
            [
                'id' => 1,
                'title' => 'Introduction to Programming',
                'code' => 'CS101',
                'description' => 'Basic programming concepts and practices',
                'instructor' => $user->name,
                'students_count' => 45,
                'status' => 'active'
            ],
            [
                'id' => 2,
                'title' => 'Web Development',
                'code' => 'CS201',
                'description' => 'Modern web development with HTML, CSS, and JavaScript',
                'instructor' => $user->name,
                'students_count' => 32,
                'status' => 'active'
            ],
            [
                'id' => 3,
                'title' => 'Database Management',
                'code' => 'CS301',
                'description' => 'Database design and SQL fundamentals',
                'instructor' => $user->name,
                'students_count' => 28,
                'status' => 'active'
            ]
        ];

        return view('staff.courses', compact('user', 'courses'));
    }

    public function announcements()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'staff') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Mock announcements data
        $announcements = [
            [
                'id' => 1,
                'title' => 'Midterm Exam Schedule',
                'content' => 'The midterm exams will be held from March 15-20. Please check your course schedules.',
                'created_at' => '2025-09-20',
                'status' => 'published'
            ],
            [
                'id' => 2,
                'title' => 'Assignment Submission Deadline',
                'content' => 'All assignments for CS101 are due by Friday, September 25th at 11:59 PM.',
                'created_at' => '2025-09-18',
                'status' => 'published'
            ],
            [
                'id' => 3,
                'title' => 'Course Materials Update',
                'content' => 'New course materials have been uploaded for CS201. Please download them from the course page.',
                'created_at' => '2025-09-15',
                'status' => 'draft'
            ]
        ];

        return view('staff.announcements', compact('user', 'announcements'));
    }

    public function contents()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'staff') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Mock course contents data
        $contents = [
            [
                'id' => 1,
                'course' => 'Introduction to Programming',
                'title' => 'Chapter 1: Variables and Data Types',
                'type' => 'PDF',
                'file_size' => '2.5 MB',
                'uploaded_at' => '2025-09-20',
                'downloads' => 45
            ],
            [
                'id' => 2,
                'course' => 'Web Development',
                'title' => 'HTML Basics Tutorial',
                'type' => 'Video',
                'file_size' => '125 MB',
                'uploaded_at' => '2025-09-18',
                'downloads' => 32
            ],
            [
                'id' => 3,
                'course' => 'Database Management',
                'title' => 'SQL Practice Exercises',
                'type' => 'PDF',
                'file_size' => '1.8 MB',
                'uploaded_at' => '2025-09-15',
                'downloads' => 28
            ]
        ];

        return view('staff.contents', compact('user', 'contents'));
    }

    public function assignments()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'staff') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Mock assignments data
        $assignments = [
            [
                'id' => 1,
                'course' => 'Introduction to Programming',
                'title' => 'Programming Exercise 1',
                'description' => 'Create a simple calculator program',
                'due_date' => '2025-09-30',
                'submissions' => 42,
                'total_students' => 45
            ],
            [
                'id' => 2,
                'course' => 'Web Development',
                'title' => 'HTML/CSS Project',
                'description' => 'Build a responsive website',
                'due_date' => '2025-10-05',
                'submissions' => 28,
                'total_students' => 32
            ]
        ];

        return view('staff.assignments', compact('user', 'assignments'));
    }
}
