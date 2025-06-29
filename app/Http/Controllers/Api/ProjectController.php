<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $projects = Project::where('user_id', $user->id)->get();
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();
        $validated['user_id'] = $user->id;
        
        $project = Project::create($validated);
        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $project = Project::where('user_id', $user->id)->findOrFail($id);
        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectRequest $request, string $id)
    {
        $user = Auth::user();
        $project = Project::where('user_id', $user->id)->findOrFail($id);
        
        $project->update($request->validated());
        
        return response()->json([
            'message' => 'Project updated successfully',
            'data' => $project->fresh()
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
        return response()->json(['message' => 'Deleted successfully']);
    }
}
