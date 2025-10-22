<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\CertificateController;

// Maintenance route - direct access
Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');

// Main routes - show home page
Route::get('/', function () {
    $homePageContents = \App\Models\HomePageContent::active()->ordered()->get();
    $announcements = \App\Models\PublicAnnouncement::active()->published()->featured()->latest()->take(3)->get();
    $galleryAnnouncements = \App\Models\PublicAnnouncement::active()->published()->whereNotNull('image_url')->latest()->get();
    return view('home', compact('homePageContents', 'announcements', 'galleryAnnouncements'));
});

Route::get('/classes', function () {
    return view('classes');
})->name('classes');

// Professional Bootstrap Demo
Route::get('/professional-demo', function () {
    return view('professional-demo');
})->name('professional.demo');

// Announcements routes
Route::get('/announcements', function() {
    $announcements = \App\Models\PublicAnnouncement::active()->published()->latest()->paginate(10);
    $categories = \App\Models\PublicAnnouncement::active()->published()->distinct()->pluck('category');
    return view('announcements.index', compact('announcements', 'categories'));
})->name('announcements.index');
Route::get('/announcements/{id}', function($id) {
    $announcement = \App\Models\PublicAnnouncement::active()->published()->findOrFail($id);
    
    // Get related announcements (same category, excluding current announcement)
    $relatedAnnouncements = \App\Models\PublicAnnouncement::active()
        ->published()
        ->where('category', $announcement->category)
        ->where('id', '!=', $announcement->id)
        ->latest()
        ->take(2)
        ->get();
    
    // If not enough related announcements in same category, get recent ones
    if ($relatedAnnouncements->count() < 2) {
        $additionalAnnouncements = \App\Models\PublicAnnouncement::active()
            ->published()
            ->where('id', '!=', $announcement->id)
            ->whereNotIn('id', $relatedAnnouncements->pluck('id'))
            ->latest()
            ->take(2 - $relatedAnnouncements->count())
            ->get();
        
        $relatedAnnouncements = $relatedAnnouncements->merge($additionalAnnouncements);
    }
    
    return view('announcements.show', compact('announcement', 'relatedAnnouncements'));
})->name('announcements.show');

// Bootstrap test route
Route::get('/test-bootstrap', function () {
    return view('test-bootstrap');
})->name('test.bootstrap');

// Student & Lecturer login system (main login route)
Route::get('/login', [App\Http\Controllers\Auth\StudentLecturerAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\StudentLecturerAuthController::class, 'login']);
Route::post('/student-lecturer-logout', [App\Http\Controllers\Auth\StudentLecturerAuthController::class, 'logout'])->name('student-lecturer.logout');

// Admin & Finance Admin login system
Route::get('/admin-login', [App\Http\Controllers\Auth\AdminFinanceAuthController::class, 'showLoginForm'])->name('admin-finance.login');
Route::post('/admin-login', [App\Http\Controllers\Auth\AdminFinanceAuthController::class, 'login']);
Route::post('/admin-finance-logout', [App\Http\Controllers\Auth\AdminFinanceAuthController::class, 'logout'])->name('admin-finance.logout');

// Legacy unified login system (redirects to student-lecturer login)
Route::get('/unified-login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('unified.login');
Route::post('/unified-login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Student authentication routes
Route::prefix('student')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\StudentAuthController::class, 'logout'])->name('student.logout');
    Route::get('/password/reset', [App\Http\Controllers\Auth\StudentAuthController::class, 'showPasswordResetForm'])->name('student.password.reset');
    Route::post('/password/email', [App\Http\Controllers\Auth\StudentAuthController::class, 'sendPasswordResetLink'])->name('student.password.email');
    Route::get('/password/reset/{token}', function ($token) {
        return view('auth.student-password-reset', ['token' => $token]);
    })->name('student.password.reset.token');
    Route::post('/password/reset', [App\Http\Controllers\Auth\StudentAuthController::class, 'resetPassword'])->name('student.password.update');
    
    // Protected password change routes
    Route::middleware('auth:student')->group(function () {
        Route::get('/password/change', [App\Http\Controllers\Auth\StudentAuthController::class, 'showPasswordChangeForm'])->name('student.password.change');
        Route::post('/password/change', [App\Http\Controllers\Auth\StudentAuthController::class, 'changePassword'])->name('student.password.change.post');
    });
    
    // Protected student routes
    Route::middleware(['auth:student', 'check.blocked.student'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/courses', [App\Http\Controllers\StudentController::class, 'courses'])->name('student.courses');
        Route::get('/course/{program}', [App\Http\Controllers\StudentController::class, 'courseSummary'])->name('student.course.summary');
        Route::get('/course/{subjectCode}/class', [App\Http\Controllers\StudentController::class, 'courseClass'])->name('student.course.class');
        Route::get('/assignments', [App\Http\Controllers\StudentController::class, 'assignments'])->name('student.assignments');
        Route::post('/assignments/{id}/submit', [App\Http\Controllers\StudentController::class, 'submitAssignment'])->name('student.assignments.submit');
        Route::get('/assignments/{id}/details', [App\Http\Controllers\StudentController::class, 'getAssignmentDetails'])->name('student.assignments.details');
        Route::get('/assignments/{id}/submission', [App\Http\Controllers\StudentController::class, 'getSubmission'])->name('student.assignments.submission');
        Route::get('/assignments/submissions/view/{submissionId}/{fileIndex}', [App\Http\Controllers\StudentController::class, 'viewSubmissionFile'])->name('student.assignments.submissions.view');
        Route::get('/assignments/submissions/download/{submissionId}/{fileIndex}', [App\Http\Controllers\StudentController::class, 'downloadSubmissionFile'])->name('student.assignments.submissions.download');
        Route::get('/profile', [App\Http\Controllers\StudentController::class, 'profile'])->name('student.profile');
        Route::post('/profile/picture', [App\Http\Controllers\StudentController::class, 'uploadProfilePicture'])->name('student.profile.picture');
        Route::delete('/profile/picture', [App\Http\Controllers\StudentController::class, 'deleteProfilePicture'])->name('student.profile.picture.delete');
        Route::post('/password/update', [App\Http\Controllers\StudentController::class, 'updatePassword'])->name('student.password.update');
        Route::get('/stats', [App\Http\Controllers\StudentController::class, 'getStats'])->name('student.stats');
        Route::get('/bills', [App\Http\Controllers\StudentController::class, 'bills'])->name('student.bills');
        Route::get('/payment', [App\Http\Controllers\StudentController::class, 'payment'])->name('student.payment');
        Route::get('/exam-results', [App\Http\Controllers\StudentController::class, 'examResults'])->name('student.exam-results');
        Route::post('/payment/process', [App\Http\Controllers\StudentController::class, 'processPayment'])->name('student.payment.process');
        Route::get('/receipt', [App\Http\Controllers\StudentController::class, 'receipt'])->name('student.receipt');
        Route::get('/materials/download/{id}', [App\Http\Controllers\StudentController::class, 'downloadMaterial'])->name('student.materials.download');
        Route::get('/assignments/download/{assignmentId}/{fileIndex}', [App\Http\Controllers\StudentController::class, 'downloadAssignmentFile'])->name('student.assignments.download');
        
        // Payment routes
        Route::get('/payments', [App\Http\Controllers\PaymentController::class, 'index'])->name('student.payments');
        Route::post('/payments/course', [App\Http\Controllers\PaymentController::class, 'createCoursePayment'])->name('student.payments.course');
        Route::post('/payments/general', [App\Http\Controllers\PaymentController::class, 'createGeneralPayment'])->name('student.payments.general');
        Route::get('/payments/{paymentId}/status', [App\Http\Controllers\PaymentController::class, 'getPaymentStatus'])->name('student.payments.status');
        Route::get('/payment/success', [App\Http\Controllers\PaymentController::class, 'paymentSuccess'])->name('student.payment.success');
        Route::get('/payment/failed', [App\Http\Controllers\PaymentController::class, 'paymentFailed'])->name('student.payment.failed');
        Route::get('/payment/pending', [App\Http\Controllers\PaymentController::class, 'paymentPending'])->name('student.payment.pending');
        
        // Invoice PDF routes
        Route::get('/invoices/{id}/pdf', [App\Http\Controllers\StudentController::class, 'generateInvoicePdf'])->name('student.invoice.pdf');
        Route::get('/invoices/{id}/view-pdf', [App\Http\Controllers\StudentController::class, 'viewInvoicePdf'])->name('student.invoice.view-pdf');
        
        // Student bill invoice routes
        Route::get('/bills/{id}/view', [App\Http\Controllers\StudentController::class, 'viewInvoicePdf'])->name('student.invoice.view');
        Route::get('/bills/{id}/download', [App\Http\Controllers\StudentController::class, 'generateInvoicePdf'])->name('student.invoice.download');
        
        // Receipt routes
        Route::get('/receipts/{id}', [App\Http\Controllers\StudentController::class, 'showReceipt'])->name('student.receipt.show');
        Route::get('/receipts/{id}/pdf', [App\Http\Controllers\StudentController::class, 'generateReceiptPdf'])->name('student.receipt.pdf');
        Route::get('/receipts/{id}/view-pdf', [App\Http\Controllers\StudentController::class, 'viewReceiptPdf'])->name('student.receipt.view-pdf');
    });
});

// Staff authentication routes
Route::prefix('staff')->group(function () {
    // Protected staff routes
    Route::middleware('auth:staff')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\StaffController::class, 'dashboard'])->name('staff.dashboard');
        Route::get('/courses', [App\Http\Controllers\StaffController::class, 'courses'])->name('staff.courses');
        Route::get('/announcements', [App\Http\Controllers\StaffController::class, 'announcements'])->name('staff.announcements');
        Route::post('/announcements/create', [App\Http\Controllers\StaffController::class, 'createAnnouncement'])->name('staff.announcements.create');
        Route::get('/announcements/get', [App\Http\Controllers\StaffController::class, 'getAnnouncements'])->name('staff.announcements.get');
        Route::get('/contents', [App\Http\Controllers\StaffController::class, 'contents'])->name('staff.contents');
        Route::get('/assignments', [App\Http\Controllers\StaffController::class, 'assignments'])->name('staff.assignments');
        Route::post('/assignments/create', [App\Http\Controllers\StaffController::class, 'createAssignment'])->name('staff.assignments.create');
        Route::post('/assignments/{id}/publish', [App\Http\Controllers\StaffController::class, 'publishAssignment'])->name('staff.assignments.publish');
        Route::delete('/assignments/{id}/delete', [App\Http\Controllers\StaffController::class, 'deleteAssignment'])->name('staff.assignments.delete');
        Route::get('/assignments/submissions/{submissionId}/view/{fileIndex}', [App\Http\Controllers\StaffController::class, 'viewSubmissionFile'])->name('staff.assignments.submissions.view-file');
        Route::get('/assignments/{id}/submissions', [App\Http\Controllers\StaffController::class, 'getAssignmentSubmissions'])->name('staff.assignments.submissions');
        Route::post('/assignments/submissions/{id}/grade', [App\Http\Controllers\StaffController::class, 'gradeSubmission'])->name('staff.assignments.grade');
        Route::get('/assignments/submissions/{id}/files', [App\Http\Controllers\StaffController::class, 'getSubmissionFiles'])->name('staff.assignments.submission.files');
        Route::get('/assignments/submissions/download/{submissionId}/{fileIndex}', [App\Http\Controllers\StaffController::class, 'downloadSubmissionFile'])->name('staff.assignments.submission.download');
        Route::get('/profile', [App\Http\Controllers\StaffController::class, 'profile'])->name('staff.profile');
        Route::post('/profile', [App\Http\Controllers\StaffController::class, 'updateProfile'])->name('staff.profile.update');
        Route::get('/password/change', [App\Http\Controllers\StaffController::class, 'changePassword'])->name('staff.password.change');
        Route::post('/password/change', [App\Http\Controllers\StaffController::class, 'updatePassword'])->name('staff.password.update');
        
        // Course Materials Routes
        Route::post('/materials/upload', [App\Http\Controllers\StaffController::class, 'uploadMaterial'])->name('staff.materials.upload');
        Route::post('/materials/get', [App\Http\Controllers\StaffController::class, 'getMaterials'])->name('staff.materials.get');
        Route::get('/materials/download/{id}', [App\Http\Controllers\StaffController::class, 'downloadMaterial'])->name('staff.materials.download');
        Route::delete('/materials/delete/{id}', [App\Http\Controllers\StaffController::class, 'deleteMaterial'])->name('staff.materials.delete');
        
        // Announcement Routes
        Route::delete('/announcements/delete/{id}', [App\Http\Controllers\StaffController::class, 'deleteAnnouncement'])->name('staff.announcements.delete');
        
        // AJAX Routes
        Route::post('/courses/class-students', [App\Http\Controllers\StaffController::class, 'getClassStudents'])->name('staff.courses.class-students');
        
        // Exam Results Management
        Route::get('/exam-results', [App\Http\Controllers\StaffController::class, 'examResults'])->name('staff.exam-results');
        Route::get('/exam-results/form', [App\Http\Controllers\StaffController::class, 'examResultsForm'])->name('staff.exam-results.form');
        Route::post('/exam-results/store', [App\Http\Controllers\StaffController::class, 'storeExamResults'])->name('staff.exam-results.store');
        Route::delete('/exam-results/delete', [App\Http\Controllers\StaffController::class, 'deleteExamResults'])->name('staff.exam-results.delete');
    });
});

// Course-specific routes
Route::prefix('course')->group(function () {
    Route::middleware('auth:student')->group(function () {
        Route::get('/{courseId}/test', function($courseId) {
            $courseInfo = [
                'id' => $courseId,
                'name' => strtoupper($courseId),
                'title' => 'Test Course - ' . strtoupper($courseId),
                'description' => 'This is a test course to verify the layout is working.',
                'instructor' => 'Test Instructor',
                'total_instructors' => 5,
                'total_students' => 100,
            ];
            return view('course.summary', compact('courseId', 'courseInfo'));
        })->name('course.test');
        
        Route::get('/{courseId}/summary', [App\Http\Controllers\CourseController::class, 'summary'])->name('course.summary');
        Route::get('/{courseId}/announcements', [App\Http\Controllers\CourseController::class, 'announcements'])->name('course.announcements');
        Route::get('/{courseId}/contents', [App\Http\Controllers\CourseController::class, 'contents'])->name('course.contents');
    });
});

// Login selection
Route::get('/login-selection', function () {
    return view('auth.login-selection');
})->name('login.selection');


// Finance Admin routes
Route::prefix('finance-admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\FinanceAdminController::class, 'dashboard'])->name('finance-admin.dashboard');
    Route::get('/students', [App\Http\Controllers\FinanceAdminController::class, 'students'])->name('finance-admin.students');
    Route::get('/students/{id}', [App\Http\Controllers\FinanceAdminController::class, 'showStudent'])->name('finance-admin.student.show');
    Route::post('/students/{id}/block', [App\Http\Controllers\FinanceAdminController::class, 'blockStudent'])->name('finance-admin.student.block');
    Route::post('/students/{id}/unblock', [App\Http\Controllers\FinanceAdminController::class, 'unblockStudent'])->name('finance-admin.student.unblock');
    Route::get('/students/{id}/payments', [App\Http\Controllers\FinanceAdminController::class, 'paymentHistory'])->name('finance-admin.payment-history');
    Route::get('/pending-payments', [App\Http\Controllers\FinanceAdminController::class, 'pendingPayments'])->name('finance-admin.pending-payments');
    Route::get('/reports', function() { return view('finance-admin.reports'); })->name('finance-admin.reports');
    Route::get('/password/change', [App\Http\Controllers\FinanceAdminController::class, 'changePassword'])->name('finance-admin.password.change');
    Route::post('/password/change', [App\Http\Controllers\FinanceAdminController::class, 'updatePassword'])->name('finance-admin.password.update');
    
    // Invoice Management Routes
    Route::get('/invoices', [App\Http\Controllers\FinanceAdminController::class, 'invoices'])->name('finance-admin.invoices');
    Route::get('/students/{id}/create-invoice', [App\Http\Controllers\FinanceAdminController::class, 'createInvoice'])->name('finance-admin.create-invoice');
    Route::post('/students/{id}/create-invoice', [App\Http\Controllers\FinanceAdminController::class, 'storeInvoice'])->name('finance-admin.store-invoice');
            Route::get('/invoices/{id}', [App\Http\Controllers\FinanceAdminController::class, 'showInvoice'])->name('finance-admin.invoice.show');
            Route::post('/payments/{id}/complete', [App\Http\Controllers\FinanceAdminController::class, 'markPaymentCompleted'])->name('finance-admin.payment.complete');
            Route::post('/invoices/{id}/mark-paid', [App\Http\Controllers\FinanceAdminController::class, 'markInvoiceAsPaid'])->name('finance-admin.invoice.mark-paid');
            Route::get('/invoices/{id}/pdf', [App\Http\Controllers\FinanceAdminController::class, 'generateInvoicePdf'])->name('finance-admin.invoice.pdf');
            Route::get('/invoices/{id}/view-pdf', [App\Http\Controllers\FinanceAdminController::class, 'viewInvoicePdf'])->name('finance-admin.invoice.view-pdf');
});

// Admin authentication routes (now using separate admin login)
Route::prefix('admin')->group(function () {
    Route::get('/login', function() {
        return redirect()->route('admin-finance.login')->with('message', 'Please use the admin login page.');
    })->name('admin.login');
    Route::post('/login', function() {
        return redirect()->route('admin-finance.login')->with('message', 'Please use the admin login page.');
    });
    Route::post('/logout', [App\Http\Controllers\Auth\AdminFinanceAuthController::class, 'logout'])->name('admin.logout');
    
    // Protected admin routes (accessible by both admin and staff users)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/students', [AdminController::class, 'students'])->name('admin.students');
        Route::get('/students/create', [AdminController::class, 'createStudent'])->name('admin.students.create');
        Route::post('/students', [AdminController::class, 'storeStudent'])->name('admin.students.store');
        Route::get('/students/{student}/edit', [AdminController::class, 'editStudent'])->name('admin.students.edit');
        Route::put('/students/{student}', [AdminController::class, 'updateStudent'])->name('admin.students.update');
        Route::delete('/students/{student}', [AdminController::class, 'deleteStudent'])->name('admin.students.delete');
        Route::post('/students/bulk-delete', [AdminController::class, 'bulkDeleteStudents'])->name('admin.students.bulk-delete');
        
        // Lecturer Management Routes
        Route::get('/lecturers', [AdminController::class, 'lecturers'])->name('admin.lecturers');
        Route::get('/lecturers/create', [AdminController::class, 'createLecturer'])->name('admin.lecturers.create');
        Route::post('/lecturers', [AdminController::class, 'storeLecturer'])->name('admin.lecturers.store');
                Route::get('/lecturers/{lecturer}/edit', [AdminController::class, 'editLecturer'])->name('admin.lecturers.edit');
                Route::put('/lecturers/{lecturer}', [AdminController::class, 'updateLecturer'])->name('admin.lecturers.update');
                Route::delete('/lecturers/{lecturer}', [AdminController::class, 'deleteLecturer'])->name('admin.lecturers.delete');
                
                // Ex-Student Management Routes
                Route::get('/ex-students', [AdminController::class, 'exStudents'])->name('admin.ex-students');
                Route::get('/ex-students/create', [AdminController::class, 'createExStudent'])->name('admin.ex-students.create');
                Route::post('/ex-students', [AdminController::class, 'storeExStudent'])->name('admin.ex-students.store');
                Route::get('/ex-students/{exStudent}/edit', [AdminController::class, 'editExStudent'])->name('admin.ex-students.edit');
                Route::put('/ex-students/{exStudent}', [AdminController::class, 'updateExStudent'])->name('admin.ex-students.update');
                Route::delete('/ex-students/{exStudent}', [AdminController::class, 'deleteExStudent'])->name('admin.ex-students.delete');
                Route::post('/ex-students/{exStudent}/generate-qr', [AdminController::class, 'generateQrCode'])->name('admin.ex-students.generate-qr');
                Route::get('/ex-students/{exStudent}/download-qr', [AdminController::class, 'downloadQrCode'])->name('admin.ex-students.download-qr');
        Route::post('/students/google-sheets-import', [AdminController::class, 'googleSheetsImport'])->name('admin.students.google-sheets-import');
        Route::post('/students/onedrive-import', [AdminController::class, 'oneDriveImport'])->name('admin.students.onedrive-import');
        Route::get('/import', [AdminController::class, 'showImportForm'])->name('admin.import');
        Route::post('/import', [AdminController::class, 'importStudents'])->name('admin.import');
        Route::get('/sync', [AdminController::class, 'showImportForm'])->name('admin.sync');
        Route::post('/sync', [AdminController::class, 'syncFromCsv'])->name('admin.sync');
        Route::get('/automation', [AdminController::class, 'automation'])->name('admin.automation');
        Route::post('/automation/trigger-import', [AdminController::class, 'triggerImport'])->name('admin.automation.trigger');
        Route::post('/automation/save-settings', [AdminController::class, 'saveAutomationSettings'])->name('admin.automation.save');
        Route::post('/automation/check-file', [AdminController::class, 'checkFileStatus'])->name('admin.automation.check-file');
        Route::post('/automation/run-check', [AdminController::class, 'runAutomationCheck'])->name('admin.automation.run-check');
        
        // Google Sheets automation routes
        Route::post('/automation/google-sheets/start', [AdminController::class, 'startGoogleSheetsAutomation'])->name('admin.automation.google-sheets.start');
        Route::post('/automation/google-sheets/stop', [AdminController::class, 'stopGoogleSheetsAutomation'])->name('admin.automation.google-sheets.stop');
        Route::post('/automation/google-sheets/test', [AdminController::class, 'testGoogleSheetsAutomation'])->name('admin.automation.google-sheets.test');
        
        // New automation controller routes
        Route::get('/automation/status-api', [AutomationController::class, 'status'])->name('automation.status-api');
        Route::post('/automation/start-api', [AutomationController::class, 'start'])->name('automation.start-api');
        Route::post('/automation/stop-api', [AutomationController::class, 'stop'])->name('automation.stop-api');
        Route::post('/automation/test-api', [AutomationController::class, 'test'])->name('automation.test-api');
        Route::get('/automation/run-check', [AutomationController::class, 'runAutomationCheck'])->name('automation.run-check');
        
        // Legacy automation routes (for backward compatibility)
        Route::post('/automation/start', [AdminController::class, 'startAutomation'])->name('admin.automation.start');
        Route::post('/automation/stop', [AdminController::class, 'stopAutomation'])->name('admin.automation.stop');
        Route::get('/automation/status', [AdminController::class, 'automation'])->name('admin.automation.status');
        
        // New automation web interface routes
        Route::get('/automation-web', [AdminController::class, 'automationWeb'])->name('admin.automation.web');
        Route::get('/automation/status-web', [AdminController::class, 'automationStatus'])->name('admin.automation.status');
        Route::post('/automation/start-web', [AdminController::class, 'startAutomation'])->name('admin.automation.start');
        Route::post('/automation/stop-web', [AdminController::class, 'stopAutomation'])->name('admin.automation.stop');
        Route::post('/automation/test-web', [AdminController::class, 'testAutomation'])->name('admin.automation.test');
        Route::get('/automation/logs-web', [AdminController::class, 'automationLogs'])->name('admin.automation.logs');
        
        // OneDrive automation routes
        Route::get('/automation-onedrive', [AdminController::class, 'automationOneDrive'])->name('admin.automation.onedrive');
        Route::post('/automation-setup', [AdminController::class, 'setupAutomation'])->name('admin.automation.setup');
        
        // Auto-sync management page
        Route::get('/auto-sync', function() {
            return view('admin.auto-sync');
        })->name('admin.auto-sync');
        
        // Auto-sync routes
        Route::post('/auto-sync/start', [AdminController::class, 'startAutoSync'])->name('admin.auto-sync.start');
        Route::post('/auto-sync/stop', [AdminController::class, 'stopAutoSync'])->name('admin.auto-sync.stop');
        Route::get('/auto-sync/status', [AdminController::class, 'getAutoSyncStatus'])->name('admin.auto-sync.status');
        Route::post('/auto-sync/force', [AdminController::class, 'forceAutoSync'])->name('admin.auto-sync.force');
        Route::post('/auto-sync/set-interval', [AdminController::class, 'setAutoSyncInterval'])->name('admin.auto-sync.set-interval');
        
        
        // Debug routes (remove in production)
        Route::get('/debug/test-simple', function() {
            return response()->json(['success' => true, 'message' => 'Simple test route works']);
        })->name('admin.debug.test-simple');
        Route::get('/debug/test-status', function() {
            try {
                $syncService = new \App\Services\AutoOneDriveSyncService();
                $status = $syncService->getSyncStatus();
                return response()->json(['success' => true, 'status' => $status]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()]);
            }
        })->name('admin.debug.test-status');
        Route::get('/debug/onedrive-test', [AdminController::class, 'testOneDriveConnection'])->name('admin.debug.onedrive-test');
        Route::get('/debug/test-url', function() {
            $url = env('ONEDRIVE_EXCEL_URL');
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);
            
            return response()->json([
                'url' => $url,
                'status' => $response->status(),
                'success' => $response->successful(),
                'content_type' => $response->header('Content-Type'),
                'content_length' => $response->header('Content-Length'),
                'response_preview' => substr($response->body(), 0, 500)
            ]);
        })->name('admin.debug.test-url');
        Route::get('/debug/test-edit/{id}', function($id) {
            $student = \App\Models\User::find($id);
            if ($student) {
                return response()->json([
                    'success' => true,
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'student_role' => $student->role,
                    'edit_url' => route('admin.students.edit', $student)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ]);
            }
        })->name('admin.debug.test-edit');
        
        // Student Certificate Management Routes
        Route::get('/student-certificates', [App\Http\Controllers\StudentCertificateController::class, 'index'])->name('admin.student-certificates.index');
        Route::get('/student-certificates/create', [App\Http\Controllers\StudentCertificateController::class, 'create'])->name('admin.student-certificates.create');
        Route::post('/student-certificates/generate', [App\Http\Controllers\StudentCertificateController::class, 'generateFromExcel'])->name('admin.student-certificates.generate');
        Route::get('/student-certificates/{id}/download', [App\Http\Controllers\StudentCertificateController::class, 'download'])->name('admin.student-certificates.download');
        Route::get('/student-certificates/{id}/view', [App\Http\Controllers\StudentCertificateController::class, 'view'])->name('admin.student-certificates.view');
        Route::delete('/student-certificates/{id}', [App\Http\Controllers\StudentCertificateController::class, 'destroy'])->name('admin.student-certificates.destroy');
        Route::post('/student-certificates/bulk-download', [App\Http\Controllers\StudentCertificateController::class, 'bulkDownload'])->name('admin.student-certificates.bulk-download');
        Route::post('/student-certificates/bulk-delete', [App\Http\Controllers\StudentCertificateController::class, 'bulkDelete'])->name('admin.student-certificates.bulk-delete');
        
        // Password change route
        Route::get('/password/change', function() {
            return view('admin.password-change');
        })->name('admin.password.change');
        Route::post('/password/change', function(Request $request) {
            $request->validate([
                'current_password' => 'required',
                'password' => ['required', 'confirmed', new \App\Rules\StrongPassword],
            ]);
            
            $user = auth()->user();
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            // Check if new password is the same as current password
            if (Hash::check($request->password, $user->password)) {
                return back()->withErrors(['password' => 'New password must be different from your current password.']);
            }

            // Check password history (prevent reusing last 5 passwords)
            if (\App\Models\PasswordHistory::hasUsedPassword($user->id, $request->password, 5)) {
                return back()->withErrors(['password' => 'You cannot reuse any of your last 5 passwords.']);
            }

            // Store current password in history before updating
            \App\Models\PasswordHistory::storePassword($user->id, $request->current_password);
            
            $user->update([
                'password' => Hash::make($request->password),
                'must_reset_password' => false
            ]);
            
            return redirect()->route('admin.dashboard')->with('success', 'Password changed successfully! Your password is now more secure.');
        })->name('admin.password.change.submit');
        
        // Home Page Content Management
        Route::prefix('home-page')->group(function () {
            Route::get('/', [App\Http\Controllers\AdminContentController::class, 'homePageIndex'])->name('admin.home-page.index');
            Route::get('/create', [App\Http\Controllers\AdminContentController::class, 'homePageCreate'])->name('admin.home-page.create');
            Route::post('/', [App\Http\Controllers\AdminContentController::class, 'homePageStore'])->name('admin.home-page.store');
            Route::get('/{content}/edit', [App\Http\Controllers\AdminContentController::class, 'homePageEdit'])->name('admin.home-page.edit');
            Route::put('/{content}', [App\Http\Controllers\AdminContentController::class, 'homePageUpdate'])->name('admin.home-page.update');
            Route::delete('/{content}', [App\Http\Controllers\AdminContentController::class, 'homePageDestroy'])->name('admin.home-page.destroy');
            Route::post('/upload-image', [App\Http\Controllers\AdminContentController::class, 'uploadImage'])->name('admin.home-page.upload-image');
        });
        
        // Public Announcements Management
        Route::prefix('announcements')->group(function () {
            Route::get('/', [App\Http\Controllers\AdminContentController::class, 'announcementsIndex'])->name('admin.announcements.index');
            Route::get('/preview', function() {
                $announcements = \App\Models\PublicAnnouncement::active()->published()->latest()->paginate(10);
                $categories = \App\Models\PublicAnnouncement::active()->published()->distinct()->pluck('category');
                return view('admin.announcements.admin-announcements', compact('announcements', 'categories'));
            })->name('admin.announcements.preview');
            Route::get('/create', [App\Http\Controllers\AdminContentController::class, 'announcementsCreate'])->name('admin.announcements.create');
            Route::post('/', [App\Http\Controllers\AdminContentController::class, 'announcementsStore'])->name('admin.announcements.store');
            Route::get('/{announcement}/edit', [App\Http\Controllers\AdminContentController::class, 'announcementsEdit'])->name('admin.announcements.edit');
            Route::put('/{announcement}', [App\Http\Controllers\AdminContentController::class, 'announcementsUpdate'])->name('admin.announcements.update');
            Route::delete('/{announcement}', [App\Http\Controllers\AdminContentController::class, 'announcementsDestroy'])->name('admin.announcements.destroy');
            Route::post('/upload-image', [App\Http\Controllers\AdminContentController::class, 'uploadImage'])->name('admin.announcements.upload-image');
        });
        
        // Home Page Preview
        Route::get('/home-page/preview', function() {
            $homePageContents = \App\Models\HomePageContent::active()->ordered()->get();
            $announcements = \App\Models\PublicAnnouncement::active()->published()->featured()->latest()->take(3)->get();
            return view('admin.home-page.admin-home', compact('homePageContents', 'announcements'));
        })->name('admin.home-page.preview');
        
        // Gallery Management
        Route::post('/gallery/update', function(Request $request) {
            $request->validate([
                'images' => 'required|array',
                'images.*' => 'url'
            ]);
            
            $gallerySection = \App\Models\HomePageContent::where('section_name', 'gallery')->first();
            
            if (!$gallerySection) {
                $gallerySection = \App\Models\HomePageContent::create([
                    'section_name' => 'gallery',
                    'title' => 'Gallery',
                    'content' => '',
                    'metadata' => json_encode(['images' => $request->images]),
                    'sort_order' => 2,
                    'is_active' => true,
                    'admin_id' => \App\Models\Admin::where('email', auth()->user()->email)->first()->id ?? 1
                ]);
            } else {
                $gallerySection->update([
                    'metadata' => json_encode(['images' => $request->images])
                ]);
            }
            
            return response()->json(['success' => true, 'message' => 'Gallery updated successfully']);
        })->name('admin.gallery.update');
        
        // Gallery Image Upload
        Route::post('/gallery/upload', function(Request $request) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120' // 5MB max
            ]);
            
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/gallery', $filename);
                $url = asset('storage/gallery/' . $filename);
                
                return response()->json([
                    'success' => true, 
                    'url' => $url,
                    'message' => 'Image uploaded successfully'
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'No image file provided']);
        })->name('admin.gallery.upload');
        
        // Hero Section Management
        Route::post('/hero/update', function(Request $request) {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image_url' => 'required|url'
            ]);
            
            $heroSection = \App\Models\HomePageContent::where('section_name', 'hero')->first();
            
            if (!$heroSection) {
                $heroSection = \App\Models\HomePageContent::create([
                    'section_name' => 'hero',
                    'title' => $request->title,
                    'content' => $request->content,
                    'image_url' => $request->image_url,
                    'sort_order' => 1,
                    'is_active' => true,
                    'admin_id' => \App\Models\Admin::where('email', auth()->user()->email)->first()->id ?? 1
                ]);
            } else {
                $heroSection->update([
                    'title' => $request->title,
                    'content' => $request->content,
                    'image_url' => $request->image_url
                ]);
            }
            
            return response()->json(['success' => true, 'message' => 'Hero section updated successfully']);
        })->name('admin.hero.update');
    });
});

// HTTP Cron endpoint for external cron services (no authentication required)
Route::get('/import-students', function() {
    // Set execution time limit for cron import
    set_time_limit(300); // 5 minutes
    
    try {
        Log::info('HTTP Cron: Starting Google Drive import');
        
        // Use Google Drive service instead of OneDrive
        $service = new \App\Services\GoogleDriveImportService();
        $result = $service->importFromGoogleDrive();
        
        Log::info('HTTP Cron: Import completed', $result);
        
        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'] ?? 'Import completed successfully',
            'timestamp' => now()->toISOString(),
            'created' => $result['created'] ?? 0,
            'updated' => $result['updated'] ?? 0,
            'errors' => $result['errors'] ?? 0,
            'processed_sheets' => $result['processed_sheets'] ?? []
        ]);
    } catch (\Exception $e) {
        Log::error('HTTP Cron: Google Drive import failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Import failed: ' . $e->getMessage(),
            'timestamp' => now()->toISOString()
        ], 500);
    }
})->name('admin.import-students');

// Admin import endpoint (same as main import but with /admin prefix)
Route::get('/admin/import-students', function() {
    // Set execution time limit for cron import
    set_time_limit(300); // 5 minutes
    
    try {
        Log::info('HTTP Cron: Starting Google Drive import via /admin/import-students');
        
        // Use Google Drive service instead of OneDrive
        $service = new \App\Services\GoogleDriveImportService();
        $result = $service->importFromGoogleDrive();
        
        Log::info('HTTP Cron: Import completed', $result);
        
        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'] ?? 'Import completed successfully',
            'timestamp' => now()->toISOString(),
            'created' => $result['created'] ?? 0,
            'updated' => $result['updated'] ?? 0,
            'errors' => $result['errors'] ?? 0,
            'processed_sheets' => $result['processed_sheets'] ?? []
        ]);
    } catch (\Exception $e) {
        Log::error('HTTP Cron: Google Drive import failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Import failed: ' . $e->getMessage(),
            'timestamp' => now()->toISOString()
        ], 500);
    }
});

// Test endpoint for OneDrive import
Route::get('/admin/test-import', [App\Http\Controllers\StudentImportController::class, 'testImport']);

// API endpoint for Python OneDrive bridge
Route::post('/api/import-excel-data', [App\Http\Controllers\ExcelDataImportController::class, 'importExcelData']);

// Simple test route (no middleware)
Route::get('/test-cron', function() {
    return response()->json(['success' => true, 'message' => 'Cron test endpoint works']);
});

// Test file upload route
Route::post('/test-upload', function(Request $request) {
    try {
        $file = $request->file('excel_file');
        if (!$file) {
            return response()->json(['error' => 'No file uploaded']);
        }
        
        $filePath = $file->store('temp', 'local');
        $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . $filePath);
        
        return response()->json([
            'success' => true,
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $filePath,
            'full_path' => $fullPath,
            'file_exists' => file_exists($fullPath),
            'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

// Test route for checking payment status (for local development)
Route::get('/test-payment-status/{paymentId}', function($paymentId) {
    try {
        $payment = \App\Models\Payment::findOrFail($paymentId);
        
        return response()->json([
            'success' => true,
            'payment' => [
                'id' => $payment->id,
                'billplz_id' => $payment->billplz_id,
                'status' => $payment->status,
                'amount' => $payment->formatted_amount,
                'description' => $payment->description,
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                'is_expired' => $payment->isExpired(),
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Payment not found: ' . $e->getMessage()
        ]);
    }
});

// Billplz webhook routes (no authentication required)
Route::post('/payment/billplz/callback', [App\Http\Controllers\PaymentController::class, 'billplzCallback'])->name('billplz.callback');
Route::get('/payment/billplz/redirect', [App\Http\Controllers\PaymentController::class, 'billplzRedirect'])->name('billplz.redirect');

// Accounting integration API routes
Route::prefix('api/accounting')->group(function () {
    Route::get('/payments', [App\Http\Controllers\PaymentController::class, 'getAccountingData'])->name('accounting.payments');
    Route::get('/statistics', [App\Http\Controllers\PaymentController::class, 'getPaymentStatistics'])->name('accounting.statistics');
    Route::post('/sync', [App\Http\Controllers\PaymentController::class, 'syncToAccounting'])->name('accounting.sync');
    Route::get('/test-connection', [App\Http\Controllers\PaymentController::class, 'testAccountingConnection'])->name('accounting.test');
});

// Ex-Student QR Verification Routes
Route::prefix('ex-student')->group(function () {
    Route::get('/login', [App\Http\Controllers\ExStudentController::class, 'login'])->name('ex-student.login');
    Route::post('/verify-qr', [App\Http\Controllers\ExStudentController::class, 'verifyQr'])->name('ex-student.verify-qr');
    Route::get('/verify', [App\Http\Controllers\ExStudentController::class, 'verify'])->name('ex-student.verify');
    Route::get('/dashboard', [App\Http\Controllers\ExStudentController::class, 'dashboard'])->name('ex-student.dashboard');
    Route::get('/certificate', [App\Http\Controllers\ExStudentController::class, 'certificate'])->name('ex-student.certificate');
    Route::get('/certificate-preview', [App\Http\Controllers\ExStudentController::class, 'certificatePreview'])->name('ex-student.certificate.preview');
    Route::get('/transcript1', [App\Http\Controllers\ExStudentController::class, 'transcript1'])->name('ex-student.transcript1');
    Route::get('/transcript2', [App\Http\Controllers\ExStudentController::class, 'transcript2'])->name('ex-student.transcript2');
    Route::post('/generate-qr', [App\Http\Controllers\ExStudentController::class, 'generateQrCode'])->name('ex-student.generate-qr');
    Route::post('/logout', [App\Http\Controllers\ExStudentController::class, 'logout'])->name('ex-student.logout');
});

// Recent activities endpoint (no authentication required)
Route::get('/recent-activities', function() {
    try {
        $activities = \App\Models\SyncActivity::getRecentActivities(24);
        
        return response()->json([
            'success' => true,
            'activities' => $activities->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'status' => $activity->status,
                    'message' => $activity->message,
                    'created_count' => $activity->created_count,
                    'updated_count' => $activity->updated_count,
                    'error_count' => $activity->error_count,
                    'processed_sheets' => $activity->processed_sheets,
                    'source' => $activity->source,
                    'formatted_time' => $activity->formatted_time,
                    'status_badge_class' => $activity->status_badge_class,
                    'created_at' => $activity->created_at->toISOString()
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch activities: ' . $e->getMessage()
        ]);
    }
});

// Cleanup old activities endpoint (no authentication required)
Route::get('/cleanup-activities', function() {
    try {
        $deleted = \App\Models\SyncActivity::cleanupOldActivities(24);
        
        return response()->json([
            'success' => true,
            'message' => "Cleaned up {$deleted} old activities",
            'deleted_count' => $deleted
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to cleanup activities: ' . $e->getMessage()
        ]);
    }
});

// Auto-sync endpoint (performs actual import based on interval)
Route::get('/auto-sync', function() {
    try {
        $autoSyncService = new \App\Services\AutoSyncService();
        $result = $autoSyncService->performAutoSync();
        
        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'skipped' => $result['skipped'] ?? false,
            'result' => $result['result'] ?? null,
            'timestamp' => now()->toISOString()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Auto-sync failed: ' . $e->getMessage(),
            'timestamp' => now()->toISOString()
        ]);
    }
});

// Simple status endpoint (no authentication required)
Route::get('/sync-status', function() {
    try {
        $autoSyncService = new \App\Services\AutoSyncService();
        $status = $autoSyncService->getSyncStatus();
        
        return response()->json([
            'success' => true,
            'status' => $status
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Status check failed: ' . $e->getMessage()
        ]);
    }
});

// Test endpoint for OneDrive import (no authentication required)
Route::post('/test-onedrive-import', function() {
    try {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        
        Log::info('Test OneDrive import started');
        
        $service = new \App\Services\OneDriveExcelImportService();
        $result = $service->importFromOneDrive();
        
        Log::info('Test OneDrive import completed', $result);
        
        return response()->json($result);
    } catch (\Exception $e) {
        Log::error('Test OneDrive import failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

// Certificate generation routes
Route::prefix('certificates')->group(function () {
    // Generate Word certificate
    Route::post('/generate/word', [CertificateController::class, 'generateWordCertificate'])->name('certificate.generate.word');
    
    // Generate PDF certificate
    Route::get('/generate/pdf/{studentId}', [CertificateController::class, 'generatePdfCertificate'])->name('certificate.generate.pdf');
    
    // Generate PDF certificate (cPanel compatible)
    Route::get('/generate/pdf-cpanel/{studentId}', [CertificateController::class, 'generatePdfCertificateCpanel'])->name('certificate.generate.pdf.cpanel');
    
    // Generate PDF preview (for preview page)
    Route::post('/preview/pdf', [CertificateController::class, 'generatePdfPreview'])->name('certificate.preview.pdf');
    
    // Generate PDF preview (for iframe direct access)
    Route::get('/preview/pdf/{studentId}', [CertificateController::class, 'generatePdfPreview'])->name('certificate.preview.pdf.get');
    
    // Static PDF preview (simple iframe)
    Route::get('/preview/{id}', [CertificateController::class, 'previewCertificate'])->name('certificates.preview');
    
    // Test PDF generation endpoint
    Route::get('/test-pdf/{studentId}', [CertificateController::class, 'testPdfGeneration']);
    
    // Minimal test PDF route
    Route::get('/test-pdf', function () {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test PDF</h1><p>If you see this, PDF works.</p>');
        return $pdf->stream('test.pdf');
    });
    
    // Preview certificate
    Route::get('/preview', [CertificateController::class, 'previewCertificate'])->name('certificate.preview');
    
    // Generate template (for testing)
    Route::get('/template', [CertificateController::class, 'generateTemplate'])->name('certificate.template');
    
    // Get certificate data
    Route::get('/data', [CertificateController::class, 'getCertificateData'])->name('certificate.data');
    
    // Ex-students certificate routes
    Route::get('/', [CertificateController::class, 'index'])->name('certificate.index');
    Route::get('/generate/{studentId}', [CertificateController::class, 'generatePdfCertificateCpanel'])->name('certificate.generate');
    Route::get('/download/{studentId}', [CertificateController::class, 'download'])->name('certificate.download');
    Route::get('/verify/{certificateNumber}', [CertificateController::class, 'verifyCertificate'])->name('certificate.verify');
    
    // Bulk generate certificates
    Route::post('/bulk-generate', [CertificateController::class, 'bulkGenerateCertificates'])->name('certificate.bulk.generate');
    
    // Download certificate
    Route::get('/download/{filename}', [CertificateController::class, 'downloadCertificate'])->name('certificate.download');
});

// Maintenance route - catch all URLs with # and redirect to maintenance page
Route::get('/{any}', function ($any) {
    if (strpos($any, '#') !== false) {
        return view('maintenance');
    }
    abort(404);
})->where('any', '.*');

