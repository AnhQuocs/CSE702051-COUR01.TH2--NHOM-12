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
        $query = Project::byUser($user->id)->with('user:id,name,email');

        // Lọc theo mức độ ưu tiên nếu có
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        // Lọc theo trạng thái nếu có
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sắp xếp
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Phân trang
        $perPage = min($request->input('per_page', 10), 50); // Giới hạn tối đa 50
        $projects = $query->paginate($perPage);
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
        $project->load('user:id,name,email');
        
        // Clear cache sau khi tạo project mới
        Cache::forget("user_{$user->id}_project_stats");
        
        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $project = Project::byUser($user->id)->with('user:id,name,email')->findOrFail($id);
        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectRequest $request, string $id)
    {
        $user = Auth::user();
        $project = Project::byUser($user->id)->findOrFail($id);
        $validated = $request->validated();
        
        // Nếu chuyển sang hoàn thành và đã quá hạn thì đánh completed_late
        if (($validated['status'] === 'Đã hoàn thành' || $validated['status'] === 'Hoàn thành muộn') && $project->deadline < now()->toDateString()) {
            $validated['status'] = 'Hoàn thành muộn';
            $validated['completed_late'] = true;
        } elseif ($validated['status'] === 'Đã hoàn thành') {
            $validated['completed_late'] = false;
        }
        
        $project->update($validated);
        $project->load('user:id,name,email');
        
        // Clear cache sau khi update
        Cache::forget("user_{$user->id}_project_stats");
        
        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $project = Project::byUser($user->id)->findOrFail($id);
        $project->delete();
        
        // Clear cache sau khi xóa
        Cache::forget("user_{$user->id}_project_stats");
        
        return response()->json(['message' => 'Project deleted successfully'], 200);
    }
}
