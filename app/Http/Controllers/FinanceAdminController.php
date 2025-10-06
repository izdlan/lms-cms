<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\DB;

class FinanceAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isFinanceAdmin() && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access.');
            }
            return $next($request);
        });
    }

    /**
     * Show finance admin dashboard
     */
    public function dashboard()
    {
        try {
            $totalStudents = User::where('role', 'student')->count();
            $blockedStudents = User::where('role', 'student')->where('is_blocked', true)->count();
            $activeStudents = $totalStudents - $blockedStudents;
            
            // Get students with pending payments (mock data for now)
            $studentsWithPendingPayments = User::where('role', 'student')
                ->where('is_blocked', false)
                ->take(10)
                ->get();

            $stats = [
                'total_students' => $totalStudents,
                'active_students' => $activeStudents,
                'blocked_students' => $blockedStudents,
                'students_with_pending_payments' => $studentsWithPendingPayments->count(),
            ];

            return view('finance-admin.dashboard', compact('stats', 'studentsWithPendingPayments'));
        } catch (\Exception $e) {
            \Log::error('Finance Admin Dashboard Error: ' . $e->getMessage());
            return response()->view('errors.500', ['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show all students
     */
    public function students(Request $request)
    {
        $query = User::where('role', 'student');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('ic', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'blocked') {
                $query->where('is_blocked', true);
            } elseif ($request->status === 'active') {
                $query->where('is_blocked', false);
            }
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('finance-admin.students', compact('students'));
    }

    /**
     * Show student details
     */
    public function showStudent($id)
    {
        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        
        // Get student's enrollment history
        $enrollments = StudentEnrollment::where('user_id', $id)
            ->with(['subject', 'lecturer'])
            ->orderBy('enrollment_date', 'desc')
            ->get();

        return view('finance-admin.student-details', compact('student', 'enrollments'));
    }

    /**
     * Block a student
     */
    public function blockStudent(Request $request, $id)
    {
        $request->validate([
            'block_reason' => 'required|string|max:500'
        ]);

        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        
        $student->update([
            'is_blocked' => true,
            'blocked_at' => now(),
            'block_reason' => $request->block_reason
        ]);

        return redirect()->back()->with('success', 'Student has been blocked successfully.');
    }

    /**
     * Unblock a student
     */
    public function unblockStudent($id)
    {
        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        
        $student->update([
            'is_blocked' => false,
            'blocked_at' => null,
            'block_reason' => null
        ]);

        return redirect()->back()->with('success', 'Student has been unblocked successfully.');
    }

    /**
     * Show payment history for a student
     */
    public function paymentHistory($id)
    {
        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        
        // Mock payment data - in real implementation, this would come from a payments table
        $payments = [
            [
                'bill_number' => '2022495772013',
                'bill_date' => '2025-09-12',
                'session' => '20254',
                'bill_type' => 'Tuition Fee',
                'amount' => 590.00,
                'status' => 'Pending',
                'due_date' => '2025-10-12'
            ],
            [
                'bill_number' => '2022495772012',
                'bill_date' => '2025-05-10',
                'session' => '20252',
                'bill_type' => 'EET Fee',
                'amount' => 30.00,
                'status' => 'Paid',
                'paid_date' => '2025-05-10'
            ],
            [
                'bill_number' => '2022495772011',
                'bill_date' => '2025-03-19',
                'session' => '20252',
                'bill_type' => 'Tuition Fee',
                'amount' => 590.00,
                'status' => 'Paid',
                'paid_date' => '2025-03-19'
            ]
        ];

        return view('finance-admin.payment-history', compact('student', 'payments'));
    }

    /**
     * Get students with pending payments
     */
    public function pendingPayments()
    {
        // Mock data - in real implementation, this would query actual payment records
        $studentsWithPendingPayments = User::where('role', 'student')
            ->where('is_blocked', false)
            ->get()
            ->map(function($student) {
                return [
                    'student' => $student,
                    'pending_amount' => rand(100, 2000), // Mock pending amount
                    'overdue_days' => rand(1, 30), // Mock overdue days
                    'last_payment' => now()->subDays(rand(10, 60)) // Mock last payment date
                ];
            });

        return view('finance-admin.pending-payments', compact('studentsWithPendingPayments'));
    }

    /**
     * Show change password form
     */
    public function changePassword()
    {
        return view('finance-admin.change-password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
            'must_reset_password' => false,
        ]);

        return redirect()->route('finance-admin.dashboard')->with('success', 'Password updated successfully!');
    }
}