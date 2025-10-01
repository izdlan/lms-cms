<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subject;
use App\Models\CourseClo;

class TestCourseCLOs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:course-clos {subject_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test CLOs loading for a specific subject';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subjectCode = $this->argument('subject_code');
        
        $this->info("Testing CLOs for subject: {$subjectCode}");
        
        // Test direct CLO query
        $clos = CourseClo::where('subject_code', $subjectCode)->orderBy('order')->get();
        $this->info("Direct CLO query: {$clos->count()} CLOs found");
        
        // Test subject with CLOs relationship
        $subject = Subject::with('clos')->where('code', $subjectCode)->first();
        
        if (!$subject) {
            $this->error("Subject {$subjectCode} not found!");
            return;
        }
        
        $this->info("Subject found: {$subject->name}");
        $this->info("CLOs via relationship: {$subject->clos->count()} CLOs found");
        
        // Test the same format as in controller
        $subjectDetails = [
            'name' => $subject->name,
            'description' => $subject->description,
            'assessment' => $subject->assessment_methods ?? 'TBA',
            'duration' => $subject->duration ?? '4 weeks',
            'clos' => $subject->clos->map(function($clo) {
                return [
                    'clo' => $clo->clo_code,
                    'description' => $clo->description,
                    'mqf' => $clo->mqf_alignment
                ];
            })->toArray(),
        ];
        
        $this->info("Formatted CLOs: " . count($subjectDetails['clos']) . " CLOs");
        
        if (count($subjectDetails['clos']) > 0) {
            $this->info("Sample CLO:");
            $firstClo = $subjectDetails['clos'][0];
            $this->line("  CLO: {$firstClo['clo']}");
            $this->line("  Description: {$firstClo['description']}");
            $this->line("  MQF: {$firstClo['mqf']}");
        }
    }
}