<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        
        return view('admin.dashboard', compact('students', 'totalStudents'));
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

    
}
