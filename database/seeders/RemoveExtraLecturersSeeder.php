<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;

class RemoveExtraLecturersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all lecturers
        $lecturers = User::where('role', 'lecturer')->get();
        
        $this->command->info("Found {$lecturers->count()} lecturers");
        
        if ($lecturers->count() <= 2) {
            $this->command->info("Already have {$lecturers->count()} lecturers. No action needed.");
            return;
        }
        
        // Keep only the first two lecturers (John Smith and Sarah Johnson)
        $lecturersToKeep = $lecturers->take(2);
        $lecturersToDelete = $lecturers->skip(2);
        
        $this->command->info("Keeping lecturers:");
        foreach ($lecturersToKeep as $lecturer) {
            $this->command->line("- {$lecturer->name} ({$lecturer->email})");
        }
        
        $this->command->info("Deleting lecturers:");
        foreach ($lecturersToDelete as $lecturer) {
            $this->command->line("- {$lecturer->name} ({$lecturer->email})");
        }
        
        // Delete extra lecturers
        $deletedCount = 0;
        foreach ($lecturersToDelete as $lecturer) {
            // Delete associated lecturer profile first
            Lecturer::where('user_id', $lecturer->id)->delete();
            // Then delete the user
            $lecturer->delete();
            $deletedCount++;
        }
        
        $this->command->info("Successfully deleted {$deletedCount} lecturers.");
        
        // Show remaining lecturers
        $remaining = User::where('role', 'lecturer')->get(['id', 'name', 'email']);
        $this->command->info("Remaining lecturers ({$remaining->count()}):");
        foreach ($remaining as $lecturer) {
            $this->command->line("- ID: {$lecturer->id}, Name: {$lecturer->name}, Email: {$lecturer->email}");
        }
    }
}