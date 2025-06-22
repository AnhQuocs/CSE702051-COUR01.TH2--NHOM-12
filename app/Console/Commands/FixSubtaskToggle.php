<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subtask;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FixSubtaskToggle extends Command
{
    protected $signature = 'fix:subtask-toggle {subtask_id}';
    protected $description = 'Test and fix subtask toggle functionality';

    public function handle()
    {
        $subtaskId = $this->argument('subtask_id');
        
        try {
            $subtask = Subtask::findOrFail($subtaskId);
            $this->info("Found subtask: {$subtask->title}");
            $this->info("Current status: " . ($subtask->is_completed ? 'completed' : 'not completed'));
            
            // Test toggle
            $oldStatus = $subtask->is_completed;
            $subtask->update(['is_completed' => !$subtask->is_completed]);
            $subtask->refresh();
            
            $this->info("Toggled from {$oldStatus} to {$subtask->is_completed}");
            
            // Test project status
            $project = $subtask->project->fresh();
            $project->load('subtasks');
            
            $this->info("Project progress: {$project->progress_percentage}%");
            $this->info("Project status: {$project->final_status}");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
