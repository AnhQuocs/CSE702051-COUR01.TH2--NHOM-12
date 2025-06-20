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
        $query = Project::with(['category', 'tags', 'user'])
            ->where('user_id', Auth::id()); // Only show user's own projects

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        $projects = $query->paginate(12);
        $categories = Category::all();

        return view('projects.index', compact('projects', 'categories'));
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
            'status' => $request->status,
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

        return redirect()->route('projects.index')
            ->with('success', 'Dự án đã được tạo thành công!');
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $project->load(['category', 'tags', 'comments.user', 'user']);
        
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
            'status' => $request->status,
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
