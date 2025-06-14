<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ProjectStatsController extends Controller
{
    public function stats(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now()->toDateString();
        $projects = Project::where('user_id', $user->id)->get();

        $total = $projects->count();
        $completed = $projects->where('status', 'Đã hoàn thành')->count();
        $overdue = $projects->where('status', '!=', 'Đã hoàn thành')
            ->where('deadline', '<', $now)->count();
        $incomplete = $total - $completed;

        $completedPercent = $total > 0 ? round($completed / $total * 100, 2) : 0;
        $incompletePercent = $total > 0 ? round($incomplete / $total * 100, 2) : 0;
        $overduePercent = $total > 0 ? round($overdue / $total * 100, 2) : 0;

        return response()->json([
            'total' => $total,
            'completed' => $completed,
            'incomplete' => $incomplete,
            'overdue' => $overdue,
            'completed_percent' => $completedPercent,
            'incomplete_percent' => $incompletePercent,
            'overdue_percent' => $overduePercent,
        ]);
    }
}
