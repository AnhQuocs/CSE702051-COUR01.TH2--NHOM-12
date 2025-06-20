<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Lấy projects với relationships và subtasks
        $projects = Project::where('user_id', $user->id)
            ->with(['category:id,name,color', 'tags:id,name,color', 'subtasks'])
            ->orderBy('created_at', 'desc')
            ->take(10) // Giới hạn 10 projects gần nhất
            ->get();

        // Thống kê nhanh với auto status
        $allProjects = Project::where('user_id', $user->id)->with('subtasks')->get();
        
        $stats = [
            'total' => $allProjects->count(),
            'completed' => $allProjects->filter(function($project) {
                return $project->final_status === 'completed';
            })->count(),
            'in_progress' => $allProjects->filter(function($project) {
                return $project->final_status === 'in_progress';
            })->count(),
            'overdue' => $allProjects->filter(function($project) {
                return $project->final_status === 'overdue';
            })->count(),
        ];

        return view('dashboard', compact('projects', 'stats'));
    }
}
