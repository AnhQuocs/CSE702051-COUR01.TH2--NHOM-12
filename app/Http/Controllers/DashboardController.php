<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Thống kê nhanh
        $stats = [
            'total' => Project::where('user_id', $user->id)->count(),
            'completed' => Project::where('user_id', $user->id)->completed()->count(),
            'in_progress' => Project::where('user_id', $user->id)->where('status', 'Đang thực hiện')->count(),
            'overdue' => Project::where('user_id', $user->id)->overdue()->count(),
        ];

        return view('dashboard', compact('projects', 'stats'));
    }
}
