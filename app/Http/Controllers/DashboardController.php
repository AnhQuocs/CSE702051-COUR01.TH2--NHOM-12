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
        
        // Lấy projects với relationships
        $projects = Project::where('user_id', $user->id)
            ->with(['category:id,name,color', 'tags:id,name,color'])
            ->orderBy('created_at', 'desc')
            ->take(10) // Giới hạn 10 projects gần nhất
            ->get();

        // Thống kê nhanh với status mới
        $stats = [
            'total' => Project::where('user_id', $user->id)->count(),
            'completed' => Project::where('user_id', $user->id)->where('status', 'completed')->count(),
            'in_progress' => Project::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'overdue' => Project::where('user_id', $user->id)
                ->where('status', '!=', 'completed')
                ->where('end_date', '<', Carbon::today())
                ->whereNotNull('end_date')
                ->count(),
        ];

        return view('dashboard', compact('projects', 'stats'));
    }
}
