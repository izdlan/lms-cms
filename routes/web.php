<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AutomationController;

// Main routes
Route::get('/', function () {
    return view('home');
});

Route::get('/classes', function () {
    return view('classes');
})->name('classes');

// Bootstrap test route
Route::get('/test-bootstrap', function () {
    return view('test-bootstrap');
})->name('test.bootstrap');

// Login selection
Route::get('/login', function () {
    return view('auth.login-selection');
})->name('login.selection');

// Student authentication routes
Route::prefix('student')->group(function () {
    Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
    Route::post('/login', [StudentAuthController::class, 'login']);
    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('student.logout');
    Route::get('/password/reset', [StudentAuthController::class, 'showPasswordResetForm'])->name('student.password.reset');
    Route::post('/password/email', [StudentAuthController::class, 'sendPasswordResetLink'])->name('student.password.email');
    Route::get('/password/reset/{token}', function ($token) {
        return view('auth.student-password-reset', ['token' => $token]);
    })->name('student.password.reset.token');
    Route::post('/password/reset', [StudentAuthController::class, 'resetPassword'])->name('student.password.update');
    Route::get('/password/change', [StudentAuthController::class, 'showPasswordChangeForm'])->name('student.password.change');
    Route::post('/password/change', [StudentAuthController::class, 'changePassword'])->name('student.password.change.post');
    
    // Protected student routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/courses', [StudentController::class, 'courses'])->name('student.courses');
        Route::get('/assignments', [StudentController::class, 'assignments'])->name('student.assignments');
        Route::get('/profile', [StudentController::class, 'profile'])->name('student.profile');
        Route::put('/profile', [StudentController::class, 'updateProfile'])->name('student.profile.update');
        Route::get('/password/reset', [StudentController::class, 'showPasswordResetForm'])->name('student.password.reset');
        Route::post('/password/update', [StudentController::class, 'updatePassword'])->name('student.password.update');
        Route::get('/stats', [StudentController::class, 'getStats'])->name('student.stats');
        Route::get('/bills', [StudentController::class, 'bills'])->name('student.bills');
        Route::get('/payment', [StudentController::class, 'payment'])->name('student.payment');
        Route::get('/receipt', [StudentController::class, 'receipt'])->name('student.receipt');
    });
});

// Staff authentication routes
Route::prefix('staff')->group(function () {
    // Protected staff routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
        Route::get('/courses', [StaffController::class, 'courses'])->name('staff.courses');
        Route::get('/announcements', [StaffController::class, 'announcements'])->name('staff.announcements');
        Route::get('/contents', [StaffController::class, 'contents'])->name('staff.contents');
        Route::get('/assignments', [StaffController::class, 'assignments'])->name('staff.assignments');
        Route::get('/students', [StaffController::class, 'students'])->name('staff.students');
        Route::get('/profile', [StaffController::class, 'profile'])->name('staff.profile');
        Route::post('/profile', [StaffController::class, 'updateProfile'])->name('staff.profile.update');
        Route::get('/password/change', [StaffController::class, 'changePassword'])->name('staff.password.change');
        Route::post('/password/change', [StaffController::class, 'updatePassword'])->name('staff.password.update');
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
        
        Route::get('/{courseId}/summary', [CourseController::class, 'summary'])->name('course.summary');
        Route::get('/{courseId}/announcements', [CourseController::class, 'announcements'])->name('course.announcements');
        Route::get('/{courseId}/contents', [CourseController::class, 'contents'])->name('course.contents');
    });
});

// Admin authentication routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    // Protected admin routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/students', [AdminController::class, 'students'])->name('admin.students');
        Route::get('/students/create', [AdminController::class, 'createStudent'])->name('admin.students.create');
        Route::post('/students', [AdminController::class, 'storeStudent'])->name('admin.students.store');
        Route::get('/students/{student}/edit', [AdminController::class, 'editStudent'])->name('admin.students.edit');
        Route::put('/students/{student}', [AdminController::class, 'updateStudent'])->name('admin.students.update');
        Route::delete('/students/{student}', [AdminController::class, 'deleteStudent'])->name('admin.students.delete');
        Route::post('/students/bulk-delete', [AdminController::class, 'bulkDeleteStudents'])->name('admin.students.bulk-delete');
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
    });
});
