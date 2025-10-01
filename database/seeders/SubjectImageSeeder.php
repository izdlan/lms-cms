<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;

class SubjectImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample subject images (placeholder URLs for now)
        $subjects = Subject::active()->take(5)->get();
        
        foreach ($subjects as $index => $subject) {
            // For demonstration, we'll use placeholder images
            // In a real scenario, you would upload actual images
            $placeholderImages = [
                'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=400&h=300&fit=crop',
            ];
            
            // For now, we'll just set a placeholder URL
            // In production, you would download and store these images locally
            $subject->update([
                'image' => 'subjects/placeholder_' . ($index + 1) . '.jpg'
            ]);
        }
    }
}
