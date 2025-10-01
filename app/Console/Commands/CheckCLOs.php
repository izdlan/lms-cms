<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CourseClo;
use App\Models\Subject;

class CheckCLOs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clos:check {--subject= : Check CLOs for specific subject}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check CLOs in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subjectCode = $this->option('subject');
        
        $this->info("=== COURSE LEARNING OUTCOMES (CLOs) ===");
        
        $query = CourseClo::query();
        if ($subjectCode) {
            $query->where('subject_code', $subjectCode);
        }
        
        $clos = $query->orderBy('subject_code')->orderBy('order')->get();
        
        if ($clos->isEmpty()) {
            $this->warn('No CLOs found!');
            return;
        }
        
        $this->info("Total CLOs: {$clos->count()}");
        
        // Group by subject
        $closBySubject = $clos->groupBy('subject_code');
        
        foreach ($closBySubject as $subjectCode => $subjectClos) {
            $subject = Subject::where('code', $subjectCode)->first();
            $subjectName = $subject ? $subject->name : 'Unknown Subject';
            
            $this->line("\n{$subjectCode} - {$subjectName} ({$subjectClos->count()} CLOs):");
            
            foreach ($subjectClos as $clo) {
                $this->line("  {$clo->clo_code}: {$clo->description}");
                $this->line("    MQF Alignment: {$clo->mqf_alignment}");
            }
        }
        
        // Show specific subject if requested
        if ($subjectCode) {
            $this->info("\n=== DETAILED CLOs FOR {$subjectCode} ===");
            $subjectClos = CourseClo::where('subject_code', $subjectCode)->orderBy('order')->get();
            
            $this->table(
                ['CLO Code', 'Description', 'MQF Alignment', 'Order'],
                $subjectClos->map(function($clo) {
                    return [
                        $clo->clo_code,
                        substr($clo->description, 0, 60) . '...',
                        $clo->mqf_alignment,
                        $clo->order
                    ];
                })->toArray()
            );
        }
    }
}