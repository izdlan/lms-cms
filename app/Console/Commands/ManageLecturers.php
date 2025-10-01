<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Lecturer;

class ManageLecturers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lecturers:manage 
                            {action : Action to perform (list, keep-two, delete-all)}
                            {--keep-ids= : Comma-separated IDs to keep when using keep-two action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage lecturers in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'list':
                $this->listLecturers();
                break;
            case 'keep-two':
                $this->keepTwoLecturers();
                break;
            case 'delete-all':
                $this->deleteAllLecturers();
                break;
            default:
                $this->error('Invalid action. Use: list, keep-two, or delete-all');
        }
    }
    
    private function listLecturers()
    {
        $lecturers = User::where('role', 'lecturer')->get(['id', 'name', 'email']);
        
        $this->info("Current lecturers ({$lecturers->count()}):");
        $this->table(
            ['ID', 'Name', 'Email'],
            $lecturers->map(function($lecturer) {
                return [$lecturer->id, $lecturer->name, $lecturer->email];
            })->toArray()
        );
    }
    
    private function keepTwoLecturers()
    {
        $lecturers = User::where('role', 'lecturer')->get();
        
        if ($lecturers->count() <= 2) {
            $this->info("Already have {$lecturers->count()} lecturers. No action needed.");
            return;
        }
        
        $keepIds = $this->option('keep-ids');
        
        if ($keepIds) {
            $idsToKeep = array_map('intval', explode(',', $keepIds));
        } else {
            // Keep the first two by default
            $idsToKeep = $lecturers->take(2)->pluck('id')->toArray();
        }
        
        $this->info("Keeping lecturers with IDs: " . implode(', ', $idsToKeep));
        
        // Delete lecturers not in the keep list
        $lecturersToDelete = $lecturers->whereNotIn('id', $idsToKeep);
        
        $this->info("Deleting {$lecturersToDelete->count()} lecturers:");
        foreach ($lecturersToDelete as $lecturer) {
            $this->line("- {$lecturer->name} ({$lecturer->email})");
        }
        
        if ($this->confirm('Are you sure you want to delete these lecturers?')) {
            $deletedCount = 0;
            foreach ($lecturersToDelete as $lecturer) {
                // Also delete associated lecturer profile
                Lecturer::where('user_id', $lecturer->id)->delete();
                $lecturer->delete();
                $deletedCount++;
            }
            
            $this->info("Successfully deleted {$deletedCount} lecturers.");
            
            // Show remaining lecturers
            $remaining = User::where('role', 'lecturer')->get(['id', 'name', 'email']);
            $this->info("Remaining lecturers ({$remaining->count()}):");
            foreach ($remaining as $lecturer) {
                $this->line("- ID: {$lecturer->id}, Name: {$lecturer->name}, Email: {$lecturer->email}");
            }
        } else {
            $this->info('Operation cancelled.');
        }
    }
    
    private function deleteAllLecturers()
    {
        $lecturers = User::where('role', 'lecturer')->get();
        
        if ($lecturers->isEmpty()) {
            $this->info('No lecturers found.');
            return;
        }
        
        $this->warn("This will delete ALL {$lecturers->count()} lecturers:");
        foreach ($lecturers as $lecturer) {
            $this->line("- {$lecturer->name} ({$lecturer->email})");
        }
        
        if ($this->confirm('Are you sure you want to delete ALL lecturers?')) {
            $deletedCount = 0;
            foreach ($lecturers as $lecturer) {
                // Also delete associated lecturer profile
                Lecturer::where('user_id', $lecturer->id)->delete();
                $lecturer->delete();
                $deletedCount++;
            }
            
            $this->info("Successfully deleted {$deletedCount} lecturers.");
        } else {
            $this->info('Operation cancelled.');
        }
    }
}