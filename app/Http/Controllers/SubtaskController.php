<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubtaskController extends Controller
{
    /**
     * Toggle subtask completion status
     */
    public function toggle(Subtask $subtask)
    {
        try {
            Log::info('Subtask toggle called', [
                'subtask_id' => $subtask->id,
                'current_status' => $subtask->is_completed,
                'user_id' => Auth::id(),
                'project_user_id' => $subtask->project->user_id
            ]);

            // Check if user owns the project
            if ($subtask->project->user_id !== Auth::id()) {
                Log::error('Unauthorized subtask toggle attempt');
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $oldStatus = $subtask->is_completed;
            $newStatus = !$subtask->is_completed;

            // Update subtask
            $updated = $subtask->update([
                'is_completed' => $newStatus
            ]);

            if (!$updated) {
                Log::error('Failed to update subtask');
                return response()->json(['success' => false, 'error' => 'Update failed'], 500);
            }

            // Verify the update
            $subtask->refresh();
            
            Log::info('Subtask updated', [
                'subtask_id' => $subtask->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'saved_status' => $subtask->is_completed,
                'update_successful' => $updated
            ]);

            // Reload project with fresh data
            $project = $subtask->project->fresh();
            $project->load('subtasks');

            return response()->json([
                'success' => true,
                'subtask' => [
                    'id' => $subtask->id,
                    'is_completed' => $subtask->is_completed,
                    'title' => $subtask->title,
                ],
                'project' => [
                    'id' => $project->id,
                    'progress_percentage' => $project->progress_percentage,
                    'final_status' => $project->final_status,
                    'subtasks_count' => $project->subtasks->count(),
                    'completed_subtasks_count' => $project->subtasks->where('is_completed', true)->count(),
                ],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Exception in subtask toggle', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add new subtask to project
     */
    public function store(Request $request, Project $project)
    {
        // Check if user owns the project
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $maxOrder = $project->subtasks()->max('order') ?? -1;

        $subtask = $project->subtasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'order' => $maxOrder + 1,
            'is_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'subtask' => $subtask,
            'progress' => $project->progress_percentage,
            'status' => $project->final_status,
        ]);
    }

    /**
     * Delete subtask
     */
    public function destroy(Subtask $subtask)
    {
        // Check if user owns the project
        if ($subtask->project->user_id !== Auth::id()) {
            abort(403);
        }

        $project = $subtask->project;
        $subtask->delete();

        // Refresh project to get updated counts
        $project->refresh();
        $project->load('subtasks');

        return response()->json([
            'success' => true,
            'progress' => $project->progress_percentage,
            'status' => $project->final_status,
            'completed_subtasks' => $project->subtasks->where('is_completed', true)->count(),
            'total_subtasks' => $project->subtasks->count(),
        ]);
    }
}
