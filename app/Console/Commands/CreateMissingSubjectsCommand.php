<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Program;
use App\Models\ProgramSubject;
use App\Models\Subject;

class CreateMissingSubjectsCommand extends Command
{
    protected $signature = 'subjects:create-missing';
    protected $description = 'Create Subject records from ProgramSubject for all programs';

    public function handle()
    {
        $this->info('Creating subjects from program_subjects table...');
        
        $programs = Program::all();
        $created = 0;
        $skipped = 0;
        
        foreach ($programs as $program) {
            $this->info("\nProcessing program: {$program->code} ({$program->name})");
            
            $programSubjects = ProgramSubject::where('program_id', $program->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
            
            $this->info("Found {$programSubjects->count()} program subjects");
            
            foreach ($programSubjects as $programSubject) {
                // Check if subject already exists
                $existingSubject = null;
                if ($programSubject->subject_code) {
                    $existingSubject = Subject::where('code', $programSubject->subject_code)->first();
                }
                
                if ($existingSubject) {
                    $this->line("  ⊘ Already exists: {$programSubject->subject_code}");
                    $skipped++;
                    continue;
                }
                
                // Create subject code if not exists
                $subjectCode = $programSubject->subject_code;
                if (!$subjectCode) {
                    // Generate a subject code
                    $subjectCode = $program->code . str_pad($programSubject->sort_order, 3, '0', STR_PAD_LEFT);
                    $this->warn("  ⚠ No subject_code for {$programSubject->subject_name}, generated: {$subjectCode}");
                }
                
                // Create the subject
                Subject::create([
                    'code' => $subjectCode,
                    'name' => $programSubject->subject_name,
                    'description' => $programSubject->description ?? $programSubject->subject_name,
                    'classification' => $programSubject->classification ?? 'Core',
                    'credit_hours' => $programSubject->credit_hours ?? 3,
                    'program_code' => $program->code,
                    'is_active' => true,
                ]);
                
                $this->info("  ✓ Created: {$subjectCode} - {$programSubject->subject_name}");
                $created++;
            }
        }
        
        $this->newLine();
        $this->info("=== Summary ===");
        $this->info("Created: {$created} subjects");
        $this->info("Skipped: {$skipped} subjects");
    }
}

