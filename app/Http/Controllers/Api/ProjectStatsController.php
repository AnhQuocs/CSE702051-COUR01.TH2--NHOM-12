<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class ProjectStatsController extends Controller
{    public function stats(Request $request)
    {
        $user = Auth::user();
        
        // Cache stats for 5 minutes per user
        $cacheKey = "user_{$user->id}_project_stats";
        
        $stats = Cache::remember($cacheKey, 300, function () use ($user) {
            $now = Carbon::now()->toDateString();
            $baseQuery = Project::byUser($user->id);
            
            // Sử dụng database aggregation thay vì load all vào memory
            $total = $baseQuery->count();
            $completed = $baseQuery->completed()->count();
            $overdue = $baseQuery->overdue()->count();
            $incomplete = $total - $completed;
            
            $completedPercent = $total > 0 ? round($completed / $total * 100, 2) : 0;
            $incompletePercent = $total > 0 ? round($incomplete / $total * 100, 2) : 0;
            $overduePercent = $total > 0 ? round($overdue / $total * 100, 2) : 0;
            
            return [
                'total' => $total,
                'completed' => $completed,
                'incomplete' => $incomplete,
                'overdue' => $overdue,
                'completed_percent' => $completedPercent,
                'incomplete_percent' => $incompletePercent,
                'overdue_percent' => $overduePercent,
                'cache_expires_at' => now()->addMinutes(5)->toISOString(),
            ];
        });

        return response()->json($stats);
    }
}
