<?php
/**
 * Controller: FinanceAdminController
 * Purpose: Finance admin operations for students, invoices (student bills),
 *          payments, receipts, and related reports. Restricts access to finance
 *          admins/admins via middleware.
 * Key Views: `finance-admin.dashboard`, `finance-admin.students`,
 *            `finance-admin.student-details`, `finance-admin.pending-payments`,
 *            `finance-admin.invoices`, `finance-admin.invoice-details`,
 *            `finance-admin.change-password`.
 * Notes: Creates and finalizes payments/receipts (transactional), filters/searches,
 *        and generates PDFs using DomPDF.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StudentEnrollment;
use App\Models\StudentBill as Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
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
            
            // Get students with pending payments (real data)
            $studentsWithPendingPayments = User::where('role', 'student')
                ->where('is_blocked', false)
                ->whereHas('studentBills', function($query) {
                    $query->where('status', 'pending');
                })
                ->with(['studentBills' => function($query) {
                    $query->where('status', 'pending');
                }])
                ->get()
                ->map(function($student) {
                    $pendingBills = $student->studentBills->where('status', 'pending');
                    $totalPendingAmount = $pendingBills->sum('amount');
                    $overdueBills = $pendingBills->where('due_date', '<', now()->toDateString());
                    $overdueDays = $overdueBills->isNotEmpty() ? $overdueBills->max(function($bill) {
                        return now()->diffInDays($bill->due_date);
                    }) : 0;
                    
                    $lastPayment = Payment::where('student_id', $student->id)
                        ->where('status', 'completed')
                        ->latest('paid_at')
                        ->first();
                    
                    return [
                        'student' => $student,
                        'pending_amount' => $totalPendingAmount,
                        'overdue_days' => $overdueDays,
                        'last_payment' => $lastPayment ? $lastPayment->paid_at : null,
                        'pending_invoices_count' => $pendingBills->count()
                    ];
                });

            $stats = [
                'total_students' => $totalStudents,
                'active_students' => $activeStudents,
                'blocked_students' => $blockedStudents,
                'pending_payments' => $studentsWithPendingPayments->count(),
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
                  ->orWhere('user_id', 'like', "%{$search}%");
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
        
        // Get real payment data from database
        $payments = Payment::where('student_id', $id)
            ->with(['invoice', 'receipt'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('finance-admin.payment-history', compact('student', 'payments'));
    }

    /**
     * Get students with pending payments
     */
    public function pendingPayments()
    {
        // Get students with pending bills (formerly invoices)
        $studentsWithPendingPayments = User::where('role', 'student')
            ->where('is_blocked', false)
            ->whereHas('studentBills', function($query) {
                $query->where('status', 'pending');
            })
            ->with(['studentBills' => function($query) {
                $query->where('status', 'pending');
            }])
            ->get()
            ->map(function($student) {
                $pendingBills = $student->studentBills->where('status', 'pending');
                $totalPendingAmount = $pendingBills->sum('amount');
                $overdueBills = $pendingBills->where('due_date', '<', now()->toDateString());
                $overdueDays = $overdueBills->isNotEmpty() ? $overdueBills->max(function($bill) {
                    return now()->diffInDays($bill->due_date);
                }) : 0;
                
                $lastPayment = Payment::where('student_id', $student->id)
                    ->where('status', 'completed')
                    ->latest('paid_at')
                    ->first();
                
                return [
                    'student' => $student,
                    'pending_amount' => $totalPendingAmount,
                    'overdue_days' => $overdueDays,
                    'last_payment' => $lastPayment ? $lastPayment->paid_at : null,
                    'pending_invoices_count' => $pendingBills->count()
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

    /**
     * Show and update Finance Admin profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('finance-admin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|max:2048'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['must_reset_password'] = false;
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $data['profile_picture'] = $path;
        }

        $user->update($data);
        return redirect()->route('finance-admin.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Show create invoice form
     */
    public function createInvoice($studentId)
    {
        $student = User::where('id', $studentId)->where('role', 'student')->firstOrFail();
        return view('finance-admin.create-invoice', compact('student'));
    }

    /**
     * Store new invoice
     */
    public function storeInvoice(Request $request, $studentId)
    {
        $request->validate([
            'bill_type' => 'required|string|max:255',
            'session' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date|after:today',
            'description' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000'
        ]);

        $student = User::where('id', $studentId)->where('role', 'student')->firstOrFail();

        $invoice = \App\Models\StudentBill::create([
            'bill_number' => \App\Models\StudentBill::generateBillNumber(),
            'user_id' => $studentId,
            'bill_type' => $request->bill_type,
            'session' => $request->session,
            'amount' => $request->amount,
            'bill_date' => now()->toDateString(),
            'due_date' => $request->due_date,
            'description' => $request->description,
            'metadata' => ['created_by' => Auth::id(), 'notes' => $request->notes],
            'status' => \App\Models\StudentBill::STATUS_UNPAID
        ]);

        return redirect()->route('finance-admin.student.show', $studentId)
            ->with('success', 'Invoice created successfully!');
    }

    /**
     * Show invoice details
     */
    public function showInvoice($invoiceId)
    {
        $invoice = Invoice::with(['user', 'payment'])
            ->findOrFail($invoiceId);
        
        return view('finance-admin.invoice-details', compact('invoice'));
    }

    /**
     * Mark payment as completed (for existing payments)
     */
    public function markPaymentCompleted(Request $request, $paymentId)
    {
        $request->validate([
            'payment_method' => 'required|string|in:online_banking,credit_card,debit_card,bank_transfer,cash,other',
            'transaction_id' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string|max:1000'
        ]);

        $payment = Payment::findOrFail($paymentId);
        
        DB::transaction(function() use ($payment, $request) {
            // Mark payment as completed
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'payment_notes' => $request->payment_notes
            ]);

            // Update invoice status if fully paid
            $invoice = $payment->invoice;
            if ($invoice->isFullyPaid()) {
                $invoice->update(['status' => 'paid']);
            }

            // Create receipt
            Receipt::create([
                'receipt_number' => Receipt::generateReceiptNumber(),
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'student_id' => $payment->student_id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'payment_date' => $payment->paid_at,
                'receipt_notes' => $payment->payment_notes
            ]);
        });

        return redirect()->back()->with('success', 'Payment marked as completed and receipt generated!');
    }

    /**
     * Mark invoice as paid (create payment and receipt)
     */
    public function markInvoiceAsPaid(Request $request, $invoiceId)
    {
        $request->validate([
            'payment_method' => 'required|string|in:online_banking,credit_card,debit_card,bank_transfer,cash,other',
            'transaction_id' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string|max:1000'
        ]);

        $invoice = Invoice::findOrFail($invoiceId);
        
        DB::transaction(function() use ($invoice, $request) {
            // Create payment record
            $payment = Payment::create([
                'payment_number' => Payment::generatePaymentNumber(),
                'student_bill_id' => $invoice->id,
                'student_id' => $invoice->user_id,
                'amount' => $invoice->amount,
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'status' => 'completed',
                'paid_at' => now(),
                'payment_notes' => $request->payment_notes
            ]);

            // Update invoice status
            $invoice->update(['status' => 'paid']);

            // Create receipt
            Receipt::create([
                'receipt_number' => Receipt::generateReceiptNumber(),
                'payment_id' => $payment->id,
                'student_bill_id' => $invoice->id,
                'student_id' => $invoice->user_id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'payment_date' => $payment->paid_at,
                'receipt_notes' => $payment->payment_notes
            ]);
        });

        return redirect()->back()->with('success', 'Invoice marked as paid and receipt generated!');
    }

    /**
     * Show all invoices
     */
    public function invoices(Request $request)
    {
        $query = Invoice::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('bill_type', 'like', "%{$search}%")
                  ->orWhereHas('student', function($studentQuery) use ($search) {
                      $studentQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%")
                                  ->orWhere('ic', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $search = $request->get('search', '');
        $statusFilter = $request->get('status', '');

        return view('finance-admin.invoices', compact('invoices', 'search', 'statusFilter'));
    }

    /**
     * Generate PDF for invoice
     */
    public function generateInvoicePdf($invoiceId)
    {
        $invoice = Invoice::with(['user'])
            ->findOrFail($invoiceId);
        
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * View PDF for invoice (in browser)
     */
    public function viewInvoicePdf($invoiceId)
    {
        $invoice = Invoice::with(['user'])
            ->findOrFail($invoiceId);
        
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }
}