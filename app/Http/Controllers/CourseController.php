<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    /**
     * Show course summary page
     */
    public function summary(Request $request, $courseId)
    {
        try {
            $user = Auth::guard('student')->user();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login to access this page.');
            }
            
            if ($user->role !== 'student') {
                return redirect()->route('login')->with('error', 'Access denied. Please login as a student.');
            }

            // Get course information (you can expand this to fetch from database)
            $courseInfo = $this->getCourseInfo($courseId);
            
            Log::info('Course summary accessed', ['courseId' => $courseId, 'user' => $user->name]);
            
            return view('course.summary', compact('courseId', 'courseInfo'));
        } catch (\Exception $e) {
            Log::error('Course summary error: ' . $e->getMessage());
            return response()->view('errors.500', ['message' => 'An error occurred while loading the course page.'], 500);
        }
    }

    /**
     * Show course announcements page
     */
    public function announcements(Request $request, $courseId)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        if ($user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Access denied. Please login as a student.');
        }

        // Get course announcements (you can expand this to fetch from database)
        $announcements = $this->getCourseAnnouncements($courseId);
        
        return view('course.announcements', compact('courseId', 'announcements'));
    }

    /**
     * Show course contents page
     */
    public function contents(Request $request, $courseId)
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        if ($user->role !== 'student') {
            return redirect()->route('login')->with('error', 'Access denied. Please login as a student.');
        }

        // Get course contents (you can expand this to fetch from database)
        $contents = $this->getCourseContents($courseId);
        
        return view('course.contents', compact('courseId', 'contents'));
    }

    /**
     * Get course information
     */
    private function getCourseInfo($courseId)
    {
        // Mock data - replace with actual database queries
        return [
            'id' => $courseId,
            'name' => strtoupper($courseId),
            'title' => 'Technology Entrepreneurship',
            'description' => 'Behind every successful technology company is a visionary, effective and efficient technopreneur. In this course, students will be exposed to entrepreneurship and apply their entrepreneurial skills in developing an advanced technology that could be a basis for the creation and development of a technology-based venture.',
            'instructor' => 'Dr. Yasmin Binti Kasim',
            'total_instructors' => 153,
            'total_students' => 5472,
            'mooc_title' => 'Technology Entrepreneurship',
            'intro_video' => null,
        ];
    }

    /**
     * Get course announcements
     */
    private function getCourseAnnouncements($courseId)
    {
        // Mock data - replace with actual database queries
        return [
            [
                'id' => 1,
                'title' => 'Attention to all students group (SR2415NB, CDCS2535B, CFAP2255A, EMK4M2A, EMD7M2C, CAFF1154A, AP2295A, AP2208A, AP2207A, CFAP2256B)',
                'content' => 'Dear students, Please contact me at 019-3749803 regarding class ENT600 & ENT300. Thank You',
                'author' => 'JAUHARIAH BINTI JUHARI',
                'author_image' => 'https://via.placeholder.com/40x40/ff6b6b/ffffff?text=JJ',
                'date' => 'April 21st, 2024',
                'created_at' => '2024-04-21 10:30:00'
            ],
            [
                'id' => 2,
                'title' => 'Assignment Submission Deadline Reminder',
                'content' => 'Please note that the assignment submission deadline is approaching. Make sure to submit your work before the due date.',
                'author' => 'Course Coordinator',
                'author_image' => 'https://via.placeholder.com/40x40/4ecdc4/ffffff?text=CC',
                'date' => 'April 18th, 2024',
                'created_at' => '2024-04-18 14:20:00'
            ]
        ];
    }

    /**
     * Get course contents
     */
    private function getCourseContents($courseId)
    {
        // Mock data - replace with actual database queries
        return [
            [
                'id' => 1,
                'name' => 'STANDARD LEARNING MATERIAL',
                'files_count' => 10,
                'created_by' => 'System',
                'date_uploaded' => '2024-05-13 07:47:52',
                'is_highlighted' => true
            ],
            [
                'id' => 2,
                'name' => '2024 OCT-MARCH MOHD RAMADAN - UITM PUNCAK ALAM',
                'files_count' => 0,
                'created_by' => 'DR. MOHD RAMADAN BIN AB HAMID',
                'date_uploaded' => '2024-10-18 08:57:10',
                'is_highlighted' => false
            ],
            [
                'id' => 3,
                'name' => 'ADZHAR ABD KADIR',
                'files_count' => 2,
                'created_by' => 'ADZHAR BIN ABD KADIR',
                'date_uploaded' => '2018-10-10 15:34:59',
                'is_highlighted' => false
            ],
            [
                'id' => 4,
                'name' => 'AHMAD NUR MISUARI (KUALA PILAH)',
                'files_count' => 7,
                'created_by' => 'AHMAD NUR MISUARI BIN IBRAHIM',
                'date_uploaded' => '2017-11-07 12:09:55',
                'is_highlighted' => false
            ],
            [
                'id' => 5,
                'name' => 'AINAA IDAYU ISKANDAR -KAMPUS MELAKA',
                'files_count' => 5,
                'created_by' => 'DR. AINAA IDAYU BINTI ISKANDAR',
                'date_uploaded' => '2017-09-19 11:55:55',
                'is_highlighted' => false
            ]
        ];
    }
}
