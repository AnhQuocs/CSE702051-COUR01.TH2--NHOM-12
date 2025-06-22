<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Project;
use App\Models\Subtask;

class UserStatsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Basic statistics with eager loading
        $projects = Project::with(['subtasks'])->where('user_id', $user->id)->get();
        $totalProjects = $projects->count();
        
        // Calculate completed projects based on final_status
        $completedProjects = $projects->filter(function($project) {
            return $project->final_status === 'completed';
        })->count();
        
        $totalSubtasks = $projects->sum(function($project) {
            return $project->subtasks->count();
        });
        
        $completedSubtasks = $projects->sum(function($project) {
            return $project->subtasks->where('is_completed', true)->count();
        });
        
        // Projects by status using computed property
        $projectsByStatus = [
            'not_planned' => 0,
            'not_started' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'overdue' => 0
        ];
        
        foreach ($projects as $project) {
            $status = $project->final_status;
            if (isset($projectsByStatus[$status])) {
                $projectsByStatus[$status]++;
            }
        }
        
        // Projects by priority from in-memory data
        $projectsByPriority = [
            'low' => 0,
            'medium' => 0, 
            'high' => 0
        ];
        
        foreach ($projects as $project) {
            if (isset($projectsByPriority[$project->priority])) {
                $projectsByPriority[$project->priority]++;
            }
        }
        
        // Recent activity (last 30 days)
        $recentProjects = $projects->filter(function($project) {
            return $project->created_at >= Carbon::now()->subDays(30);
        })->sortByDesc('created_at')->take(5);
        
        $recentSubtasks = collect();
        foreach ($projects as $project) {
            $projectRecentSubtasks = $project->subtasks
                ->where('updated_at', '>=', Carbon::now()->subDays(30))
                ->where('is_completed', true)
                ->sortByDesc('updated_at')
                ->take(10);
            $recentSubtasks = $recentSubtasks->merge($projectRecentSubtasks);
        }
        $recentSubtasks = $recentSubtasks->sortByDesc('updated_at')->take(10);
        
        // Monthly activity chart data - optimized with in-memory data
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            
            $monthProjects = $projects->filter(function($project) use ($month) {
                return $project->created_at->year == $month->year && 
                       $project->created_at->month == $month->month;
            })->count();
            
            $monthCompletedSubtasks = 0;
            foreach ($projects as $project) {
                $monthCompletedSubtasks += $project->subtasks->filter(function($subtask) use ($month) {
                    return $subtask->is_completed && 
                           $subtask->updated_at &&
                           $subtask->updated_at->year == $month->year && 
                           $subtask->updated_at->month == $month->month;
                })->count();
            }
            
            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'projects' => $monthProjects,
                'completed_subtasks' => $monthCompletedSubtasks
            ];
        }
        
        // Weekly productivity - optimized
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $dayCompletedSubtasks = 0;
            foreach ($projects as $project) {
                $dayCompletedSubtasks += $project->subtasks->filter(function($subtask) use ($date) {
                    return $subtask->is_completed && 
                           $subtask->updated_at &&
                           $subtask->updated_at->toDateString() == $date->toDateString();
                })->count();
            }
            
            $weeklyData[] = [
                'day' => $date->format('D'),
                'date' => $date->format('M j'),
                'completed' => $dayCompletedSubtasks
            ];
        }
        
        // Average completion time - only for projects with both start and end dates
        $completedProjectsWithDates = $projects->filter(function($project) {
            return $project->start_date && $project->end_date && 
                   $project->end_date >= $project->start_date;
        });
        
        $averageProjectDuration = null;
        if ($completedProjectsWithDates->count() > 0) {
            $totalDays = $completedProjectsWithDates->sum(function($project) {
                return Carbon::parse($project->start_date)->diffInDays(Carbon::parse($project->end_date));
            });
            $averageProjectDuration = $totalDays / $completedProjectsWithDates->count();
        }
        
        // Compare with previous period
        $previousPeriodData = $this->getPreviousPeriodComparison($user);
        
        return view('stats.index', compact(
            'totalProjects',
            'completedProjects', 
            'totalSubtasks',
            'completedSubtasks',
            'projectsByStatus',
            'projectsByPriority',
            'recentProjects',
            'recentSubtasks',
            'monthlyData',
            'weeklyData',
            'averageProjectDuration',
            'previousPeriodData'
        ));
    }

    public function export($format = 'json')
    {
        $user = Auth::user();
        
        // Gather all statistics data
        $projects = Project::with(['subtasks'])->where('user_id', $user->id)->get();
        $totalProjects = $projects->count();
        
        $completedProjects = $projects->filter(function($project) {
            return $project->final_status === 'completed';
        })->count();
        
        $totalSubtasks = $projects->sum(function($project) {
            return $project->subtasks->count();
        });
        
        $completedSubtasks = $projects->sum(function($project) {
            return $project->subtasks->where('is_completed', true)->count();
        });
        
        $projectsByStatus = [
            'not_planned' => 0,
            'not_started' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'overdue' => 0
        ];
        
        foreach ($projects as $project) {
            $status = $project->final_status;
            if (isset($projectsByStatus[$status])) {
                $projectsByStatus[$status]++;
            }
        }
        
        $exportData = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'summary' => [
                'total_projects' => $totalProjects,
                'completed_projects' => $completedProjects,
                'total_subtasks' => $totalSubtasks,
                'completed_subtasks' => $completedSubtasks,
                'completion_rate' => $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 2) : 0,
                'task_completion_rate' => $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100, 2) : 0,
            ],
            'breakdown' => [
                'projects_by_status' => $projectsByStatus,
                'projects_by_priority' => [
                    'low' => $projects->where('priority', 'low')->count(),
                    'medium' => $projects->where('priority', 'medium')->count(),
                    'high' => $projects->where('priority', 'high')->count(),
                ]
            ],
            'exported_at' => now()->toISOString(),
            'exported_format' => $format
        ];
        
        switch ($format) {
            case 'json':
                return response()->json($exportData, 200, [
                    'Content-Disposition' => 'attachment; filename="user_stats_' . now()->format('Y-m-d') . '.json"'
                ]);
                
            case 'csv':
                return $this->exportToCSV($exportData);
                
            default:
                return response()->json(['error' => 'Unsupported format'], 400);
        }
    }
    
    private function exportToCSV($data)
    {
        $filename = 'user_stats_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, ['Metric', 'Value']);
            
            // Summary data
            fputcsv($file, ['Total Projects', $data['summary']['total_projects']]);
            fputcsv($file, ['Completed Projects', $data['summary']['completed_projects']]);
            fputcsv($file, ['Total Subtasks', $data['summary']['total_subtasks']]);
            fputcsv($file, ['Completed Subtasks', $data['summary']['completed_subtasks']]);
            fputcsv($file, ['Project Completion Rate (%)', $data['summary']['completion_rate']]);
            fputcsv($file, ['Task Completion Rate (%)', $data['summary']['task_completion_rate']]);
            
            // Empty row
            fputcsv($file, ['']);
            
            // Status breakdown
            fputcsv($file, ['Status', 'Count']);
            foreach ($data['breakdown']['projects_by_status'] as $status => $count) {
                fputcsv($file, [ucfirst(str_replace('_', ' ', $status)), $count]);
            }
            
            // Empty row
            fputcsv($file, ['']);
            
            // Priority breakdown
            fputcsv($file, ['Priority', 'Count']);
            foreach ($data['breakdown']['projects_by_priority'] as $priority => $count) {
                fputcsv($file, [ucfirst($priority), $count]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function getPreviousPeriodComparison($user)
    {
        // Get data from 30 days ago
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        $previousProjects = Project::with(['subtasks'])
            ->where('user_id', $user->id)
            ->where('created_at', '<', $thirtyDaysAgo)
            ->get();
            
        $previousTotalProjects = $previousProjects->count();
        $previousCompletedProjects = $previousProjects->filter(function($project) {
            return $project->final_status === 'completed';
        })->count();
        
        $previousTotalSubtasks = $previousProjects->sum(function($project) {
            return $project->subtasks->count();
        });
        
        $previousCompletedSubtasks = $previousProjects->sum(function($project) {
            return $project->subtasks->where('is_completed', true)->count();
        });
        
        // Current data for comparison
        $currentProjects = Project::with(['subtasks'])->where('user_id', $user->id)->get();
        $currentTotalProjects = $currentProjects->count();
        $currentCompletedProjects = $currentProjects->filter(function($project) {
            return $project->final_status === 'completed';
        })->count();
        
        $currentTotalSubtasks = $currentProjects->sum(function($project) {
            return $project->subtasks->count();
        });
        
        $currentCompletedSubtasks = $currentProjects->sum(function($project) {
            return $project->subtasks->where('is_completed', true)->count();
        });
        
        return [
            'projects' => [
                'current' => $currentTotalProjects,
                'previous' => $previousTotalProjects,
                'change' => $currentTotalProjects - $previousTotalProjects,
                'percentage' => $previousTotalProjects > 0 ? round((($currentTotalProjects - $previousTotalProjects) / $previousTotalProjects) * 100, 1) : 0
            ],
            'completed_projects' => [
                'current' => $currentCompletedProjects,
                'previous' => $previousCompletedProjects,
                'change' => $currentCompletedProjects - $previousCompletedProjects,
                'percentage' => $previousCompletedProjects > 0 ? round((($currentCompletedProjects - $previousCompletedProjects) / $previousCompletedProjects) * 100, 1) : 0
            ],
            'subtasks' => [
                'current' => $currentTotalSubtasks,
                'previous' => $previousTotalSubtasks,
                'change' => $currentTotalSubtasks - $previousTotalSubtasks,
                'percentage' => $previousTotalSubtasks > 0 ? round((($currentTotalSubtasks - $previousTotalSubtasks) / $previousTotalSubtasks) * 100, 1) : 0
            ],
            'completed_subtasks' => [
                'current' => $currentCompletedSubtasks,
                'previous' => $previousCompletedSubtasks,
                'change' => $currentCompletedSubtasks - $previousCompletedSubtasks,
                'percentage' => $previousCompletedSubtasks > 0 ? round((($currentCompletedSubtasks - $previousCompletedSubtasks) / $previousCompletedSubtasks) * 100, 1) : 0
            ]
        ];
    }
    
    public function report()
    {
        $user = Auth::user();
        
        // Reuse the same data gathering logic from index method
        $projects = Project::with(['subtasks'])->where('user_id', $user->id)->get();
        $totalProjects = $projects->count();
        
        $completedProjects = $projects->filter(function($project) {
            return $project->final_status === 'completed';
        })->count();
        
        $totalSubtasks = $projects->sum(function($project) {
            return $project->subtasks->count();
        });
        
        $completedSubtasks = $projects->sum(function($project) {
            return $project->subtasks->where('is_completed', true)->count();
        });
        
        $projectsByStatus = [
            'not_planned' => 0,
            'not_started' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'overdue' => 0
        ];
        
        foreach ($projects as $project) {
            $status = $project->final_status;
            if (isset($projectsByStatus[$status])) {
                $projectsByStatus[$status]++;
            }
        }
        
        $projectsByPriority = [
            'low' => 0,
            'medium' => 0, 
            'high' => 0
        ];
        
        foreach ($projects as $project) {
            if (isset($projectsByPriority[$project->priority])) {
                $projectsByPriority[$project->priority]++;
            }
        }
        
        $recentProjects = $projects->filter(function($project) {
            return $project->created_at >= Carbon::now()->subDays(30);
        })->sortByDesc('created_at');
        
        $recentSubtasks = collect();
        foreach ($projects as $project) {
            $projectRecentSubtasks = $project->subtasks
                ->where('updated_at', '>=', Carbon::now()->subDays(30))
                ->where('is_completed', true)
                ->sortByDesc('updated_at');
            $recentSubtasks = $recentSubtasks->merge($projectRecentSubtasks);
        }
        $recentSubtasks = $recentSubtasks->sortByDesc('updated_at');
        
        return view('stats.report', compact(
            'totalProjects',
            'completedProjects', 
            'totalSubtasks',
            'completedSubtasks',
            'projectsByStatus',
            'projectsByPriority',
            'recentProjects',
            'recentSubtasks'
        ));
    }
}
