<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    public function index()
    {
        try {
            // Mock announcement data - in real app, this would come from database
            $announcements = [
                [
                    'id' => 1,
                    'title' => 'Important: Course Registration Deadline Extended',
                    'content' => 'Due to technical issues, the course registration deadline has been extended to December 15, 2024. Please ensure you complete your registration before this date.',
                    'author' => 'Academic Office',
                    'date' => '2024-12-10',
                    'priority' => 'high',
                    'category' => 'Academic',
                    'poster' => '/store/1/announcements/course-registration-poster.jpg',
                    'has_poster' => true
                ],
                [
                    'id' => 2,
                    'title' => 'Final Exam Schedule Released',
                    'content' => 'The final exam schedule for the current semester has been released. Please check your student portal for details and prepare accordingly.',
                    'author' => 'Academic Office',
                    'date' => '2024-12-08',
                    'priority' => 'high',
                    'category' => 'Academic',
                    'poster' => '/store/1/announcements/exam-schedule-poster.jpg',
                    'has_poster' => true
                ],
                [
                    'id' => 3,
                    'title' => 'Student Portal Maintenance Scheduled',
                    'content' => 'The student portal will be under maintenance on December 12, 2024, from 2:00 AM to 6:00 AM. Please plan accordingly.',
                    'author' => 'IT Department',
                    'date' => '2024-12-05',
                    'priority' => 'medium',
                    'category' => 'Academic',
                    'poster' => null,
                    'has_poster' => false
                ],
                [
                    'id' => 4,
                    'title' => 'Graduation Ceremony Information',
                    'content' => 'Graduation ceremony details for the December 2024 batch will be announced soon. Please check your email for updates.',
                    'author' => 'Student Affairs',
                    'date' => '2024-12-03',
                    'priority' => 'low',
                    'category' => 'Events',
                    'poster' => '/store/1/announcements/graduation-ceremony-poster.jpg',
                    'has_poster' => true
                ],
                [
                    'id' => 5,
                    'title' => 'Scholarship Application Deadline',
                    'content' => 'Applications for the Merit Scholarship for the next semester are now open. Deadline: December 20, 2024.',
                    'author' => 'Financial Aid Office',
                    'date' => '2024-12-01',
                    'priority' => 'high',
                    'category' => 'Financial',
                    'poster' => '/store/1/announcements/scholarship-poster.jpg',
                    'has_poster' => true
                ]
            ];

            Log::info('Announcements page accessed');
            return view('announcements.index', compact('announcements'));
        } catch (\Exception $e) {
            Log::error('Announcements page error: ' . $e->getMessage());
            return response()->view('errors.500', ['message' => 'An error occurred while loading announcements.'], 500);
        }
    }

    public function show($id)
    {
        try {
            // Mock single announcement data
            $announcement = [
                'id' => $id,
                'title' => 'Important: Course Registration Deadline Extended',
                'content' => 'Due to technical issues, the course registration deadline has been extended to December 15, 2024. Please ensure you complete your registration before this date. This extension applies to all undergraduate and graduate programs. Students who have already registered do not need to take any action.',
                'author' => 'Academic Office',
                'date' => '2024-12-10',
                'priority' => 'high',
                'category' => 'Academic',
                'poster' => '/store/1/announcements/course-registration-poster.jpg',
                'has_poster' => true,
                'full_content' => 'Due to technical issues with our registration system, the course registration deadline has been extended to December 15, 2024. This extension applies to all undergraduate and graduate programs.

Students who have already registered do not need to take any action. For those who haven\'t registered yet, please ensure you complete your registration before the new deadline.

If you encounter any issues during registration, please contact the Academic Office at academic@olympia.edu or visit our office during business hours.

Thank you for your understanding and cooperation.'
            ];

            Log::info('Announcement detail accessed', ['id' => $id]);
            return view('announcements.show', compact('announcement'));
        } catch (\Exception $e) {
            Log::error('Announcement detail error: ' . $e->getMessage());
            return response()->view('errors.500', ['message' => 'An error occurred while loading the announcement.'], 500);
        }
    }
}
