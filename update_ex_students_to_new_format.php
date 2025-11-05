<?php

/**
 * Script to update existing ex-students to new format
 * 
 * This script:
 * 1. Sets program_short and program_full based on existing program field
 * 2. Sets graduation_day to 1 (default) if not set
 * 3. Updates existing records to follow the new format
 * 
 * Usage: php update_ex_students_to_new_format.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExStudent;

echo "Starting ex-students update to new format...\n\n";

$students = ExStudent::all();
$updated = 0;
$skipped = 0;

foreach ($students as $student) {
    $updatedFields = [];
    
    // Update program_short and program_full if not set
    if (!$student->program_short && $student->program) {
        // Extract short program name (e.g., "Bachelor of Science" from "Bachelor of Science (Hons) in ICT")
        $program = $student->program;
        
        // Try to extract short name (before "(" or "in")
        if (preg_match('/^([^(]+?)(?:\s*\(|$)/', $program, $matches)) {
            $shortName = trim($matches[1]);
        } else {
            // If no parentheses, use first part before "in"
            $parts = explode(' in ', $program);
            $shortName = trim($parts[0]);
        }
        
        $student->program_short = $shortName;
        $student->program_full = $program;
        $updatedFields[] = "program_short: {$shortName}";
        $updatedFields[] = "program_full: {$program}";
    }
    
    // Set graduation_day to 1 if not set (default to first day of month)
    if (!$student->graduation_day && $student->graduation_month) {
        $student->graduation_day = 1;
        $updatedFields[] = "graduation_day: 1";
    }
    
    if (!empty($updatedFields)) {
        $student->save();
        $updated++;
        echo "Updated student {$student->student_id} ({$student->name}):\n";
        echo "  - " . implode("\n  - ", $updatedFields) . "\n\n";
    } else {
        $skipped++;
    }
}

echo "Update complete!\n";
echo "Updated: {$updated} students\n";
echo "Skipped: {$skipped} students (already in new format or missing data)\n";

