<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $projects = Project::where('user_id', $user->id)->get()->map(function ($project) {
            return [
                'id' => $project->id,
                'userId' => $project->user_id,
                'title' => $project->title,
                'description' => $project->description,
                'priority' => $project->priority,
                'status' => $project->status,
                'deadline' => optional($project->deadline)->format('d/m/Y'),
            ];
        });
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:Thấp,Trung bình,Cao',
            'status' => 'required|in:Lên kế hoạch,Đang thực hiện,Đã hoàn thành',
            'deadline' => 'required|date',
        ]);
        $validated['user_id'] = $user->id;
        $project = Project::create($validated);
        return response()->json([
            'id' => $project->id,
            'userId' => $project->user_id,
            'title' => $project->title,
            'description' => $project->description,
            'priority' => $project->priority,
            'status' => $project->status,
            'deadline' => optional($project->deadline)->format('d/m/Y'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $project = Project::where('user_id', $user->id)->findOrFail($id);
        return response()->json([
            'id' => $project->id,
            'userId' => $project->user_id,
            'title' => $project->title,
            'description' => $project->description,
            'priority' => $project->priority,
            'status' => $project->status,
            'deadline' => optional($project->deadline)->format('d/m/Y'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $project = Project::where('user_id', $user->id)->findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:Thấp,Trung bình,Cao',
            'status' => 'required|in:Lên kế hoạch,Đang thực hiện,Đã hoàn thành',
            'deadline' => 'required|date',
        ]);
        $project->update($validated);
        return response()->json([
            'id' => $project->id,
            'userId' => $project->user_id,
            'title' => $project->title,
            'description' => $project->description,
            'priority' => $project->priority,
            'status' => $project->status,
            'deadline' => optional($project->deadline)->format('d/m/Y'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $project = Project::where('user_id', $user->id)->findOrFail($id);
        $project->delete();
        return response()->noContent();
    }
}
