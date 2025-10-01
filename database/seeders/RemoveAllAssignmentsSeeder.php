<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;

class RemoveAllAssignmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Removing all assignments and submissions...');

        // Delete all assignment submissions first (due to foreign key constraints)
        $submissionsCount = AssignmentSubmission::count();
        AssignmentSubmission::query()->delete();
        $this->command->info("✓ Deleted {$submissionsCount} assignment submissions");

        // Delete all assignments
        $assignmentsCount = Assignment::count();
        Assignment::query()->delete();
        $this->command->info("✓ Deleted {$assignmentsCount} assignments");

        $this->command->info("\n✅ Successfully removed all assignments and submissions!");
        $this->command->info("You can now add assignments manually through the lecturer interface.");
    }
}
