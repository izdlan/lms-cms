<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Services\CsvImportService;

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
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        try {
            $file = $request->file('csv_file');
            $filePath = $file->getRealPath();
            
            $csvService = new CsvImportService();
            $result = $csvService->importFromCsv($filePath);
            
            if ($result['success']) {
                $message = "Import completed! Created: {$result['created']}, Updated: {$result['updated']}, Errors: {$result['errors']}";
                return redirect()->route('admin.students')->with('success', $message);
            } else {
                return back()->withErrors(['csv_file' => 'Error importing file.']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['csv_file' => 'Error importing file: ' . $e->getMessage()]);
        }
    }

    public function syncFromCsv(Request $request)
    {
        $this->checkAdminAccess();
        
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        try {
            $file = $request->file('csv_file');
            $filePath = $file->getRealPath();
            
            $csvService = new CsvImportService();
            $result = $csvService->importFromCsv($filePath);
            
            if ($result['success']) {
                $message = "Sync completed! Created: {$result['created']}, Updated: {$result['updated']}, Errors: {$result['errors']}";
                return redirect()->route('admin.students')->with('success', $message);
            } else {
                return back()->withErrors(['csv_file' => 'Error syncing file.']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['csv_file' => 'Error syncing file: ' . $e->getMessage()]);
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

        $student->delete();
        return redirect()->route('admin.students')->with('success', 'Student deleted successfully!');
    }
}