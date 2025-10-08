<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\Log;

class FixEnrollmentStatus extends Command
{
    protected $signature = 'enrollments:fix-status';
    protected $description = 'Fix enrollment statuses to make them active';

    public function handle()
    {
        $this->info('Starting to fix enrollment statuses...');
        
        // Get all enrollments with non-active status
        $enrollments = StudentEnrollment::where('status', '!=', 'active')->get();
        
        $this->info("Found {$enrollments->count()} enrollments with non-active status");
        
        $updated = 0;
        $errors = 0;
        
        foreach ($enrollments as $enrollment) {
            try {
                $enrollment->update(['status' => 'active']);
                $updated++;
                $this->line("✓ Updated enrollment ID: {$enrollment->id}");
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("✗ Error updating enrollment ID {$enrollment->id}: " . $e->getMessage());
                Log::error('Error updating enrollment status', [
                    'enrollment_id' => $enrollment->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info("\n=== Summary ===");
        $this->info("Updated: {$updated}");
        $this->info("Errors: {$errors}");
        
        // Show final statistics
        $totalEnrollments = StudentEnrollment::count();
        $activeEnrollments = StudentEnrollment::where('status', 'active')->count();
        
        $this->info("\n=== Final Statistics ===");
        $this->info("Total Enrollments: {$totalEnrollments}");
        $this->info("Active Enrollments: {$activeEnrollments}");
        
        return 0;
    }
}
