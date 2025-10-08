<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckEnrollmentSchema extends Command
{
    protected $signature = 'enrollments:check-schema';
    protected $description = 'Check the student_enrollments table schema';

    public function handle()
    {
        $this->info('Checking student_enrollments table schema...');
        
        $schema = DB::select('DESCRIBE student_enrollments');
        
        foreach ($schema as $column) {
            if ($column->Field === 'status') {
                $this->info("Status column type: {$column->Type}");
                $this->info("Status column null: {$column->Null}");
                $this->info("Status column default: {$column->Default}");
                break;
            }
        }
        
        // Check current status values
        $statuses = DB::table('student_enrollments')
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();
            
        $this->info("\nCurrent status distribution:");
        foreach ($statuses as $status) {
            $this->info("Status '{$status->status}': {$status->count} records");
        }
        
        return 0;
    }
}
