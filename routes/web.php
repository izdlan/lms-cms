<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AnnouncementController;

// Main routes
Route::get('/', function () {
    return view('home');
});

Route::get('/classes', function () {
    return view('classes');
})->name('classes');

// Announcements routes
Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/announcements/{id}', [AnnouncementController::class, 'show'])->name('announcements.show');

// Unified login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Student authentication routes
Route::prefix('student')->group(function () {
    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('student.logout');
    Route::get('/password/reset', [StudentAuthController::class, 'showPasswordResetForm'])->name('student.password.reset');
    Route::post('/password/email', [StudentAuthController::class, 'sendPasswordResetLink'])->name('student.password.email');
    Route::get('/password/reset/{token}', function ($token) {
        return view('auth.student-password-reset', ['token' => $token]);
    })->name('student.password.reset.token');
    Route::post('/password/reset', [StudentAuthController::class, 'resetPassword'])->name('student.password.update');
    
    // Protected password change routes
    Route::middleware('auth:student')->group(function () {
        Route::get('/password/change', [StudentAuthController::class, 'showPasswordChangeForm'])->name('student.password.change');
        Route::post('/password/change', [StudentAuthController::class, 'changePassword'])->name('student.password.change.post');
    });
    
    // Protected student routes
    Route::middleware('auth:student')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/courses', [StudentController::class, 'courses'])->name('student.courses');
        Route::get('/assignments', [StudentController::class, 'assignments'])->name('student.assignments');
        Route::get('/profile', [StudentController::class, 'profile'])->name('student.profile');
        Route::post('/profile/picture', [StudentController::class, 'uploadProfilePicture'])->name('student.profile.picture');
        Route::delete('/profile/picture', [StudentController::class, 'deleteProfilePicture'])->name('student.profile.picture.delete');
        Route::get('/password/reset', [StudentController::class, 'showPasswordResetForm'])->name('student.password.reset');
        Route::post('/password/update', [StudentController::class, 'updatePassword'])->name('student.password.update');
        Route::get('/stats', [StudentController::class, 'getStats'])->name('student.stats');
        Route::get('/bills', [StudentController::class, 'bills'])->name('student.bills');
        Route::get('/payment', [StudentController::class, 'payment'])->name('student.payment');
        Route::get('/receipt', [StudentController::class, 'receipt'])->name('student.receipt');
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
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    // Protected admin routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/students', [AdminController::class, 'students'])->name('admin.students');
        Route::get('/students/create', [AdminController::class, 'createStudent'])->name('admin.students.create');
        Route::post('/students', [AdminController::class, 'storeStudent'])->name('admin.students.store');
        Route::get('/students/{student}/edit', [AdminController::class, 'editStudent'])->name('admin.students.edit');
        Route::put('/students/{student}', [AdminController::class, 'updateStudent'])->name('admin.students.update');
        Route::delete('/students/{student}', [AdminController::class, 'deleteStudent'])->name('admin.students.delete');
        Route::post('/students/bulk-delete', [AdminController::class, 'bulkDeleteStudents'])->name('admin.students.bulk-delete');
        Route::get('/import', [AdminController::class, 'showImportForm'])->name('admin.import');
        Route::post('/import', [AdminController::class, 'importStudents'])->name('admin.import');
        Route::get('/sync', [AdminController::class, 'showImportForm'])->name('admin.sync');
        Route::post('/sync', [AdminController::class, 'syncFromCsv'])->name('admin.sync');
        Route::get('/automation', [AdminController::class, 'automation'])->name('admin.automation');
        Route::post('/automation/trigger-import', [AdminController::class, 'triggerImport'])->name('admin.automation.trigger');
    });
});

// Maintenance page route
Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');

// Catch-all route for any broken links or 404s - redirect to maintenance
Route::fallback(function () {
    return response()->view('maintenance', [], 404);
});
