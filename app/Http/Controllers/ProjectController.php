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
            $statusFilter = $request->status;
            // Note: Since final_status is computed from subtasks, we need to filter after loading
            // For better performance, consider adding a cached status field to projects table
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
        
        if (in_array($sortBy, ['created_at', 'updated_at', 'start_date', 'end_date', 'title'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Get projects first
        $allProjects = $query->get();
        
        // Filter by computed final_status if needed
        if ($request->filled('status')) {
            $allProjects = $allProjects->filter(function ($project) use ($request) {
                return $project->final_status === $request->status;
            });
        }
        
        // Paginate the filtered results
        $currentPage = $request->get('page', 1);
        $perPage = 12;
        $projects = new \Illuminate\Pagination\LengthAwarePaginator(
            $allProjects->forPage($currentPage, $perPage),
            $allProjects->count(),
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

}
