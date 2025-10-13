<?php
/**
 * Controller: AdminController
 * Purpose: Admin and staff tools for user management (students, lecturers),
 *          data imports (CSV/XLSX/Google Sheets/Drive), automation controls,
 *          and ex-student certificate/QR handling.
 * Access: Requires authenticated admin or staff via `checkAdminAccess()`.
 * Key Views: `admin.dashboard`, `admin.students`, `admin.import-students`,
 *            `admin.lecturers` (+ create/edit), `admin.ex-students`,
 *            automation pages and JSON endpoints.
 * Notes: Heavily logs actions, uses cache for automation state, and exposes
 *        HTTP endpoints for cron-like operations.
 */

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lecturer;
use App\Models\ExStudent;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Services\CsvImportService;
use App\Services\XlsxImportService;
use App\Services\GoogleSheetsImportService;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function checkAdminAccess()
    {
        if (!Auth::check() || (!Auth::user()->isAdmin() && !Auth::user()->isStaff())) {
            abort(403, 'Unauthorized access.');
        }
    }

    public function dashboard()
    {
        $this->checkAdminAccess();
        
        $students = User::where('role', 'student')->paginate(20);
        $totalStudents = User::where('role', 'student')->count();
        
        // Get programme statistics
        $totalProgrammes = \App\Models\User::where('role', 'student')
            ->whereNotNull('programme_name')
            ->where('programme_name', '!=', '')
            ->distinct('programme_name')
            ->count('programme_name');
        
        $activeProgrammes = \App\Models\User::where('role', 'student')
            ->whereNotNull('programme_name')
            ->where('programme_name', '!=', '')
            ->distinct('programme_name')
            ->count('programme_name');
        
        $totalEnrollments = \App\Models\StudentEnrollment::count();
        $activeEnrollments = \App\Models\StudentEnrollment::where('status', 'enrolled')->count();
        
        return view('admin.dashboard', compact('students', 'totalStudents', 'totalProgrammes', 'activeProgrammes', 'totalEnrollments', 'activeEnrollments'));
    }

    public function students()
    {
        $this->checkAdminAccess();
        
        $students = User::where('role', 'student')->paginate(20);
        return view('admin.students', compact('students'));
    }

    public function showImportForm()
    {
        $this->checkAdminAccess();
        return view('admin.import-students');
    }

    public function importStudents(Request $request)
    {
        $this->checkAdminAccess();
        
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt'
        ]);

        try {
            $file = $request->file('excel_file');
            $fileExtension = $file->getClientOriginalExtension();
            
            if (in_array($fileExtension, ['xlsx', 'xls'])) {
                // Handle Excel files using XlsxImportService
                $xlsxService = new XlsxImportService();
                $result = $xlsxService->importFromXlsx($file->getRealPath());
                
                if ($result['success']) {
                    $message = "Import completed! Created: {$result['created']}, Updated: {$result['updated']}, Errors: {$result['errors']}";
                    return redirect()->route('admin.students')->with('success', $message);
                } else {
                    $errorMessage = isset($result['message']) ? $result['message'] : 'Error importing Excel file.';
                    return back()->withErrors(['excel_file' => $errorMessage]);
                }
            } else {
                // Handle CSV files using existing service
                $filePath = $file->getRealPath();
                $csvService = new CsvImportService();
                $result = $csvService->importFromCsv($filePath);
                
                if ($result['success']) {
                    $message = "Import completed! Created: {$result['created']}, Updated: {$result['updated']}, Errors: {$result['errors']}";
                    return redirect()->route('admin.students')->with('success', $message);
                } else {
                    return back()->withErrors(['excel_file' => 'Error importing file.']);
                }
            }
        } catch (\Exception $e) {
            Log::error('Import error', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            return back()->withErrors(['excel_file' => 'Error importing file: ' . $e->getMessage()]);
        }
    }

    public function syncFromCsv(Request $request)
    {
        $this->checkAdminAccess();
        
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt'
        ]);

        try {
            $file = $request->file('excel_file');
            $fileExtension = $file->getClientOriginalExtension();
            
            if (in_array($fileExtension, ['xlsx', 'xls'])) {
                // Handle Excel files using XlsxImportService
                $xlsxService = new XlsxImportService();
                $result = $xlsxService->importFromXlsx($file->getRealPath());
                
                if ($result['success']) {
                    $message = "Sync completed! Created: {$result['created']}, Updated: {$result['updated']}, Errors: {$result['errors']}";
                    return redirect()->route('admin.students')->with('success', $message);
                } else {
                    $errorMessage = isset($result['message']) ? $result['message'] : 'Error syncing Excel file.';
                    return back()->withErrors(['excel_file' => $errorMessage]);
                }
            } else {
                // Handle CSV files using existing service
                $filePath = $file->getRealPath();
                $csvService = new CsvImportService();
                $result = $csvService->importFromCsv($filePath);
                
                if ($result['success']) {
                    $message = "Sync completed! Created: {$result['created']}, Updated: {$result['updated']}, Errors: {$result['errors']}";
                    return redirect()->route('admin.students')->with('success', $message);
                } else {
                    return back()->withErrors(['excel_file' => 'Error syncing file.']);
                }
            }
        } catch (\Exception $e) {
            Log::error('Sync error', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            return back()->withErrors(['excel_file' => 'Error syncing file: ' . $e->getMessage()]);
        }
    }

    public function createStudent()
    {
        $this->checkAdminAccess();
        return view('admin.create-student');
    }

    public function storeStudent(Request $request)
    {
        $this->checkAdminAccess();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'ic' => 'required|string|unique:users,ic',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'previous_university' => 'nullable|string',
            'col_ref_no' => 'nullable|string',
            'student_id' => 'nullable|string',
            'courses' => 'nullable|string',
        ]);

        $courses = [];
        if (!empty($request->courses)) {
            $courses = array_map('trim', explode(',', $request->courses));
        }

        User::create([
            'name' => $request->name,
            'ic' => $request->ic,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'previous_university' => $request->previous_university,
            'col_ref_no' => $request->col_ref_no,
            'student_id' => $request->student_id,
            'password' => Hash::make($request->ic), // Use IC as password
            'role' => 'student',
            'courses' => $courses,
        ]);

        return redirect()->route('admin.students')->with('success', 'Student created successfully!');
    }

    public function editStudent(User $student)
    {
        $this->checkAdminAccess();
        
        Log::info('Edit student accessed', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'student_role' => $student->role
        ]);
        
        if ($student->role !== 'student') {
            Log::warning('Attempt to edit non-student user', [
                'user_id' => $student->id,
                'user_role' => $student->role
            ]);
            abort(404);
        }
        
        return view('admin.edit-student', compact('student'));
    }

    public function updateStudent(Request $request, User $student)
    {
        $this->checkAdminAccess();
        
        if ($student->role !== 'student') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'ic' => 'required|string|unique:users,ic,' . $student->id,
            'email' => 'required|email|unique:users,email,' . $student->id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'previous_university' => 'nullable|string',
            'col_ref_no' => 'nullable|string',
            'student_id' => 'nullable|string',
            'courses' => 'nullable|string',
        ]);

        $courses = [];
        if (!empty($request->courses)) {
            $courses = array_map('trim', explode(',', $request->courses));
        }

        $student->update([
            'name' => $request->name,
            'ic' => $request->ic,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'previous_university' => $request->previous_university,
            'col_ref_no' => $request->col_ref_no,
            'student_id' => $request->student_id,
            'courses' => $courses,
        ]);

        return redirect()->route('admin.students')->with('success', 'Student updated successfully!');
    }

    public function deleteStudent(User $student)
    {
        $this->checkAdminAccess();
        
        if ($student->role !== 'student') {
            abort(404);
        }

        try {
            $studentName = $student->name;
            $student->delete();
            
            Log::info('Student deleted successfully', [
                'student_id' => $student->id,
                'student_name' => $studentName,
                'deleted_by' => Auth::user()->name
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting student', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting student: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDeleteStudents(Request $request)
    {
        $this->checkAdminAccess();
        
        Log::info('Bulk delete request received', [
            'student_ids' => $request->input('student_ids'),
            'all_input' => $request->all()
        ]);
        
        try {
            $request->validate([
                'student_ids' => 'required|array|min:1',
                'student_ids.*' => 'integer|exists:users,id'
            ]);

            $studentIds = $request->input('student_ids');
            $deletedCount = 0;

            Log::info('Processing bulk delete', ['student_ids' => $studentIds]);

            foreach ($studentIds as $studentId) {
                $student = User::find($studentId);
                if ($student && $student->role === 'student') {
                    Log::info('Deleting student', ['id' => $studentId, 'name' => $student->name]);
                    $student->delete();
                    $deletedCount++;
                }
            }

            $message = $deletedCount === 1 
                ? '1 student deleted successfully!' 
                : "{$deletedCount} students deleted successfully!";

            Log::info('Bulk delete completed', ['deleted_count' => $deletedCount]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting students: ' . $e->getMessage()
            ], 500);
        }
    }

    public function googleSheetsImport(Request $request)
    {
        $this->checkAdminAccess();
        
        try {
            $googleSheetsService = new GoogleSheetsImportService();
            $result = $googleSheetsService->importFromGoogleSheets();
            
            if ($result['success']) {
                // Update cache with results
                cache()->put('last_google_sheets_import_time', now(), now()->addDays(30));
                cache()->put('last_google_sheets_import_results', $result, now()->addDays(30));
                cache()->put('google_sheets_automation_last_check', now(), now()->addDays(30));
                
                return response()->json([
                    'success' => true,
                    'message' => 'Google Sheets import completed successfully!',
                    'created' => $result['created'],
                    'updated' => $result['updated'],
                    'errors' => $result['errors']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Sheets import failed: ' . ($result['message'] ?? 'Unknown error'),
                    'created' => $result['created'] ?? 0,
                    'updated' => $result['updated'] ?? 0,
                    'errors' => $result['errors'] ?? 0
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Google Sheets import error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error during import: ' . $e->getMessage(),
                'created' => 0,
                'updated' => 0,
                'errors' => 1
            ]);
        }
    }

    public function oneDriveImport(Request $request)
    {
        $this->checkAdminAccess();
        
        // Increase execution time limit and memory for OneDrive import
        set_time_limit(300); // 5 minutes timeout
        ini_set('memory_limit', '512M'); // Increase memory limit to 512MB
        ini_set('max_execution_time', 300); // 5 minutes execution time limit
        ini_set('max_input_time', 300); // 5 minutes input time limitimage.png  yes
        
        try {
            Log::info('OneDrive import request received', [
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'time_limit' => ini_get('time_limit')
            ]);
            
            $googleDriveService = new \App\Services\GoogleDriveImportService();
            
            // Skip connection test and go straight to import to avoid double download
            Log::info('Starting Google Drive import from admin panel');
            
            $result = $googleDriveService->importFromGoogleDrive();
            
            Log::info('Google Drive import completed', $result);
            
            if ($result['success']) {
                // Update cache with results
                cache()->put('last_google_drive_import_time', now(), now()->addDays(30));
                cache()->put('last_google_drive_import_results', $result, now()->addDays(30));
                cache()->put('last_google_drive_sync', now(), now()->addDays(30));
                
                return response()->json([
                    'success' => true,
                    'message' => 'Google Drive import completed successfully!',
                    'created' => $result['created'],
                    'updated' => $result['updated'],
                    'errors' => $result['errors'],
                    'processed_sheets' => $result['processed_sheets'] ?? []
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'OneDrive import failed: ' . ($result['error'] ?? 'Unknown error'),
                    'created' => $result['created'] ?? 0,
                    'updated' => $result['updated'] ?? 0,
                    'errors' => $result['errors'] ?? 0
                ]);
            }
        } catch (\Exception $e) {
            Log::error('OneDrive import error: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if it's a timeout error
            if (strpos($e->getMessage(), 'timeout') !== false || strpos($e->getMessage(), 'Maximum execution time') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'OneDrive import timed out. The file might be too large or the connection is slow. Please try again or contact support.',
                    'created' => 0,
                    'updated' => 0,
                    'errors' => 1
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error during import: ' . $e->getMessage(),
                'created' => 0,
                'updated' => 0,
                'errors' => 1
            ]);
        }
    }

    public function automation()
    {
        $this->checkAdminAccess();
        
        // Get Google Sheets automation status
        $automationConfig = $this->getGoogleSheetsConfig();
        $lastImportTime = cache()->get('last_google_sheets_import_time', null);
        $totalStudents = User::where('role', 'student')->count();
        
        // Check if Google Sheets automation is running
        $automationStatus = cache()->get('google_sheets_automation_status', 'stopped');
        $isRunning = $automationStatus === 'running';
        
        // Get last import results
        $lastImportResults = cache()->get('last_google_sheets_import_results', null);
        
        // Get recent import logs
        $recentLogs = $this->getRecentGoogleSheetsLogs();
        
        // Google Sheets specific data
        $googleSheetsUrl = 'https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk/edit?usp=sharing&ouid=117738643589016699947&rtpof=true&sd=true';
        $sheetsStatus = $this->checkGoogleSheetsStatus();
        
        // Run automation check if it's running and enough time has passed
        if ($isRunning) {
            $this->runWebOnlyAutomation();
        }
        
        return view('admin.automation', compact(
            'automationConfig',
            'lastImportTime', 
            'totalStudents',
            'recentLogs',
            'isRunning',
            'lastImportResults',
            'googleSheetsUrl',
            'sheetsStatus'
        ));
    }
    
    private function getGoogleSheetsConfig()
    {
        $configPath = storage_path('app/google_sheets_automation.json');
        if (file_exists($configPath)) {
            return json_decode(file_get_contents($configPath), true);
        }
        
        return [
            'google_sheets_url' => 'https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk/edit?usp=sharing&ouid=117738643589016699947&rtpof=true&sd=true',
            'notification_email' => 'ikramalif.roslee@gmail.com',
            'import_frequency' => 'every-5-minutes',
            'status' => 'Enabled',
            'check_interval' => 300
        ];
    }
    
    private function checkGoogleSheetsStatus()
    {
        // Use cached status to avoid slow API calls on every page load
        $cachedStatus = cache()->get('google_sheets_connection_status', null);
        
        // Only check if cache is older than 5 minutes
        if (!$cachedStatus || now()->diffInMinutes($cachedStatus['last_check']) > 5) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->get('https://docs.google.com/spreadsheets/d/1pacM1tauMvbQEWb9cNH8VaeRz0q44CSk/export?format=csv');
                
                $status = [
                    'status' => $response->successful() ? 'Connected' : 'Connection Failed',
                    'last_check' => now()->format('Y-m-d H:i:s'),
                    'response_size' => $response->successful() ? strlen($response->body()) : 0,
                    'error' => $response->successful() ? null : 'HTTP ' . $response->status()
                ];
                
                // Cache the status for 5 minutes
                cache()->put('google_sheets_connection_status', $status, now()->addMinutes(5));
                
                return $status;
            } catch (\Exception $e) {
                $status = [
                    'status' => 'Error',
                    'last_check' => now()->format('Y-m-d H:i:s'),
                    'error' => $e->getMessage()
                ];
                
                // Cache the error status for 2 minutes
                cache()->put('google_sheets_connection_status', $status, now()->addMinutes(2));
                
                return $status;
            }
        }
        
        return $cachedStatus;
    }
    
    private function getRecentGoogleSheetsLogs()
    {
        // Use cached logs to avoid reading large log files on every page load
        $cachedLogs = cache()->get('google_sheets_recent_logs', []);
        $lastLogCheck = cache()->get('google_sheets_last_log_check', null);
        
        // Only read logs if cache is older than 2 minutes
        if (!$lastLogCheck || now()->diffInMinutes($lastLogCheck) > 2) {
            $logs = [];
            $logFile = storage_path('logs/laravel.log');
            
            if (file_exists($logFile)) {
                // Read only the last 1000 lines to avoid memory issues
                $lines = array_slice(file($logFile, FILE_IGNORE_NEW_LINES), -1000);
                
                // Get last 20 lines that contain Google Sheets import info
                $importLines = array_filter($lines, function($line) {
                    return strpos($line, 'Google Sheets') !== false || 
                           strpos($line, 'google_sheets') !== false ||
                           strpos($line, 'GoogleSheetsImportService') !== false ||
                           strpos($line, 'Google Sheets import') !== false;
                });
                
                $recentLines = array_slice(array_reverse($importLines), 0, 15);
                
                foreach ($recentLines as $line) {
                    if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?(\w+):\s*(.+)/', $line, $matches)) {
                        $timestamp = $matches[1];
                        $level = strtolower($matches[2]);
                        $message = trim($matches[3]);
                        
                        // Convert to Malaysia timezone for display
                        try {
                            $malaysiaTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestamp)
                                ->setTimezone('Asia/Kuala_Lumpur')
                                ->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            $malaysiaTime = $timestamp;
                        }
                        
                        // Create professional message
                        $professionalMessage = $this->createGoogleSheetsProfessionalMessage($message, $level);
                        
                        $logs[] = [
                            'time' => $malaysiaTime,
                            'level' => $level,
                            'message' => $professionalMessage,
                            'raw_message' => $message
                        ];
                    }
                }
            }
            
            // Cache the logs for 2 minutes
            cache()->put('google_sheets_recent_logs', $logs, now()->addMinutes(2));
            cache()->put('google_sheets_last_log_check', now(), now()->addMinutes(2));
            
            return $logs;
        }
        
        return $cachedLogs;
    }
    
    private function getRecentImportLogs()
    {
        // Read recent log entries related to auto import
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $lines = explode("\n", $logContent);
            
            // Get last 20 lines that contain import info
            $importLines = array_filter($lines, function($line) {
                return strpos($line, 'Student Import') !== false || 
                       strpos($line, 'Auto import') !== false ||
                       strpos($line, 'students:auto-import') !== false ||
                       strpos($line, 'AutoImportStudents') !== false;
            });
            
            $recentLines = array_slice(array_reverse($importLines), 0, 15);
            
            foreach ($recentLines as $line) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?(\w+):\s*(.+)/', $line, $matches)) {
                    $timestamp = $matches[1];
                    $level = strtolower($matches[2]);
                    $message = trim($matches[3]);
                    
                    // Convert to Malaysia timezone for display
                    try {
                        $malaysiaTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestamp)
                            ->setTimezone('Asia/Kuala_Lumpur')
                            ->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        $malaysiaTime = $timestamp;
                    }
                    
                    // Create professional message
                    $professionalMessage = $this->createProfessionalMessage($message, $level);
                    
                    $logs[] = [
                        'time' => $malaysiaTime,
                        'level' => $level,
                        'message' => $professionalMessage,
                        'raw_message' => $message
                    ];
                }
            }
        }
        
        return $logs;
    }
    
    private function createProfessionalMessage($message, $level)
    {
        // Create professional messages based on log content
        if (strpos($message, 'Student Import Automation:') !== false) {
            return $message; // Already professional
        }
        
        if (strpos($message, 'Student Import System:') !== false) {
            return $message; // Already professional
        }
        
        if (strpos($message, 'Auto import completed') !== false) {
            return "Student Import System: Import process completed successfully";
        }
        
        if (strpos($message, 'Processing sheet:') !== false) {
            $sheetName = str_replace('Processing sheet: ', '', $message);
            return "Processing student data from {$sheetName} sheet";
        }
        
        if (strpos($message, 'File change detected') !== false) {
            return "System Alert: Excel file modification detected - initiating import process";
        }
        
        if (strpos($message, 'No changes detected') !== false) {
            return "System Status: No student data changes detected in Excel file";
        }
        
        if (strpos($message, 'Import failed') !== false) {
            return "System Error: Student import process failed - please check system logs";
        }
        
        // Default professional message
        return "System Activity: " . ucfirst(strtolower($message));
    }
    
    private function createGoogleSheetsProfessionalMessage($message, $level)
    {
        // Create professional messages based on Google Sheets log content
        if (strpos($message, 'Google Sheets import') !== false) {
            return $message; // Already professional
        }
        
        if (strpos($message, 'Starting Google Sheets import') !== false) {
            return "Google Sheets Integration: Starting data import from Google Sheets";
        }
        
        if (strpos($message, 'Google Sheets data fetched successfully') !== false) {
            return "Google Sheets Integration: Data successfully retrieved from Google Sheets";
        }
        
        if (strpos($message, 'Student created from Google Sheets') !== false) {
            return "Google Sheets Integration: New student record created from Google Sheets data";
        }
        
        if (strpos($message, 'Student updated from Google Sheets') !== false) {
            return "Google Sheets Integration: Existing student record updated from Google Sheets data";
        }
        
        if (strpos($message, 'Failed to fetch Google Sheets data') !== false) {
            return "Google Sheets Integration: Error - Unable to retrieve data from Google Sheets";
        }
        
        if (strpos($message, 'No data found in Google Sheets') !== false) {
            return "Google Sheets Integration: Warning - No student data found in Google Sheets";
        }
        
        // Default professional message
        return "Google Sheets Integration: " . ucfirst(strtolower($message));
    }
    
    public function runAutomationCheck()
    {
        $this->checkAdminAccess();
        
        try {
            $this->runWebBasedAutomation();
            
            $lastCheck = cache()->get('google_sheets_automation_last_check', null);
            $lastImport = cache()->get('last_google_sheets_import_time', null);
            $lastResults = cache()->get('last_google_sheets_import_results', null);
            
            return response()->json([
                'success' => true,
                'message' => 'Automation check completed',
                'last_check' => $lastCheck ? \Carbon\Carbon::parse($lastCheck)->format('Y-m-d H:i:s') : null,
                'last_import' => $lastImport ? \Carbon\Carbon::parse($lastImport)->format('Y-m-d H:i:s') : null,
                'last_results' => $lastResults
            ]);
            
        } catch (\Exception $e) {
            Log::error('Automation check error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error running automation check: ' . $e->getMessage()
            ]);
        }
    }
    
    private function runWebOnlyAutomation()
    {
        // Check if automation is enabled
        $isRunning = cache()->get('google_sheets_automation_status', 'stopped') === 'running';
        if (!$isRunning) {
            return;
        }
        
        // Check if enough time has passed since last check
        $lastCheck = cache()->get('google_sheets_automation_last_check', null);
        $interval = cache()->get('google_sheets_automation_interval', 300); // 5 minutes default
        
        if ($lastCheck && now()->diffInSeconds($lastCheck) < $interval) {
            return; // Not enough time has passed
        }
        
        try {
            Log::info('Web-only automation: Running Google Sheets import');
            
            $googleSheetsService = new GoogleSheetsImportService();
            $result = $googleSheetsService->importFromGoogleSheets();
            
            if ($result['success']) {
                cache()->put('last_google_sheets_import_time', now(), now()->addDays(30));
                cache()->put('last_google_sheets_import_results', $result, now()->addDays(30));
                Log::info('Web-only automation: Import completed successfully', $result);
            } else {
                Log::error('Web-only automation: Import failed', $result);
            }
            
            // Update last check time
            cache()->put('google_sheets_automation_last_check', now(), now()->addDays(30));
            
        } catch (\Exception $e) {
            Log::error('Web-only automation error: ' . $e->getMessage());
        }
    }

    private function runSimpleAutomation()
    {
        // Check if automation is enabled
        $isRunning = cache()->get('google_sheets_automation_status', 'stopped') === 'running';
        if (!$isRunning) {
            return;
        }
        
        // Check if enough time has passed since last check
        $lastCheck = cache()->get('google_sheets_automation_last_check', null);
        $interval = cache()->get('google_sheets_automation_interval', 300); // 5 minutes default
        
        if ($lastCheck && now()->diffInSeconds($lastCheck) < $interval) {
            return; // Not enough time has passed
        }
        
        try {
            Log::info('Simple automation: Running Google Sheets import');
            
            $googleSheetsService = new GoogleSheetsImportService();
            $result = $googleSheetsService->importFromGoogleSheets();
            
            if ($result['success']) {
                cache()->put('last_google_sheets_import_time', now(), now()->addDays(30));
                cache()->put('last_google_sheets_import_results', $result, now()->addDays(30));
                Log::info('Simple automation: Import completed successfully', $result);
            } else {
                Log::error('Simple automation: Import failed', $result);
            }
            
            // Update last check time
            cache()->put('google_sheets_automation_last_check', now(), now()->addDays(30));
            
        } catch (\Exception $e) {
            Log::error('Simple automation error: ' . $e->getMessage());
        }
    }

    private function runWebBasedAutomation()
    {
        // Check if automation is enabled
        $isRunning = cache()->get('google_sheets_automation_status', 'stopped') === 'running';
        if (!$isRunning) {
            return;
        }
        
        // Check if enough time has passed since last check
        $lastCheck = cache()->get('google_sheets_automation_last_check', null);
        $interval = cache()->get('google_sheets_automation_interval', 300); // 5 minutes default
        
        if ($lastCheck && now()->diffInSeconds($lastCheck) < $interval) {
            return; // Not enough time has passed
        }
        
        try {
            Log::info('Web-based automation: Running Google Sheets check');
            
            $googleSheetsService = new GoogleSheetsImportService();
            
            // Check for changes
            if ($googleSheetsService->checkForChanges()) {
                Log::info('Web-based automation: Changes detected, running import');
                
                $result = $googleSheetsService->importFromGoogleSheets();
                
                if ($result['success']) {
                    cache()->put('last_google_sheets_import_time', now(), now()->addDays(30));
                    cache()->put('last_google_sheets_import_results', $result, now()->addDays(30));
                    
                    $totalImports = cache()->get('google_sheets_total_imports', 0);
                    cache()->put('google_sheets_total_imports', $totalImports + 1, now()->addDays(30));
                    
                    Log::info('Web-based automation: Import completed successfully', $result);
                } else {
                    cache()->put('google_sheets_last_error', $result['message'] ?? 'Unknown error', now()->addDays(30));
                    Log::error('Web-based automation: Import failed', $result);
                }
            } else {
                Log::info('Web-based automation: No changes detected');
            }
            
            // Update last check time
            cache()->put('google_sheets_automation_last_check', now(), now()->addDays(30));
            
        } catch (\Exception $e) {
            cache()->put('google_sheets_last_error', $e->getMessage(), now()->addDays(30));
            Log::error('Web-based automation: Error during check', ['error' => $e->getMessage()]);
        }
    }

    public function triggerImport(Request $request)
    {
        $this->checkAdminAccess();
        
        try {
            $email = $request->input('email');
            
            // Use Google Sheets import service
            $googleSheetsService = new GoogleSheetsImportService();
            $result = $googleSheetsService->importFromGoogleSheets();
            
            if ($result['success']) {
                // Update cache with import results
                cache()->put('last_google_sheets_import_time', now(), now()->addDays(30));
                cache()->put('last_google_sheets_import_results', $result, now()->addDays(30));
                
                return response()->json([
                    'success' => true,
                    'message' => 'Google Sheets import completed successfully!',
                    'created' => $result['created'],
                    'updated' => $result['updated'],
                    'errors' => $result['errors']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Sheets import failed: ' . ($result['message'] ?? 'Unknown error'),
                    'created' => $result['created'] ?? 0,
                    'updated' => $result['updated'] ?? 0,
                    'errors' => $result['errors'] ?? 1
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Google Sheets import error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Import error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function saveAutomationSettings(Request $request)
    {
        $this->checkAdminAccess();
        
        $request->validate([
            'google_sheets_url' => 'required|string',
            'notification_email' => 'required|email',
            'import_frequency' => 'required|string',
            'check_interval' => 'required|integer|min:60',
            'email_notifications' => 'boolean',
            'update_existing' => 'boolean'
        ]);
        
        try {
            $config = [
                'google_sheets_url' => $request->google_sheets_url,
                'notification_email' => $request->notification_email,
                'import_frequency' => $request->import_frequency,
                'check_interval' => $request->check_interval,
                'email_notifications' => $request->boolean('email_notifications'),
                'update_existing' => $request->boolean('update_existing'),
                'status' => 'Enabled',
                'updated_at' => now()->toDateTimeString()
            ];
            
            $configPath = storage_path('app/google_sheets_automation.json');
            file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT));
            
            return response()->json([
                'success' => true,
                'message' => 'Google Sheets automation settings saved successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving settings: ' . $e->getMessage()
            ]);
        }
    }
    
    public function checkFileStatus(Request $request)
    {
        $this->checkAdminAccess();
        
        $filePath = $request->input('file_path', storage_path('app/students/Enrollment OEM.xlsx'));
        $fileExists = file_exists($filePath);
        
        if ($fileExists) {
            $fileSize = filesize($filePath);
            $lastModified = filemtime($filePath);
            
            return response()->json([
                'success' => true,
                'status' => 'File Found',
                'file_size' => $fileSize,
                'last_modified' => date('Y-m-d H:i:s', $lastModified),
                'file_path' => $filePath
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => 'File Not Found',
                'file_path' => $filePath
            ]);
        }
    }
    
    public function startGoogleSheetsAutomation(Request $request)
    {
        $this->checkAdminAccess();
        
        try {
            $interval = $request->input('interval', 300); // 5 minutes default
            
            // Set status in cache
            cache()->put('google_sheets_automation_status', 'running', now()->addDays(30));
            cache()->put('google_sheets_automation_interval', $interval, now()->addDays(30));
            cache()->put('google_sheets_automation_last_check', now(), now()->addDays(30));
            
            // Start continuous automation process
            $this->startContinuousAutomation();
            
            // Run an immediate check to test the connection
            $googleSheetsService = new GoogleSheetsImportService();
            $testResult = $googleSheetsService->importFromGoogleSheets();
            
            if ($testResult['success']) {
                cache()->put('last_google_sheets_import_time', now(), now()->addDays(30));
                cache()->put('last_google_sheets_import_results', $testResult, now()->addDays(30));
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Google Sheets automation started successfully! Continuous monitoring is now active and will check every 5 minutes.',
                'test_result' => $testResult
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error starting Google Sheets automation: ' . $e->getMessage()
            ]);
        }
    }
    
    private function startContinuousAutomation()
    {
        try {
            // Check if continuous automation is already running
            if (PHP_OS_FAMILY === 'Windows') {
                $command = 'tasklist /FI "IMAGENAME eq php.exe" /FI "COMMANDLINE eq *continuous_automation*" 2>nul';
                $output = shell_exec($command);
                
                if (strpos($output, 'php.exe') !== false) {
                    Log::info('Continuous automation is already running');
                    return;
                }
                
                // Start continuous automation in background
                $scriptPath = base_path('automation/scripts/continuous_automation.php');
                $command = "start /B php \"{$scriptPath}\"";
                pclose(popen($command, 'r'));
                
                Log::info('Continuous automation started successfully');
            } else {
                // Linux/Unix
                $command = "pgrep -f 'continuous_automation'";
                $output = shell_exec($command);
                
                if (!empty(trim($output))) {
                    Log::info('Continuous automation is already running');
                    return;
                }
                
                // Start continuous automation in background
                $scriptPath = base_path('automation/scripts/continuous_automation.php');
                $command = "php \"{$scriptPath}\" > /dev/null 2>&1 &";
                shell_exec($command);
                
                Log::info('Continuous automation started successfully');
            }
        } catch (\Exception $e) {
            Log::error('Failed to start continuous automation: ' . $e->getMessage());
        }
    }
    
    public function stopGoogleSheetsAutomation(Request $request)
    {
        $this->checkAdminAccess();
        
        try {
            // Set status to stopped
            cache()->put('google_sheets_automation_status', 'stopped', now()->addDays(30));
            
            // Kill any running Google Sheets automation processes
            if (PHP_OS_FAMILY === 'Windows') {
                exec('taskkill /F /IM php.exe /FI "COMMANDLINE eq *google_sheets_automation_watcher*" 2>nul');
                exec('taskkill /F /IM php.exe /FI "COMMANDLINE eq *continuous_automation*" 2>nul');
            } else {
                exec('pkill -f "google_sheets_automation_watcher" 2>/dev/null');
                exec('pkill -f "continuous_automation" 2>/dev/null');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Google Sheets automation stopped successfully! All continuous processes have been terminated.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error stopping Google Sheets automation: ' . $e->getMessage()
            ]);
        }
    }

    // Automation Web Interface Methods
    // Legacy automation methods removed - using auto-sync instead

    public function startAutomation(Request $request)
    {
        $this->checkAdminAccess();
        
        $type = $request->input('type', 'google_sheets');
        
        try {
            $command = "php " . base_path("automation/scripts/automation_manager.php") . " start {$type}";
            
            if (PHP_OS_FAMILY === 'Windows') {
                $command = "start /B " . $command;
                pclose(popen($command, 'r'));
            } else {
                $command .= " > /dev/null 2>&1 &";
                shell_exec($command);
            }
            
            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' automation started successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start automation: ' . $e->getMessage()
            ]);
        }
    }

    public function stopAutomation()
    {
        $this->checkAdminAccess();
        
        try {
            // Stop Google Sheets automation
            cache()->put('google_sheets_automation_status', 'stopped', now()->addDays(30));
            
            // Kill any running Google Sheets automation processes
            if (PHP_OS_FAMILY === 'Windows') {
                exec('taskkill /F /IM php.exe /FI "COMMANDLINE eq *google_sheets_automation_watcher*" 2>nul');
            } else {
                exec('pkill -f "google_sheets_automation_watcher" 2>/dev/null');
            }
            
            // Also try to stop legacy automation
            $command = "php " . base_path("automation/scripts/automation_manager.php") . " stop";
            $output = shell_exec($command);
            
            return response()->json([
                'success' => true,
                'message' => 'All automation stopped successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to stop automation: ' . $e->getMessage()
            ]);
        }
    }

    public function testGoogleSheetsAutomation(Request $request)
    {
        $this->checkAdminAccess();
        
        try {
            $googleSheetsService = new GoogleSheetsImportService();
            $result = $googleSheetsService->importFromGoogleSheets();
            
            Log::info('Google Sheets test completed', $result);
            
            // Update cache with test results
            cache()->put('last_google_sheets_import_time', now(), now()->addDays(30));
            cache()->put('last_google_sheets_import_results', $result, now()->addDays(30));
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Google Sheets test completed successfully!' : 'Google Sheets test failed: ' . ($result['message'] ?? 'Unknown error'),
                'created' => $result['created'] ?? 0,
                'updated' => $result['updated'] ?? 0,
                'errors' => $result['errors'] ?? 0,
                'result' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('Google Sheets test failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ]);
        }
    }

    public function automationLogs()
    {
        $this->checkAdminAccess();
        
        try {
            $logFile = base_path('automation/logs/google_sheets_automation.log');
            $logs = '';
            
            if (file_exists($logFile)) {
                $logs = file_get_contents($logFile);
                // Get last 50 lines
                $lines = explode("\n", $logs);
                $logs = implode("\n", array_slice($lines, -50));
            } else {
                $logs = 'No logs available yet.';
            }
            
            return response()->json([
                'logs' => $logs
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'logs' => 'Error loading logs: ' . $e->getMessage()
            ]);
        }
    }

    // Legacy OneDrive automation method removed - using auto-sync instead

    public function testOneDriveConnection()
    {
        $this->checkAdminAccess();
        
        try {
            $oneDriveService = new \App\Services\OneDriveExcelImportService();
            $result = $oneDriveService->testConnection();
            
            // Also test download speed
            $startTime = microtime(true);
            $downloadResult = $oneDriveService->testConnection();
            $endTime = microtime(true);
            $downloadTime = round($endTime - $startTime, 2);
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'error' => $result['error'] ?? null,
                'onedrive_url' => config('google_sheets.onedrive_url'),
                'env_url' => env('ONEDRIVE_EXCEL_URL'),
                'download_test' => [
                    'success' => $downloadResult['success'],
                    'time_seconds' => $downloadTime,
                    'error' => $downloadResult['error'] ?? null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ]);
        }
    }

    // Auto-sync methods
    public function startAutoSync(Request $request)
    {
        $this->checkAdminAccess();
        
        // Increase execution time for auto-sync
        set_time_limit(300); // 5 minutes
        
        try {
            $syncService = new \App\Services\GoogleDriveImportService();
            $result = $syncService->importFromGoogleDrive();
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'new_students' => $result['new_students'] ?? 0,
                'created' => $result['created'] ?? 0,
                'updated' => $result['updated'] ?? 0,
                'errors' => $result['errors'] ?? 0
            ]);
        } catch (\Exception $e) {
            Log::error('Auto-sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Auto-sync failed: ' . $e->getMessage()
            ]);
        }
    }

    public function stopAutoSync(Request $request)
    {
        $this->checkAdminAccess();
        
        // Clear sync cache to effectively "stop" auto-sync
        \Illuminate\Support\Facades\Cache::forget('last_google_drive_sync');
        \Illuminate\Support\Facades\Cache::forget('last_google_drive_file_hash');
        
        return response()->json([
            'success' => true,
            'message' => 'Auto-sync stopped successfully'
        ]);
    }

    public function getAutoSyncStatus(Request $request)
    {
        $this->checkAdminAccess();
        
        try {
            Log::info('Getting auto-sync status');
            // For Google Drive, we'll return a simple status since we don't have a dedicated sync service
            $lastSync = \Illuminate\Support\Facades\Cache::get('last_google_drive_sync');
            $status = [
                'is_running' => false,
                'last_sync' => $lastSync ? $lastSync->format('Y-m-d H:i:s') : 'Never',
                'next_sync' => 'Manual only',
                'file_hash' => 'Google Drive file',
                'sync_interval' => 5
            ];
            
            Log::info('Auto-sync status retrieved', ['status' => $status]);
            
            return response()->json([
                'success' => true,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get auto-sync status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get sync status: ' . $e->getMessage()
            ]);
        }
    }

    public function forceAutoSync(Request $request)
    {
        $this->checkAdminAccess();
        
        // Increase execution time for force sync
        set_time_limit(300); // 5 minutes
        
        try {
            $syncService = new \App\Services\GoogleDriveImportService();
            $result = $syncService->importFromGoogleDrive();
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'new_students' => $result['new_students'] ?? 0,
                'created' => $result['created'] ?? 0,
                'updated' => $result['updated'] ?? 0,
                'errors' => $result['errors'] ?? 0
            ]);
        } catch (\Exception $e) {
            Log::error('Force sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Force sync failed: ' . $e->getMessage()
            ]);
        }
    }

    public function setAutoSyncInterval(Request $request)
    {
        $this->checkAdminAccess();
        
        $request->validate([
            'interval' => 'required|integer|min:1|max:60'
        ]);
        
        try {
            $syncService = new \App\Services\AutoOneDriveSyncService();
            $syncService->setSyncInterval($request->input('interval'));
            
            return response()->json([
                'success' => true,
                'message' => 'Sync interval updated to ' . $request->input('interval') . ' minutes'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set sync interval: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * HTTP Cron endpoint for external cron services
     * No authentication required - for external cron services
     */
    public function oneDriveImportCron(Request $request)
    {
        // Set execution time limit for cron import
        set_time_limit(300); // 5 minutes
        
        try {
            Log::info('HTTP Cron: Starting Google Drive import');
            
            // Use the new Google Drive import service
            $googleDriveService = new \App\Services\GoogleDriveImportService();
            $result = $googleDriveService->importFromGoogleDrive();
            
            Log::info('HTTP Cron: Import completed', $result);
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'timestamp' => now()->toISOString(),
                'new_students' => $result['new_students'] ?? 0,
                'created' => $result['created'] ?? 0,
                'updated' => $result['updated'] ?? 0,
                'errors' => $result['errors'] ?? 0
            ]);
        } catch (\Exception $e) {
            Log::error('HTTP Cron: Import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    public function setupAutomation(Request $request)
    {
        $this->checkAdminAccess();
        
        $action = $request->input('action');
        
        try {
            switch ($action) {
                case 'test_import':
                    // Test the import command
                    $output = [];
                    $returnCode = 0;
                    exec('php artisan import:onedrive-auto 2>&1', $output, $returnCode);
                    
                    return response()->json([
                        'success' => $returnCode === 0,
                        'message' => $returnCode === 0 ? 'Import test successful!' : 'Import test failed',
                        'output' => implode("\n", $output),
                        'return_code' => $returnCode
                    ]);
                    
                case 'create_task':
                    // Create Windows Task Scheduler task
                    $taskName = "LMS_OneDrive_AutoImport";
                    $batchFile = base_path('automation/batch/auto_onedrive_import.bat');
                    
                    $command = "schtasks /create /tn \"$taskName\" /tr \"$batchFile\" /sc minute /mo 5 /ru SYSTEM /f";
                    exec($command, $output, $returnCode);
                    
                    return response()->json([
                        'success' => $returnCode === 0,
                        'message' => $returnCode === 0 ? 'Task created successfully!' : 'Failed to create task. You may need to run as administrator.',
                        'output' => implode("\n", $output),
                        'command' => $command
                    ]);
                    
                case 'check_status':
                    // Check if task exists
                    $taskName = "LMS_OneDrive_AutoImport";
                    exec("schtasks /query /tn \"$taskName\" 2>&1", $output, $returnCode);
                    
                    return response()->json([
                        'success' => $returnCode === 0,
                        'message' => $returnCode === 0 ? 'Task is active' : 'Task not found',
                        'output' => implode("\n", $output),
                        'is_active' => $returnCode === 0
                    ]);
                    
                case 'delete_task':
                    // Delete the task
                    $taskName = "LMS_OneDrive_AutoImport";
                    exec("schtasks /delete /tn \"$taskName\" /f 2>&1", $output, $returnCode);
                    
                    return response()->json([
                        'success' => $returnCode === 0,
                        'message' => $returnCode === 0 ? 'Task deleted successfully!' : 'Failed to delete task',
                        'output' => implode("\n", $output)
                    ]);
                    
                case 'create_simple_automation':
                    // Create simple automation files (no admin required)
                    try {
                        $projectPath = base_path();
                        
                        // Create batch file
                        $batchContent = '@echo off
echo Starting automated OneDrive import...
echo Time: %date% %time%

cd /d "' . $projectPath . '"

php artisan import:onedrive-auto

echo Automated OneDrive import completed.
echo Time: %date% %time%
echo.
pause';
                        
                        file_put_contents($projectPath . '/onedrive_auto_import.bat', $batchContent);
                        
                        // Create PHP script
                        $phpContent = '<?php
/**
 * OneDrive Auto Import Runner
 * Run this script to import students from OneDrive
 */

echo "Starting OneDrive import...\n";

// Change to the project directory
chdir("' . $projectPath . '");

// Run the import command
$output = [];
$returnCode = 0;
exec("php artisan import:onedrive-auto 2>&1", $output, $returnCode);

echo "Import completed with return code: $returnCode\n";
echo "Output:\n" . implode("\n", $output) . "\n";

if ($returnCode === 0) {
    echo "✅ Import successful!\n";
} else {
    echo "❌ Import failed!\n";
}';
                        
                        file_put_contents($projectPath . '/onedrive_import_runner.php', $phpContent);
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Simple automation files created successfully!',
                            'output' => "Files created:\n- onedrive_auto_import.bat (double-click to run)\n- onedrive_import_runner.php (run with: php onedrive_import_runner.php)\n\nTo schedule automatically:\n1. Open Windows Task Scheduler\n2. Create Basic Task\n3. Set trigger to 'Every 5 minutes'\n4. Set action to run one of the created files"
                        ]);
                        
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to create simple automation files: ' . $e->getMessage()
                        ]);
                    }
                    
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid action'
                    ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Lecturer Management Methods
    public function lecturers()
    {
        $this->checkAdminAccess();
        
        $lecturers = Lecturer::paginate(20);
        return view('admin.lecturers', compact('lecturers'));
    }

    public function createLecturer()
    {
        $this->checkAdminAccess();
        return view('admin.create-lecturer');
    }

    public function storeLecturer(Request $request)
    {
        $this->checkAdminAccess();
        
        $request->validate([
            'staff_id' => 'required|string|unique:lecturers,staff_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:lecturers,email',
            'phone' => 'nullable|string',
            'department' => 'nullable|string',
            'specialization' => 'nullable|string',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'staff_id', 'name', 'email', 'phone', 'department', 
            'specialization', 'bio'
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/lecturer-profiles', $filename);
            $data['profile_picture'] = 'storage/lecturer-profiles/' . $filename;
        }

        Lecturer::create($data);

        return redirect()->route('admin.lecturers')->with('success', 'Lecturer created successfully!');
    }

    public function editLecturer(Lecturer $lecturer)
    {
        $this->checkAdminAccess();
        return view('admin.edit-lecturer', compact('lecturer'));
    }

    public function updateLecturer(Request $request, Lecturer $lecturer)
    {
        $this->checkAdminAccess();

        $request->validate([
            'staff_id' => 'required|string|unique:lecturers,staff_id,' . $lecturer->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:lecturers,email,' . $lecturer->id,
            'phone' => 'nullable|string',
            'department' => 'nullable|string',
            'specialization' => 'nullable|string',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->only([
            'staff_id', 'name', 'email', 'phone', 'department', 
            'specialization', 'bio', 'is_active'
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($lecturer->profile_picture && Storage::exists(str_replace('storage/', 'public/', $lecturer->profile_picture))) {
                Storage::delete(str_replace('storage/', 'public/', $lecturer->profile_picture));
            }

            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/lecturer-profiles', $filename);
            $data['profile_picture'] = 'storage/lecturer-profiles/' . $filename;
        }

        $lecturer->update($data);

        return redirect()->route('admin.lecturers')->with('success', 'Lecturer updated successfully!');
    }

    public function deleteLecturer(Lecturer $lecturer)
    {
        $this->checkAdminAccess();

        try {
            $lecturerName = $lecturer->name;
            
            // Delete profile picture if exists
            if ($lecturer->profile_picture && Storage::exists(str_replace('storage/', 'public/', $lecturer->profile_picture))) {
                Storage::delete(str_replace('storage/', 'public/', $lecturer->profile_picture));
            }
            
            $lecturer->delete();
            
            Log::info('Lecturer deleted successfully', [
                'lecturer_id' => $lecturer->id,
                'lecturer_name' => $lecturerName,
                'deleted_by' => Auth::user()->name
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Lecturer deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting lecturer', [
                'lecturer_id' => $lecturer->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting lecturer: ' . $e->getMessage()
            ], 500);
        }
    }

    // Ex-Student Management Methods
    public function exStudents()
    {
        $this->checkAdminAccess();
        
        $exStudents = ExStudent::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.ex-students', compact('exStudents'));
    }

    public function createExStudent()
    {
        $this->checkAdminAccess();
        return view('admin.create-ex-student');
    }

    public function storeExStudent(Request $request)
    {
        $this->checkAdminAccess();
        
        $request->validate([
            'student_id' => 'required|string|unique:ex_students,student_id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:ex_students,email',
            'phone' => 'nullable|string',
            'program' => 'nullable|string',
            'graduation_year' => 'required|string',
            'graduation_month' => 'nullable|string',
            'cgpa' => 'nullable|numeric|min:0|max:4',
            'academic_records' => 'nullable|array',
            'certificate_data' => 'nullable|array',
        ]);

        $data = $request->only([
            'student_id', 'name', 'email', 'phone', 'program', 
            'graduation_year', 'graduation_month', 'cgpa', 
            'academic_records', 'certificate_data'
        ]);

        // Create ex-student record
        $exStudent = ExStudent::createExStudent($data);

        return redirect()->route('admin.ex-students')->with('success', 'Ex-student created successfully!');
    }

    public function editExStudent(ExStudent $exStudent)
    {
        $this->checkAdminAccess();
        return view('admin.edit-ex-student', compact('exStudent'));
    }

    public function updateExStudent(Request $request, ExStudent $exStudent)
    {
        $this->checkAdminAccess();

        $request->validate([
            'student_id' => 'required|string|unique:ex_students,student_id,' . $exStudent->id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:ex_students,email,' . $exStudent->id,
            'phone' => 'nullable|string',
            'program' => 'nullable|string',
            'graduation_year' => 'required|string',
            'graduation_month' => 'nullable|string',
            'cgpa' => 'nullable|numeric|min:0|max:4',
            'academic_records' => 'nullable|array',
            'certificate_data' => 'nullable|array',
        ]);

        $data = $request->only([
            'student_id', 'name', 'email', 'phone', 'program', 
            'graduation_year', 'graduation_month', 'cgpa', 
            'academic_records', 'certificate_data'
        ]);

        $exStudent->update($data);

        return redirect()->route('admin.ex-students')->with('success', 'Ex-student updated successfully!');
    }

    public function deleteExStudent(ExStudent $exStudent)
    {
        $this->checkAdminAccess();

        try {
            $studentName = $exStudent->name;
            $exStudent->delete();
            
            Log::info('Ex-student deleted successfully', [
                'ex_student_id' => $exStudent->id,
                'student_name' => $studentName,
                'deleted_by' => Auth::user()->name
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Ex-student deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting ex-student', [
                'ex_student_id' => $exStudent->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting ex-student: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateQrCode(ExStudent $exStudent)
    {
        $this->checkAdminAccess();

        try {
            $qrCodeService = new QrCodeService();
            $qrCodePath = $qrCodeService->generateCertificateQrCode($exStudent);
            $qrCodeUrl = $qrCodeService->getQrCodeUrl($qrCodePath);

            return response()->json([
                'success' => true,
                'qr_code_url' => $qrCodeUrl,
                'verification_url' => $exStudent->getVerificationUrl(),
                'student_id' => $exStudent->student_id
            ]);
        } catch (\Exception $e) {
            Log::error('QR code generation failed', [
                'error' => $e->getMessage(),
                'ex_student_id' => $exStudent->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadQrCode(ExStudent $exStudent)
    {
        $this->checkAdminAccess();

        try {
            $qrCodeService = new QrCodeService();
            $qrCodePath = $qrCodeService->generateCertificateQrCode($exStudent);
            $filePath = storage_path('app/public/' . $qrCodePath);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code file not found'
                ], 404);
            }

            return response()->download($filePath, "{$exStudent->student_id}_certificate_qr.svg");
        } catch (\Exception $e) {
            Log::error('QR code download failed', [
                'error' => $e->getMessage(),
                'ex_student_id' => $exStudent->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to download QR code: ' . $e->getMessage()
            ], 500);
        }
    }

}