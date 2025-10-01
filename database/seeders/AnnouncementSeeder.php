<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;
use App\Models\Subject;
use App\Models\Lecturer;
use Carbon\Carbon;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = Subject::all();
        $lecturers = Lecturer::all();

        foreach ($subjects as $subject) {
            $lecturer = $lecturers->random();
            $classCode = $subject->code . '-001';

            // Create 3-5 announcements per subject
            $announcements = [
                [
                    'title' => 'Welcome to ' . $subject->name,
                    'content' => 'Welcome to our ' . $subject->name . ' class! I\'m excited to work with all of you this semester. Please review the course materials and don\'t hesitate to reach out if you have any questions.',
                    'is_important' => true,
                    'published_at' => now()->subDays(7)
                ],
                [
                    'title' => 'Course Schedule Update',
                    'content' => 'Please note that our next class will be held online via Zoom. The meeting link will be sent to your email 30 minutes before the class starts.',
                    'is_important' => false,
                    'published_at' => now()->subDays(5)
                ],
                [
                    'title' => 'Assignment Due Date Reminder',
                    'content' => 'Just a friendly reminder that the first assignment is due next Friday. Please submit it through the course portal before 11:59 PM.',
                    'is_important' => true,
                    'published_at' => now()->subDays(3)
                ],
                [
                    'title' => 'Course Materials Available',
                    'content' => 'I\'ve uploaded the lecture notes and additional reading materials for this week. Please download them from the Course Content section.',
                    'is_important' => false,
                    'published_at' => now()->subDays(1)
                ],
                [
                    'title' => 'Midterm Exam Information',
                    'content' => 'The midterm exam will be conducted online on [Date]. Please ensure you have a stable internet connection and a quiet environment. More details will be provided closer to the date.',
                    'is_important' => true,
                    'published_at' => now()->subHours(12)
                ]
            ];

            foreach ($announcements as $announcementData) {
                Announcement::create([
                    'subject_code' => $subject->code,
                    'class_code' => $classCode,
                    'title' => $announcementData['title'],
                    'content' => $announcementData['content'],
                    'author_name' => $lecturer->name,
                    'author_email' => $lecturer->email,
                    'is_important' => $announcementData['is_important'],
                    'is_active' => true,
                    'published_at' => $announcementData['published_at']
                ]);
            }
        }
    }
}