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
        $sortBy = $request->get('sort', 'created_at');
        
        // Lấy projects với relationships và subtasks
        $projectsQuery = Project::where('user_id', $user->id)
            ->with(['category:id,name,color', 'tags:id,name,color', 'subtasks']);
        
        // Sắp xếp theo yêu cầu
        switch ($sortBy) {
            case 'progress':
                // Sắp xếp theo tiến độ hoàn thành (tính toán từ subtasks)
                $projects = $projectsQuery->get()->sortByDesc(function($project) {
                    return $project->progress_percentage;
                })->values()->take(15);
                break;
            case 'deadline':
                // Sắp xếp theo thời hạn (gần nhất trước)
                $projects = $projectsQuery->get()->sortBy(function($project) {
                    if ($project->end_date) {
                        return \Carbon\Carbon::parse($project->end_date)->timestamp;
                    }
                    return PHP_INT_MAX; // Đặt projects không có deadline ở cuối
                })->values()->take(15);
                break;
            case 'priority':
                // Sắp xếp theo mức độ ưu tiên (cao -> trung bình -> thấp)
                $projects = $projectsQuery->get()->sortBy(function($project) {
                    $priorityOrder = ['high' => 1, 'medium' => 2, 'low' => 3];
                    return $priorityOrder[$project->priority] ?? 4;
                })->values()->take(15);
                break;
            default:
                // Mặc định sắp xếp theo ngày tạo mới nhất
                $projects = $projectsQuery->orderBy('created_at', 'desc')->take(15)->get();
        }

        // Thống kê nhanh với auto status
        $allProjects = Project::where('user_id', $user->id)->with('subtasks')->get();
        
        $totalSubtasks = $allProjects->sum(function($project) {
            return $project->subtasks->count();
        });
        
        $completedSubtasks = $allProjects->sum(function($project) {
            return $project->subtasks->where('is_completed', true)->count();
        });
        
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
            'not_planned' => $allProjects->filter(function($project) {
                return $project->final_status === 'not_planned';
            })->count(),
            'total_subtasks' => $totalSubtasks,
            'completed_subtasks' => $completedSubtasks,
        ];

        return view('dashboard', compact('projects', 'stats', 'sortBy'));
    }
}
