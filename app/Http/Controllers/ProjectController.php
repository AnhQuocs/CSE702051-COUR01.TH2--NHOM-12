<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Category;
use App\Models\Tag;
use App\Http\Requests\ProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index(Request $request)
    {
        $query = Project::with(['category', 'tags', 'user', 'subtasks'])
            ->where('user_id', Auth::id()); // Only show user's own projects

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by tag
        if ($request->filled('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag_id);
            });
        }

        // Filter by status (using computed final_status)
        if ($request->filled('status')) {
            // Since final_status is computed, we need to filter after loading
            // This is temporary - in production you'd want to optimize this
            $statusFilter = $request->status;
        }

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $projects = $query->get(); // Get all first for status filtering
        
        // Filter by computed final_status if needed
        if ($request->filled('status')) {
            $projects = $projects->filter(function ($project) use ($request) {
                return $project->final_status === $request->status;
            });
        }
        
        // Paginate the filtered results
        $currentPage = $request->get('page', 1);
        $perPage = 12;
        $projects = new \Illuminate\Pagination\LengthAwarePaginator(
            $projects->forPage($currentPage, $perPage),
            $projects->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        $categories = Category::all();
        $tags = Tag::where('is_active', true)
            ->withCount(['projects' => function($query) {
                $query->where('user_id', Auth::id());
            }])
            ->orderBy('name')
            ->get();

        return view('projects.index', compact('projects', 'categories', 'tags'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        
        return view('projects.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(ProjectRequest $request)
    {
        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reminder_time' => $request->reminder_time,
            'category_id' => $request->category_id,
            'user_id' => Auth::id(),
        ]);

        // Attach tags
        if ($request->filled('tags')) {
            $project->tags()->attach($request->tags);
        }

        // Create subtasks
        if ($request->filled('subtasks')) {
            foreach ($request->subtasks as $index => $subtaskData) {
                if (!empty($subtaskData['title'])) {
                    $project->subtasks()->create([
                        'title' => $subtaskData['title'],
                        'description' => $subtaskData['description'] ?? null,
                        'order' => $index,
                        'is_completed' => false,
                    ]);
                }
            }
        }

        return redirect()->route('projects.index')
            ->with('success', 'Dự án đã được tạo thành công!');
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $project->load(['category', 'tags', 'comments.user', 'user', 'subtasks' => function($query) {
            $query->orderBy('order');
        }]);
        
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $categories = Category::all();
        $tags = Tag::all();
        $projectTags = $project->tags->pluck('id')->toArray();
        $project->load('subtasks');
        
        return view('projects.edit', compact('project', 'categories', 'tags', 'projectTags'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $project->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reminder_time' => $request->reminder_time,
            'category_id' => $request->category_id,
        ]);

        // Sync tags
        if ($request->filled('tags')) {
            $project->tags()->sync($request->tags);
        } else {
            $project->tags()->detach();
        }

        // Update subtasks
        if ($request->filled('subtasks')) {
            // Delete existing subtasks
            $project->subtasks()->delete();
            
            // Create new subtasks
            foreach ($request->subtasks as $index => $subtaskData) {
                if (!empty($subtaskData['title'])) {
                    $project->subtasks()->create([
                        'title' => $subtaskData['title'],
                        'description' => $subtaskData['description'] ?? null,
                        'order' => $index,
                        'is_completed' => $subtaskData['is_completed'] ?? false,
                    ]);
                }
            }
        } else {
            // If no subtasks provided, delete all existing
            $project->subtasks()->delete();
        }

        return redirect()->route('projects.index')
            ->with('success', 'Dự án đã được cập nhật thành công!');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Request $request, Project $project)
    {
        try {
            Log::info('Attempting to delete project', [
                'project_id' => $project->id,
                'project_user_id' => $project->user_id,
                'current_user_id' => Auth::id(),
                'is_authenticated' => Auth::check(),
                'referrer' => $request->headers->get('referer')
            ]);

            // Check if user is authenticated
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('error', 'Bạn cần đăng nhập để thực hiện thao tác này!');
            }

            // Check if user owns the project
            if ($project->user_id !== Auth::id()) {
                $redirectRoute = $this->getRedirectRoute($request);
                return redirect()->route($redirectRoute)
                    ->with('error', 'Bạn không có quyền xóa dự án này!');
            }

            // Delete the project (cascade will handle relationships)
            $project->delete();
            
            Log::info('Project deleted successfully', ['project_id' => $project->id]);
            
            // Determine where to redirect based on the referrer
            $redirectRoute = $this->getRedirectRoute($request);
            
            return redirect()->route($redirectRoute)
                ->with('success', 'Dự án đã được xóa thành công!');
                
        } catch (\Exception $e) {
            Log::error('Error deleting project: ' . $e->getMessage(), [
                'project_id' => $project->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            $redirectRoute = $this->getRedirectRoute($request);
            return redirect()->route($redirectRoute)
                ->with('error', 'Có lỗi xảy ra khi xóa dự án: ' . $e->getMessage());
        }
    }

    /**
     * Determine redirect route based on request source
     */
    private function getRedirectRoute(Request $request): string
    {
        $referer = $request->headers->get('referer');
        
        // If coming from dashboard, redirect back to dashboard
        if ($referer && str_contains($referer, '/dashboard')) {
            return 'dashboard';
        }
        
        // Default to projects index
        return 'projects.index';
    }

    /**
     * Bulk delete selected projects
     */
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'project_ids' => 'required|array',
                'project_ids.*' => 'exists:projects,id'
            ]);

            $projectIds = $request->project_ids;
            
            // Only delete projects that belong to the current user
            $deletedCount = Project::whereIn('id', $projectIds)
                ->where('user_id', Auth::id())
                ->delete();

            if ($deletedCount > 0) {
                Log::info('Bulk deleted projects', [
                    'user_id' => Auth::id(),
                    'project_ids' => $projectIds,
                    'deleted_count' => $deletedCount
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => "Đã xóa thành công {$deletedCount} dự án.",
                    'deleted_count' => $deletedCount
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Không có dự án nào được xóa.'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Bulk delete failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi xóa dự án.'
            ], 500);
        }
    }

    /**
     * Bulk update status for selected projects
     */
    public function bulkStatus(Request $request)
    {
        try {
            $request->validate([
                'project_ids' => 'required|array',
                'project_ids.*' => 'exists:projects,id',
                'status' => 'required|in:not_planned,not_started,in_progress,completed'
            ]);

            $projectIds = $request->project_ids;
            $status = $request->status;
            
            // Map status to appropriate field updates
            $updates = [];
            switch ($status) {
                case 'not_planned':
                    $updates = ['status' => 'not_planned', 'start_date' => null];
                    break;
                case 'not_started':
                    $updates = ['status' => 'not_started'];
                    break;
                case 'in_progress':
                    $updates = ['status' => 'in_progress'];
                    break;
                case 'completed':
                    $updates = ['status' => 'completed'];
                    break;
            }

            // Only update projects that belong to the current user
            $updatedCount = Project::whereIn('id', $projectIds)
                ->where('user_id', Auth::id())
                ->update($updates);

            if ($updatedCount > 0) {
                $statusLabels = [
                    'not_planned' => 'Chưa lên kế hoạch',
                    'not_started' => 'Chưa bắt đầu',
                    'in_progress' => 'Đang thực hiện',
                    'completed' => 'Hoàn thành'
                ];

                Log::info('Bulk updated project status', [
                    'user_id' => Auth::id(),
                    'project_ids' => $projectIds,
                    'status' => $status,
                    'updated_count' => $updatedCount
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => "Đã cập nhật trạng thái thành '{$statusLabels[$status]}' cho {$updatedCount} dự án.",
                    'updated_count' => $updatedCount
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Không có dự án nào được cập nhật.'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Bulk status update failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi cập nhật trạng thái.'
            ], 500);
        }
    }

    /**
     * Bulk export selected projects
     */
    public function bulkExport(Request $request)
    {
        try {
            $request->validate([
                'project_ids' => 'required|array',
                'project_ids.*' => 'exists:projects,id',
                'format' => 'in:csv,json'
            ]);

            $projectIds = $request->project_ids;
            $format = $request->format ?? 'csv';
            
            // Get projects that belong to the current user
            $projects = Project::with(['category', 'tags', 'subtasks'])
                ->whereIn('id', $projectIds)
                ->where('user_id', Auth::id())
                ->get();

            if ($projects->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Không tìm thấy dự án nào để xuất.'
                ], 400);
            }

            if ($format === 'csv') {
                return $this->exportProjectsAsCsv($projects);
            } else {
                return $this->exportProjectsAsJson($projects);
            }

        } catch (\Exception $e) {
            Log::error('Bulk export failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi xuất dữ liệu.'
            ], 500);
        }
    }

    /**
     * Export projects as CSV
     */
    private function exportProjectsAsCsv($projects)
    {
        $filename = 'projects_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($projects) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for proper UTF-8 encoding in Excel
            fwrite($file, "\xEF\xBB\xBF");
            
            // CSV Headers
            fputcsv($file, [
                'ID',
                'Tiêu đề',
                'Mô tả',
                'Trạng thái',
                'Mức độ ưu tiên',
                'Danh mục',
                'Tags',
                'Ngày bắt đầu',
                'Ngày kết thúc',
                'Tiến độ (%)',
                'Số công việc',
                'Ngày tạo'
            ]);

            // Data rows
            foreach ($projects as $project) {
                $statusLabels = [
                    'not_planned' => 'Chưa lên kế hoạch',
                    'not_started' => 'Chưa bắt đầu',
                    'in_progress' => 'Đang thực hiện',
                    'completed' => 'Hoàn thành',
                    'overdue' => 'Quá hạn'
                ];

                $priorityLabels = [
                    'low' => 'Thấp',
                    'medium' => 'Trung bình',
                    'high' => 'Cao'
                ];

                fputcsv($file, [
                    $project->id,
                    $project->title,
                    $project->description,
                    $statusLabels[$project->final_status] ?? $project->final_status,
                    $priorityLabels[$project->priority] ?? $project->priority,
                    $project->category?->name ?? '',
                    $project->tags->pluck('name')->join(', '),
                    $project->start_date?->format('d/m/Y') ?? '',
                    $project->end_date?->format('d/m/Y') ?? '',
                    $project->progress_percentage,
                    $project->subtasks->count(),
                    $project->created_at->format('d/m/Y H:i')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export projects as JSON
     */
    private function exportProjectsAsJson($projects)
    {
        $filename = 'projects_export_' . date('Y-m-d_H-i-s') . '.json';
        
        $data = $projects->map(function ($project) {
            return [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'status' => $project->final_status,
                'priority' => $project->priority,
                'category' => $project->category?->name,
                'tags' => $project->tags->pluck('name'),
                'start_date' => $project->start_date?->format('Y-m-d'),
                'end_date' => $project->end_date?->format('Y-m-d'),
                'progress_percentage' => $project->progress_percentage,
                'subtasks_count' => $project->subtasks->count(),
                'created_at' => $project->created_at->format('Y-m-d H:i:s'),
                'subtasks' => $project->subtasks->map(function ($subtask) {
                    return [
                        'id' => $subtask->id,
                        'title' => $subtask->title,
                        'description' => $subtask->description,
                        'is_completed' => $subtask->is_completed,
                        'created_at' => $subtask->created_at->format('Y-m-d H:i:s')
                    ];
                })
            ];
        });

        return response()->json([
            'export_date' => date('Y-m-d H:i:s'),
            'projects_count' => $projects->count(),
            'projects' => $data
        ])
        ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
        ->header('Content-Type', 'application/json');
    }
}
