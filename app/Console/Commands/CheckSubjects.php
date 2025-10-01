<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subject;
use App\Models\Program;

class CheckSubjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subjects:check {--program= : Filter by specific program}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check subjects in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $program = $this->option('program');
        
        // Check programs first
        $this->info("=== PROGRAMS ===");
        $programs = Program::all();
        $this->table(
            ['ID', 'Name', 'Code', 'Description'],
            $programs->map(function($program) {
                return [
                    $program->id,
                    $program->name,
                    $program->code ?? 'N/A',
                    substr($program->description ?? 'N/A', 0, 50) . '...'
                ];
            })->toArray()
        );
        
        // Check subjects
        $this->info("\n=== SUBJECTS ===");
        $query = Subject::query();
        
        if ($program) {
            $query->where('program_code', $program);
            $this->info("Filtering by program: {$program}");
        }
        
        $subjects = $query->get();
        
        if ($subjects->isEmpty()) {
            $this->warn("No subjects found!");
            return;
        }
        
        $this->info("Total subjects: {$subjects->count()}");
        
        $this->table(
            ['ID', 'Code', 'Name', 'Program', 'Credits', 'Status'],
            $subjects->map(function($subject) {
                return [
                    $subject->id,
                    $subject->code,
                    substr($subject->name, 0, 30) . '...',
                    $subject->program_code ?? 'N/A',
                    $subject->credit_hours ?? 'N/A',
                    $subject->is_active ? 'Active' : 'Inactive'
                ];
            })->toArray()
        );
        
        // Group by program
        $this->info("\n=== SUBJECTS BY PROGRAM ===");
        $subjectsByProgram = $subjects->groupBy('program_code');
        foreach ($subjectsByProgram as $programName => $programSubjects) {
            $this->line("{$programName}: {$programSubjects->count()} subjects");
            foreach ($programSubjects as $subject) {
                $this->line("  - {$subject->code}: {$subject->name}");
            }
        }
        
        // Check for EMBA specifically
        $this->info("\n=== EMBA SUBJECTS ===");
        $embaSubjects = Subject::where('program_code', 'EMBA')->get();
        $this->info("EMBA subjects found: {$embaSubjects->count()}");
        
        if ($embaSubjects->isNotEmpty()) {
            foreach ($embaSubjects as $subject) {
                $this->line("- {$subject->code}: {$subject->name}");
            }
        } else {
            $this->warn("No EMBA subjects found!");
            
            // Check if there are subjects with similar program names
            $similarPrograms = Subject::where('program_code', 'like', '%EMBA%')->get();
            if ($similarPrograms->isNotEmpty()) {
                $this->info("Found subjects with similar program names:");
                foreach ($similarPrograms as $subject) {
                    $this->line("- {$subject->code}: {$subject->name} (Program: {$subject->program_code})");
                }
            }
        }
    }
}