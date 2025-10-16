<?php
/**
 * Controller: StudentController
 * Purpose: Handles student-facing pages and actions (dashboard, courses, assignments,
 *          payments, profile, and exam results). Enforces `auth:student` guard and
 *          role checks. Aggregates model data and renders Blade views under `student/*`.
 * Key Views: `student.dashboard`, `student.courses`, `student.course-summary`,
 *            `student.course-class`, `student.assignments`, `student.bills`,
 *            `student.payment`, `student.receipt`, `student.exam-results`.
 * Important Models: Program, Subject, Announcement, CourseContent, Assignment,
 *                   AssignmentSubmission, StudentBill, Payment, ExamResult,
 *                   StudentEnrollment.
 * Notes: Uses helper methods like `calculateOverallGpa` and guard rails for enrolled
 *        subject/class access and secure file responses (PDFs).
 */

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Program;
use App\Models\Subject;
use App\Models\Announcement;
use App\Models\CourseContent;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\StudentBill;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\ExamResult;
use App\Models\StudentEnrollment;
use App\Services\BillplzService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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
        
        // If student is blocked, continue showing dashboard but UI will be blurred by layout

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

        // Get assignments for this subject and class
        $assignments = Assignment::where('subject_code', $subjectCode)
            ->where('class_code', $enrollment->class_code)
            ->where('status', 'published')
            ->with(['subject', 'classSchedule', 'lecturer'])
            ->orderBy('available_from', 'asc')
            ->get();

        // Get student's submissions for these assignments
        $assignmentIds = $assignments->pluck('id');
        $submissions = AssignmentSubmission::where('user_id', $user->id)
            ->whereIn('assignment_id', $assignmentIds)
            ->with('assignment')
            ->get()
            ->keyBy('assignment_id');

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


        return view('student.course-class', compact('user', 'enrolledSubjects', 'enrollment', 'subjectDetails', 'announcements', 'courseContents', 'courseMaterials', 'assignments', 'submissions'));
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

        // Get assignments for enrolled subjects
        $subjectCodes = $enrolledSubjects->pluck('subject_code');
        $classCodes = $enrolledSubjects->pluck('class_code');

        $assignments = Assignment::whereIn('subject_code', $subjectCodes)
            ->whereIn('class_code', $classCodes)
            ->where('status', 'published')
            ->with(['subject', 'classSchedule', 'lecturer'])
            ->orderBy('available_from', 'asc')
            ->get();

        // Get student's submissions
        $submissions = AssignmentSubmission::where('user_id', $user->id)
            ->with('assignment')
            ->get()
            ->keyBy('assignment_id');

        return view('student.assignments', compact('user', 'enrolledSubjects', 'assignments', 'submissions'));
    }

    public function submitAssignment(Request $request, $assignmentId)
    {
        $user = Auth::guard('student')->user();
        if (!$user || $user->role !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $assignment = Assignment::findOrFail($assignmentId);

        // Check if student is enrolled in this assignment's class
        $enrollment = $user->enrolledSubjects()
            ->where('subject_code', $assignment->subject_code)
            ->where('class_code', $assignment->class_code)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'You are not enrolled in this course'], 403);
        }

        // Check if assignment is available for submission
        if (!$assignment->isAvailableForSubmission()) {
            return response()->json(['error' => 'Assignment is not available for submission'], 403);
        }

        $request->validate([
            'submission_text' => 'nullable|string',
            'attachments' => 'required|array|min:1',
            'attachments.*' => 'file|mimes:pdf|max:10240' // PDF only, 10MB max per file
        ]);

        // Check if already submitted
        $existingSubmission = AssignmentSubmission::where('assignment_id', $assignmentId)
            ->where('user_id', $user->id)
            ->first();

        if ($existingSubmission) {
            if ($existingSubmission->status === 'graded') {
                return response()->json(['error' => 'Assignment has been graded and cannot be resubmitted'], 400);
            }
            return response()->json(['error' => 'Assignment already submitted'], 400);
        }

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('assignment-submissions', 'public');
                $attachments[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }
        }

        // Check if submission is late
        $isLate = now() > $assignment->due_date;

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignmentId,
            'user_id' => $user->id,
            'submission_text' => $request->submission_text,
            'attachments' => $attachments,
            'is_late' => $isLate,
            'submitted_at' => now(),
            'status' => 'submitted'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assignment submitted successfully!',
            'submission' => $submission
        ]);
    }

    public function getAssignmentDetails($assignmentId)
    {
        $user = Auth::guard('student')->user();
        if (!$user || $user->role !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $assignment = Assignment::with(['subject', 'classSchedule', 'lecturer'])
            ->findOrFail($assignmentId);

        // Check if student is enrolled
        $enrollment = $user->enrolledSubjects()
            ->where('subject_code', $assignment->subject_code)
            ->where('class_code', $assignment->class_code)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'You are not enrolled in this course'], 403);
        }

        // Get student's submission if exists
        $submission = AssignmentSubmission::where('assignment_id', $assignmentId)
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'success' => true,
            'assignment' => $assignment,
            'submission' => $submission
        ]);
    }

    public function downloadAssignmentFile($assignmentId, $fileIndex)
    {
        $user = Auth::guard('student')->user();
        if (!$user || $user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Unauthorized');
        }

        $assignment = Assignment::findOrFail($assignmentId);
        
        // Check if student is enrolled in this assignment's class
        $enrollment = $user->enrolledSubjects()
            ->where('subject_code', $assignment->subject_code)
            ->where('class_code', $assignment->class_code)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

        if (!$assignment->attachments || !isset($assignment->attachments[$fileIndex])) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $file = $assignment->attachments[$fileIndex];
        $filePath = storage_path('app/public/' . $file['file_path']);

        if (!file_exists($filePath)) {
            Log::error("Assignment file not found: {$filePath}");
            return redirect()->back()->with('error', 'File not found on server: ' . $filePath);
        }

        try {
            return response()->download($filePath, $file['original_name']);
        } catch (\Exception $e) {
            Log::error("Assignment download error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Download failed: ' . $e->getMessage());
        }
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
        $user->profile_picture = $path;
        $user->save();

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
        
        // Get student's bills
        $bills = StudentBill::where('user_id', $user->id)
            ->orderBy('bill_date', 'desc')
            ->paginate(10);
        
        // Get student's enrolled subjects with lecturer and class information
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->get();
        
        return view('student.bills', compact('user', 'enrolledSubjects', 'bills'));
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
        
        $billId = $request->get('bill_id');
        $bill = null;
        
        if ($billId) {
            $bill = StudentBill::where('id', $billId)
                ->where('user_id', $user->id)
                ->where('status', StudentBill::STATUS_PENDING)
                ->firstOrFail();
        }
        
        // Get student's enrolled subjects with lecturer and class information
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->get();
        
        return view('student.payment', compact('user', 'enrolledSubjects', 'bill'));
    }

    /**
     * Process bill payment with Billplz
     */
    public function processPayment(Request $request)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login to access this page.'], 401);
        }

        $request->validate([
            'bill_id' => 'required|exists:student_bills,id',
        ]);

        try {
            $bill = StudentBill::where('id', $request->bill_id)
                ->where('user_id', $user->id)
                ->where('status', StudentBill::STATUS_PENDING)
                ->firstOrFail();

            // Check if there's already a pending payment for this bill
            $existingPayment = Payment::where('reference_id', $bill->id)
                ->where('reference_type', 'bill')
                ->where('user_id', $user->id)
                ->where('status', Payment::STATUS_PENDING)
                ->first();

            if ($existingPayment && !$existingPayment->isExpired()) {
                return response()->json([
                    'success' => true,
                    'payment_url' => $existingPayment->payment_url,
                    'message' => 'Existing payment found'
                ]);
            }

            // Create Billplz payment using the new service method
            $billplzService = new BillplzService();
            $result = $billplzService->createBillPayment($user, $bill);

            if ($result['success']) {
                $billplzData = $result['data'];
                
                // Create payment record
                $payment = Payment::create([
                    'billplz_id' => $billplzData['id'],
                    'billplz_collection_id' => $billplzData['collection_id'],
                    'student_id' => $user->id,
                    'student_bill_id' => $bill->id,
                    'amount' => $bill->amount,
                    'payment_method' => 'online_banking',
                    'description' => "Payment for {$bill->bill_type} - {$bill->bill_number}",
                    'payment_details' => $billplzData,
                    'expires_at' => now()->addMinutes(config('billplz.timeout', 30)),
                ]);

                return response()->json([
                    'success' => true,
                    'payment_url' => $billplzData['url'],
                    'payment_id' => $payment->id,
                    'message' => 'Payment created successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Bill payment processing failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'bill_id' => $request->bill_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment. Please try again.'
            ], 500);
        }
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
        
        $paymentId = $request->get('payment_id');
        $payment = null;
        $bill = null;
        
        if ($paymentId) {
            $payment = Payment::where('id', $paymentId)
                ->where('student_id', $user->id)
                ->whereIn('status', [Payment::STATUS_PAID, Payment::STATUS_COMPLETED])
                ->first();
                
            if ($payment && $payment->student_bill_id) {
                $bill = StudentBill::find($payment->student_bill_id);
            }
        }
        
        // Get student's enrolled subjects with lecturer and class information
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->get();
        
        return view('student.receipt', compact('user', 'enrolledSubjects', 'payment', 'bill'));
    }

    public function getSubmission($assignmentId)
    {
        $user = Auth::guard('student')->user();
        if (!$user || $user->role !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $assignment = Assignment::findOrFail($assignmentId);

        // Check if student is enrolled
        $enrollment = $user->enrolledSubjects()
            ->where('subject_code', $assignment->subject_code)
            ->where('class_code', $assignment->class_code)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'You are not enrolled in this course'], 403);
        }

        // Get student's submission
        $submission = AssignmentSubmission::where('assignment_id', $assignmentId)
            ->where('user_id', $user->id)
            ->first();

        if (!$submission) {
            return response()->json(['error' => 'Submission not found'], 404);
        }

        return response()->json([
            'success' => true,
            'submission' => $submission
        ]);
    }

    public function viewSubmissionFile($submissionId, $fileIndex)
    {
        $user = Auth::guard('student')->user();
        if (!$user || $user->role !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $submission = AssignmentSubmission::where('id', $submissionId)
                ->where('user_id', $user->id)
                ->first();

            if (!$submission) {
                return response()->json(['error' => 'Submission not found'], 404);
            }

            $attachments = $submission->attachments;
            if (!$attachments || !isset($attachments[$fileIndex])) {
                return response()->json(['error' => 'File not found'], 404);
            }

            $file = $attachments[$fileIndex];
            $filePath = storage_path('app/public/' . $file['file_path']);

            if (!file_exists($filePath)) {
                return response()->json(['error' => 'File does not exist'], 404);
            }

            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file['original_name'] . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Error viewing submission file: ' . $e->getMessage());
            return response()->json(['error' => 'Error viewing file'], 500);
        }
    }

    public function downloadSubmissionFile($submissionId, $fileIndex)
    {
        $user = Auth::guard('student')->user();
        if (!$user || $user->role !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $submission = AssignmentSubmission::where('id', $submissionId)
                ->where('user_id', $user->id)
                ->first();

            if (!$submission) {
                return response()->json(['error' => 'Submission not found'], 404);
            }

            $attachments = $submission->attachments;
            if (!$attachments || !isset($attachments[$fileIndex])) {
                return response()->json(['error' => 'File not found'], 404);
            }

            $file = $attachments[$fileIndex];
            $filePath = storage_path('app/public/' . $file['file_path']);

            if (!file_exists($filePath)) {
                return response()->json(['error' => 'File does not exist'], 404);
            }

            return response()->download($filePath, $file['original_name']);

        } catch (\Exception $e) {
            Log::error('Error downloading submission file: ' . $e->getMessage());
            return response()->json(['error' => 'Error downloading file'], 500);
        }
    }

    /**
     * Show exam results for the student
     */
    public function examResults(Request $request)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user || $user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Get current academic year
        $currentYear = $request->get('year', '2025');

        // Get student's enrolled subjects (all 12 EMBA subjects)
        $enrolledSubjects = StudentEnrollment::where('user_id', $user->id)
            ->where('status', 'enrolled')
            ->with(['subject', 'lecturer'])
            ->get();

        // Get exam results for the current academic year
        $examResults = ExamResult::where('user_id', $user->id)
            ->where('academic_year', $currentYear)
            ->with(['subject', 'lecturer'])
            ->get()
            ->keyBy('subject_code');

        // Get all available academic years for filter
        $availableYears = ExamResult::where('user_id', $user->id)
            ->distinct()
            ->pluck('academic_year')
            ->sort()
            ->values();

        // Calculate overall GPA
        $overallGpa = $this->calculateOverallGpa($examResults);

        // Get enrolled subjects for navbar (same as dashboard)
        $enrolledSubjects = $user->enrolledSubjects()
            ->where('status', 'enrolled')
            ->with(['subject', 'lecturer'])
            ->get();

        return view('student.exam-results', compact(
            'user',
            'enrolledSubjects',
            'examResults',
            'currentYear',
            'availableYears',
            'overallGpa'
        ));
    }

    /**
     * Get current semester based on month
     */
    private function getCurrentSemester()
    {
        $month = date('n');
        if ($month >= 1 && $month <= 4) {
            return 'Semester 1';
        } elseif ($month >= 5 && $month <= 8) {
            return 'Semester 2';
        } else {
            return 'Semester 3';
        }
    }

    /**
     * Calculate overall GPA for the semester
     */
    private function calculateOverallGpa($examResults)
    {
        if ($examResults->isEmpty()) {
            return 0.00;
        }

        $totalGpa = 0;
        $totalCredits = 0;

        foreach ($examResults as $result) {
            if ($result->gpa && $result->subject) {
                $credits = $result->subject->credit_hours ?? 1;
                $totalGpa += $result->gpa * $credits;
                $totalCredits += $credits;
            }
        }

        return $totalCredits > 0 ? round($totalGpa / $totalCredits, 2) : 0.00;
    }

    /**
     * Show receipt details
     */
    public function showReceipt($id)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $receipt = Receipt::where('id', $id)
            ->where('student_id', $user->id)
            ->with(['payment', 'studentBill'])
            ->firstOrFail();

        return view('student.receipt-details', compact('receipt', 'user'));
    }

    /**
     * Generate receipt PDF
     */
    public function generateReceiptPdf($id)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $receipt = Receipt::where('id', $id)
            ->where('student_id', $user->id)
            ->with(['payment', 'studentBill'])
            ->firstOrFail();

        $pdf = \PDF::loadView('receipts.pdf', compact('receipt', 'user'));
        
        return $pdf->download('receipt-' . $receipt->receipt_number . '.pdf');
    }

    /**
     * View receipt PDF
     */
    public function viewReceiptPdf($id)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $receipt = Receipt::where('id', $id)
            ->where('student_id', $user->id)
            ->with(['payment', 'studentBill'])
            ->firstOrFail();

        $pdf = \PDF::loadView('receipts.pdf', compact('receipt', 'user'));
        
        return $pdf->stream('receipt-' . $receipt->receipt_number . '.pdf');
    }

    /**
     * Generate invoice PDF for student bill
     */
    public function generateInvoicePdf($id)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $bill = StudentBill::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $invoice = null; // Set invoice to null since we're using StudentBill
        $pdf = \PDF::loadView('invoices.pdf', compact('bill', 'user', 'invoice'));
        
        return $pdf->download('invoice-' . $bill->bill_number . '.pdf');
    }

    /**
     * View invoice PDF for student bill
     */
    public function viewInvoicePdf($id)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $bill = StudentBill::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $invoice = null; // Set invoice to null since we're using StudentBill
        $pdf = \PDF::loadView('invoices.pdf', compact('bill', 'user', 'invoice'));
        
        return $pdf->stream('invoice-' . $bill->bill_number . '.pdf');
    }
}
