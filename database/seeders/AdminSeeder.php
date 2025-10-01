<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Admin::create([
            'name' => 'System Administrator',
            'email' => 'admin@olympia.edu',
            'password' => 'admin123', // Will be hashed automatically
            'role' => 'admin',
            'is_active' => true
        ]);

        // Create some sample home page content
        \App\Models\HomePageContent::create([
            'section_name' => 'hero',
            'title' => 'Welcome to Olympia Education',
            'content' => 'Your gateway to professional, executive, advanced and continuing education. We provide quality education that prepares you for success in your chosen field.',
            'image_url' => 'https://via.placeholder.com/800x400/007bff/ffffff?text=Olympia+Education',
            'sort_order' => 1,
            'is_active' => true
        ]);

        \App\Models\HomePageContent::create([
            'section_name' => 'about',
            'title' => 'About Us',
            'content' => 'Olympia Education is committed to providing high-quality education and training programs that meet the needs of today\'s professionals and students.',
            'image_url' => 'https://via.placeholder.com/600x400/28a745/ffffff?text=About+Us',
            'sort_order' => 2,
            'is_active' => true
        ]);

        // Create some sample announcements
        \App\Models\PublicAnnouncement::create([
            'title' => 'Welcome to Our New Learning Management System',
            'content' => 'We are excited to announce the launch of our new and improved Learning Management System. This platform provides enhanced features for students, lecturers, and administrators.',
            'category' => 'general',
            'priority' => 'high',
            'image_url' => 'https://via.placeholder.com/400x300/ffc107/000000?text=LMS+Launch',
            'published_at' => now(),
            'is_featured' => true,
            'is_active' => true,
            'admin_id' => 1
        ]);

        \App\Models\PublicAnnouncement::create([
            'title' => 'Course Registration Now Open',
            'content' => 'Registration for the upcoming semester is now open. Please visit the course registration page to select your courses.',
            'category' => 'academic',
            'priority' => 'medium',
            'published_at' => now(),
            'is_featured' => false,
            'is_active' => true,
            'admin_id' => 1
        ]);
    }
}
