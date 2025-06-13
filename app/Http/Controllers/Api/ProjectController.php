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
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Project::where('user_id', $user->id);

        // Lọc theo mức độ ưu tiên nếu có
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        // Lọc theo trạng thái nếu có
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Phân trang
        $perPage = $request->input('per_page', 10);
        $projects = $query->paginate($perPage);
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
        return response()->json($project);
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
