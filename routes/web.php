<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;

// Main routes
Route::get('/', function () {
    return view('home');
});

Route::get('/classes', function () {
    return view('classes');
})->name('classes');

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
        Route::get('/import', [AdminController::class, 'showImportForm'])->name('admin.import');
        Route::post('/import', [AdminController::class, 'importStudents'])->name('admin.import');
        Route::get('/sync', [AdminController::class, 'showImportForm'])->name('admin.sync');
        Route::post('/sync', [AdminController::class, 'syncFromCsv'])->name('admin.sync');
        Route::get('/automation', [AdminController::class, 'automation'])->name('admin.automation');
        Route::post('/automation/trigger-import', [AdminController::class, 'triggerImport'])->name('admin.automation.trigger');
    });
});
