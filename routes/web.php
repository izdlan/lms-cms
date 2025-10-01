<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AutomationController;

// Maintenance route - direct access
Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');

// Main routes - show home page
Route::get('/', function () {
    return view('home');
});

Route::get('/classes', function () {
    return view('classes');
})->name('classes');

// Professional Bootstrap Demo
Route::get('/professional-demo', function () {
    return view('professional-demo');
})->name('professional.demo');

// Announcements routes
Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/announcements/{id}', [App\Http\Controllers\AnnouncementController::class, 'show'])->name('announcements.show');

// Bootstrap test route
Route::get('/test-bootstrap', function () {
    return view('test-bootstrap');
})->name('test.bootstrap');

// Unified login system
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
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
    Route::middleware('auth:student')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/courses', [App\Http\Controllers\StudentController::class, 'courses'])->name('student.courses');
        Route::get('/course/{program}', [App\Http\Controllers\StudentController::class, 'courseSummary'])->name('student.course.summary');
        Route::get('/course/{subjectCode}/class', [App\Http\Controllers\StudentController::class, 'courseClass'])->name('student.course.class');
        Route::get('/assignments', [App\Http\Controllers\StudentController::class, 'assignments'])->name('student.assignments');
        Route::post('/assignments/{id}/submit', [App\Http\Controllers\StudentController::class, 'submitAssignment'])->name('student.assignments.submit');
        Route::get('/assignments/{id}/details', [App\Http\Controllers\StudentController::class, 'getAssignmentDetails'])->name('student.assignments.details');
        Route::get('/profile', [App\Http\Controllers\StudentController::class, 'profile'])->name('student.profile');
        Route::post('/profile/picture', [App\Http\Controllers\StudentController::class, 'uploadProfilePicture'])->name('student.profile.picture');
        Route::delete('/profile/picture', [App\Http\Controllers\StudentController::class, 'deleteProfilePicture'])->name('student.profile.picture.delete');
        Route::post('/password/update', [App\Http\Controllers\StudentController::class, 'updatePassword'])->name('student.password.update');
        Route::get('/stats', [App\Http\Controllers\StudentController::class, 'getStats'])->name('student.stats');
        Route::get('/bills', [App\Http\Controllers\StudentController::class, 'bills'])->name('student.bills');
        Route::get('/payment', [App\Http\Controllers\StudentController::class, 'payment'])->name('student.payment');
        Route::get('/receipt', [App\Http\Controllers\StudentController::class, 'receipt'])->name('student.receipt');
        Route::get('/materials/download/{id}', [App\Http\Controllers\StudentController::class, 'downloadMaterial'])->name('student.materials.download');
        Route::get('/assignments/download/{assignmentId}/{fileIndex}', [App\Http\Controllers\StudentController::class, 'downloadAssignmentFile'])->name('student.assignments.download');
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
        Route::get('/assignments/{id}/submissions', [App\Http\Controllers\StaffController::class, 'getAssignmentSubmissions'])->name('staff.assignments.submissions');
        Route::post('/assignments/submissions/{id}/grade', [App\Http\Controllers\StaffController::class, 'gradeSubmission'])->name('staff.assignments.grade');
        Route::get('/students', [App\Http\Controllers\StaffController::class, 'students'])->name('staff.students');
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


// Admin authentication routes (now using unified login)
Route::prefix('admin')->group(function () {
    Route::get('/login', function() {
        return redirect()->route('login')->with('message', 'Please use the main login page and select "Admin" as your role.');
    })->name('admin.login');
    Route::post('/login', function() {
        return redirect()->route('login')->with('message', 'Please use the main login page and select "Admin" as your role.');
    });
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('admin.logout');
    
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

// Maintenance route - catch all URLs with # and redirect to maintenance page
Route::get('/{any}', function ($any) {
    if (strpos($any, '#') !== false) {
        return view('maintenance');
    }
    abort(404);
})->where('any', '.*');
