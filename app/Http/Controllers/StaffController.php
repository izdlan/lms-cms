<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Program;
use App\Models\Subject;
use App\Models\Lecturer;
use App\Models\ClassSchedule;
use App\Models\StudentEnrollment;
use App\Models\Announcement;
use App\Models\CourseMaterial;

class StaffController extends Controller
{
    public function dashboard()
    {
        $user = Auth::guard('staff')->user();
        
        // Check if user is a lecturer
        if (!$user || !$user->isLecturer()) {
            return redirect()->route('login')->with('error', 'Access denied. Lecturer privileges required.');
        }

        // Get lecturer information using the relationship
        $lecturer = $user->lecturer;
        
        if (!$lecturer) {
            return redirect()->route('login')->with('error', 'Lecturer profile not found.');
        }

        // Get subjects taught by this lecturer
        $subjects = Subject::whereHas('classSchedules', function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })->with(['classSchedules' => function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        }])->get();

        // Get all classes for this lecturer
        $classes = ClassSchedule::where('lecturer_id', $lecturer->id)
            ->with('subject')
            ->get();

        // Get statistics
        $totalSubjects = $subjects->count();
        $totalClasses = $classes->count();
        $totalStudents = StudentEnrollment::whereIn('class_code', $classes->pluck('class_code'))->count();
        $totalMaterials = 0; // Will be calculated from course contents

        return view('staff.dashboard', compact('user', 'lecturer', 'subjects', 'classes', 'totalSubjects', 'totalClasses', 'totalStudents', 'totalMaterials'));
    }

    public function courses(Request $request)
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Get lecturer information using the relationship
        $lecturer = $user->lecturer;
        
        if (!$lecturer) {
            return redirect()->route('login')->with('error', 'Lecturer profile not found.');
        }

        // Get subjects taught by this lecturer
        $subjects = Subject::whereHas('classSchedules', function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })->with(['classSchedules' => function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        }])->get();

        // Get classes for this lecturer
        $classes = ClassSchedule::where('lecturer_id', $lecturer->id)
            ->with('subject')
            ->get();

        // Handle class selection
        $selectedClass = null;
        $students = collect();
        
        if ($request->has('class_code')) {
            $selectedClass = ClassSchedule::where('class_code', $request->class_code)
                ->where('lecturer_id', $lecturer->id)
                ->with('subject')
                ->first();
                
            if ($selectedClass) {
                $students = StudentEnrollment::where('class_code', $selectedClass->class_code)
                    ->with(['user', 'subject'])
                    ->get();
            }
        }

        return view('staff.courses', compact('user', 'lecturer', 'subjects', 'classes', 'selectedClass', 'students'));
    }

    /**
     * Get students for a specific class via AJAX
     */
    public function getClassStudents(Request $request)
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $lecturer = $user->lecturer;
        if (!$lecturer) {
            return response()->json(['error' => 'Lecturer profile not found'], 404);
        }

        $classCode = $request->input('class_code');
        
        // Verify the class belongs to this lecturer
        $class = ClassSchedule::where('class_code', $classCode)
            ->where('lecturer_id', $lecturer->id)
            ->with('subject')
            ->first();

        if (!$class) {
            return response()->json(['error' => 'Class not found or access denied'], 404);
        }

        // Get students enrolled in this class
        $students = StudentEnrollment::where('class_code', $classCode)
            ->with(['user', 'subject'])
            ->get();

        return response()->json([
            'class' => $class,
            'students' => $students,
            'student_count' => $students->count()
        ]);
    }

    public function announcements()
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Get lecturer information using the relationship
        $lecturer = $user->lecturer;
        
        if (!$lecturer) {
            return redirect()->route('login')->with('error', 'Lecturer profile not found.');
        }

        // Get subjects taught by this lecturer
        $subjects = Subject::whereHas('classSchedules', function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })->with(['classSchedules' => function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        }])->get();

        return view('staff.announcements', compact('user', 'lecturer', 'subjects'));
    }

    public function createAnnouncement(Request $request)
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $request->validate([
            'subject_code' => 'required|string',
            'class_code' => 'required|string',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_important' => 'boolean',
            'target_classes' => 'required|array|min:1',
        ]);

        $lecturer = $user->lecturer;
        if (!$lecturer) {
            return redirect()->route('login')->with('error', 'Lecturer profile not found.');
        }

        // Create announcement for each target class
        foreach ($request->target_classes as $classCode) {
            Announcement::create([
                'subject_code' => $request->subject_code,
                'class_code' => $classCode,
                'title' => $request->title,
                'content' => $request->input('content'),
                'author_name' => $lecturer->getAttribute('name'),
                'author_email' => $lecturer->getAttribute('email'),
                'is_important' => $request->boolean('is_important'),
                'is_active' => true,
                'published_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully!'
        ]);
    }

    public function getAnnouncements(Request $request)
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'subject_code' => 'required|string',
            'class_code' => 'required|string',
        ]);

        $announcements = Announcement::where('subject_code', $request->subject_code)
            ->where('class_code', $request->class_code)
            ->orderBy('published_at', 'desc')
            ->orderBy('is_important', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'announcements' => $announcements
        ]);
    }

    /**
     * Delete announcement
     */
    public function deleteAnnouncement($id)
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $announcement = Announcement::findOrFail($id);
        
        // Check if user has permission to delete this announcement
        $lecturer = $user->lecturer;
        if (!$lecturer || $announcement->author_email !== $lecturer->getAttribute('email')) {
            return response()->json(['error' => 'You do not have permission to delete this announcement.'], 403);
        }

        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully!'
        ]);
    }

    public function contents()
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Get lecturer information using the relationship
        $lecturer = $user->lecturer;
        
        if (!$lecturer) {
            return redirect()->route('login')->with('error', 'Lecturer profile not found.');
        }

        // Get subjects taught by this lecturer
        $subjects = Subject::whereHas('classSchedules', function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })->with(['classSchedules' => function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        }])->get();

        return view('staff.contents', compact('user', 'lecturer', 'subjects'));
    }

    public function assignments()
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $programs = Program::active()->get();
        return view('staff.assignments', compact('user', 'programs'));
    }

    public function students()
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $programs = Program::active()->get();
        $students = User::where('role', 'student')->get();
        return view('staff.students', compact('user', 'programs', 'students'));
    }

    public function profile()
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        return view('staff.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone']);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                $oldPath = storage_path('app/public/' . $user->profile_picture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Store new profile picture
            $file = $request->file('profile_picture');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_pictures', $filename, 'public');
            $data['profile_picture'] = $path;
        }

        // Handle profile picture removal
        if ($request->has('remove_profile_picture')) {
            if ($user->profile_picture) {
                $oldPath = storage_path('app/public/' . $user->profile_picture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $data['profile_picture'] = null;
        }

        /** @var \App\Models\User $user */
        $user->update($data);

        return redirect()->route('staff.profile')->with('success', 'Profile updated successfully!');
    }

    public function changePassword()
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        return view('staff.password-change', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'must_reset_password' => false,
        ]);

        return redirect()->route('staff.dashboard')->with('success', 'Password updated successfully!');
    }

    /**
     * Upload course material
     */
    public function uploadMaterial(Request $request)
    {
        Log::info('Upload material request received', [
            'user_id' => Auth::guard('staff')->id(),
            'request_data' => $request->all(),
            'files' => $request->files->all()
        ]);
        
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            Log::warning('Unauthorized upload attempt', ['user' => $user]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'subject_code' => 'required|string',
            'class_code' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material_type' => 'required|in:document,video,image,audio,link,other',
            'file' => 'nullable|file|max:10240', // 10MB max
            'external_url' => 'nullable|url',
        ]);

        $lecturer = $user->lecturer;
        if (!$lecturer) {
            return response()->json(['error' => 'Lecturer profile not found.'], 400);
        }

        $materialData = [
            'subject_code' => $request->subject_code,
            'class_code' => $request->class_code,
            'title' => $request->title,
            'description' => $request->description,
            'material_type' => $request->material_type,
            'author_name' => $lecturer->getAttribute('name'),
            'author_email' => $lecturer->getAttribute('email'),
            'is_active' => true,
            'is_public' => true,
            'published_at' => now(),
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('materials/' . $request->subject_code, $filename, 'public');
            
            $materialData['file_path'] = $path;
            $materialData['file_name'] = $file->getClientOriginalName();
            $materialData['file_size'] = $file->getSize();
            $materialData['file_extension'] = $file->getClientOriginalExtension();
        }

        // Handle external URL
        if ($request->material_type === 'link' && $request->external_url) {
            $materialData['external_url'] = $request->external_url;
        }

        $material = CourseMaterial::create($materialData);

        return response()->json([
            'success' => true,
            'message' => 'Material uploaded successfully!',
            'material' => $material
        ]);
    }

    /**
     * Get course materials for a subject and class
     */
    public function getMaterials(Request $request)
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'subject_code' => 'required|string',
            'class_code' => 'required|string',
        ]);

        $materials = CourseMaterial::where('subject_code', $request->subject_code)
            ->where('class_code', $request->class_code)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'materials' => $materials
        ]);
    }

    /**
     * Download course material
     */
    public function downloadMaterial($id)
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $material = CourseMaterial::findOrFail($id);
        
        // Check if user has access to this material
        $lecturer = $user->lecturer;
        if (!$lecturer || $material->author_email !== $lecturer->getAttribute('email')) {
            return redirect()->back()->with('error', 'You do not have permission to download this material.');
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
     * Delete course material
     */
    public function deleteMaterial($id)
    {
        $user = Auth::guard('staff')->user();
        if (!$user || $user->role !== 'lecturer') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $material = CourseMaterial::findOrFail($id);
        
        // Check if user has permission to delete this material
        $lecturer = $user->lecturer;
        if (!$lecturer || $material->author_email !== $lecturer->getAttribute('email')) {
            return response()->json(['error' => 'You do not have permission to delete this material.'], 403);
        }

        // Delete file if exists
        if ($material->file_path && file_exists(storage_path('app/public/' . $material->file_path))) {
            unlink(storage_path('app/public/' . $material->file_path));
        }

        $material->delete();

        return response()->json([
            'success' => true,
            'message' => 'Material deleted successfully!'
        ]);
    }
}
