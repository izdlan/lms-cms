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

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function checkAdminAccess()
    {
        if (!Auth::user()->isAdmin()) {
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
        
        if ($student->role !== 'student') {
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
            
            return redirect()->route('admin.students')->with('success', 'Student deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting student', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.students')->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }

    public function bulkDeleteStudents(Request $request)
    {
        $this->checkAdminAccess();
        
        \Log::info('Bulk delete request received', [
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

            \Log::info('Processing bulk delete', ['student_ids' => $studentIds]);

            foreach ($studentIds as $studentId) {
                $student = User::find($studentId);
                if ($student && $student->role === 'student') {
                    \Log::info('Deleting student', ['id' => $studentId, 'name' => $student->name]);
                    $student->delete();
                    $deletedCount++;
                }
            }

            $message = $deletedCount === 1 
                ? '1 student deleted successfully!' 
                : "{$deletedCount} students deleted successfully!";

            \Log::info('Bulk delete completed', ['deleted_count' => $deletedCount]);

            return redirect()->route('admin.students')->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Bulk delete error: ' . $e->getMessage());
            return redirect()->route('admin.students')->with('error', 'Error deleting students: ' . $e->getMessage());
        }
    }

    public function automation()
    {
        $this->checkAdminAccess();
        
        // Get real automation status
        $automationConfig = $this->getAutomationConfig();
        $lastImportTime = cache()->get('last_auto_import_time', null);
        $totalStudents = User::where('role', 'student')->count();
        $filePath = $automationConfig['excel_file'] ?? storage_path('app/students/Enrollment OEM.xlsx');
        $fileExists = file_exists($filePath);
        $fileStatus = $fileExists ? 'Watching' : 'File Not Found';
        $lastModified = $fileExists ? filemtime($filePath) : null;
        
        // Get recent import logs
        $recentLogs = $this->getRecentImportLogs();
        
        return view('admin.automation', compact(
            'automationConfig',
            'lastImportTime', 
            'totalStudents',
            'fileStatus',
            'lastModified',
            'recentLogs'
        ));
    }
    
    private function getAutomationConfig()
    {
        $configPath = storage_path('app/automation.json');
        if (file_exists($configPath)) {
            return json_decode(file_get_contents($configPath), true);
        }
        
        return [
            'excel_file' => storage_path('app/students/Enrollment OEM.xlsx'),
            'notification_email' => 'admin@example.com',
            'import_frequency' => 'every-minute',
            'status' => 'Enabled'
        ];
    }
    
    private function getRecentImportLogs()
    {
        // Read recent log entries related to auto import
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $lines = explode("\n", $logContent);
            
            // Get last 20 lines that contain auto import info
            $autoImportLines = array_filter($lines, function($line) {
                return strpos($line, 'AutoImportStudents') !== false || 
                       strpos($line, 'Auto import') !== false ||
                       strpos($line, 'students:auto-import') !== false;
            });
            
            $recentLines = array_slice(array_reverse($autoImportLines), 0, 10);
            
            foreach ($recentLines as $line) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?(\w+):\s*(.+)/', $line, $matches)) {
                    $logs[] = [
                        'time' => $matches[1],
                        'level' => strtolower($matches[2]),
                        'message' => trim($matches[3])
                    ];
                }
            }
        }
        
        return $logs;
    }

    public function triggerImport(Request $request)
    {
        $this->checkAdminAccess();
        
        try {
            $filePath = $request->input('file_path', storage_path('app/students/Enrollment OEM.xlsx'));
            $email = $request->input('email');
            
            // Run the auto-import command
            $exitCode = Artisan::call('students:auto-import', [
                '--file' => $filePath,
                '--email' => $email,
                '--force' => true
            ]);
            
            if ($exitCode === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Import completed successfully!',
                    'output' => Artisan::output()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Import failed with exit code: ' . $exitCode,
                    'output' => Artisan::output()
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import error: ' . $e->getMessage()
            ]);
        }
    }
}