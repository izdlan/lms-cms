<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;

class LecturerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all lecturer users
        $lecturerUsers = User::where('role', 'lecturer')->get();
        
        foreach ($lecturerUsers as $user) {
            // Check if lecturer profile already exists for this user
            $existingLecturer = Lecturer::where('email', $user->email)->first();
            
            if (!$existingLecturer) {
                // Create lecturer profile for this user
                $staffId = 'LEC' . str_pad($user->id + 100, 3, '0', STR_PAD_LEFT); // Use higher numbers to avoid conflicts
                Lecturer::create([
                    'user_id' => $user->id,
                    'staff_id' => $staffId,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'department' => 'General Studies',
                    'specialization' => 'Academic Teaching',
                    'bio' => 'Experienced lecturer with expertise in academic teaching and student development.',
                    'is_active' => true
                ]);
            } else {
                // Update existing lecturer profile to link to user
                $existingLecturer->update([
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}