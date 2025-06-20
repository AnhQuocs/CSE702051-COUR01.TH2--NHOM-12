<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubtaskController extends Controller
{
    /**
     * Toggle subtask completion status
     */
    public function toggle(Subtask $subtask)
    {
        // Check if user owns the project
        if ($subtask->project->user_id !== Auth::id()) {
            abort(403);
        }

        $subtask->update([
            'is_completed' => !$subtask->is_completed
        ]);

        return response()->json([
            'success' => true,
            'is_completed' => $subtask->is_completed,
            'progress' => $subtask->project->progress_percentage,
            'status' => $subtask->project->final_status,
        ]);
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

        return response()->json([
            'success' => true,
            'progress' => $project->progress_percentage,
            'status' => $project->final_status,
        ]);
    }
}
