<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Category;
use App\Models\Tag;
use App\Http\Requests\ProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index(Request $request)
    {
        $query = Project::with(['category', 'tags', 'user']);

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
    public function destroy(Project $project)
    {
        $project->delete();
        
        return redirect()->route('projects.index')
            ->with('success', 'Dự án đã được xóa thành công!');
    }
}
