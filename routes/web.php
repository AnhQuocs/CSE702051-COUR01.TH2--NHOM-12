<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\UserStatsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Project routes
    Route::resource('projects', ProjectController::class);
    
    // User Statistics route
    Route::get('/stats', [UserStatsController::class, 'index'])->name('stats.index');
    Route::get('/stats/export/{format?}', [UserStatsController::class, 'export'])->name('stats.export');
    Route::get('/stats/report', [UserStatsController::class, 'report'])->name('stats.report');
    
    // Subtask routes
    Route::patch('/subtasks/{subtask}/toggle', [SubtaskController::class, 'toggle'])
          ->name('subtasks.toggle');
    Route::post('/projects/{project}/subtasks', [SubtaskController::class, 'store'])->name('subtasks.store');
    Route::put('/projects/{project}/subtasks/order', [SubtaskController::class, 'updateOrder'])->name('subtasks.updateOrder');
    Route::delete('/subtasks/{subtask}', [SubtaskController::class, 'destroy'])->name('subtasks.destroy');
    
    // Test route
    Route::get('/test-subtasks', function() {
        $subtasks = \App\Models\Subtask::with('project')->take(5)->get();
        return response()->json($subtasks);
    });
    
    Route::get('/test-toggle/{subtask}', function(\App\Models\Subtask $subtask) {
        $oldStatus = $subtask->is_completed;
        $subtask->update(['is_completed' => !$subtask->is_completed]);
        $newStatus = $subtask->fresh()->is_completed;
        
        return response()->json([
            'success' => true,
            'subtask_id' => $subtask->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'message' => "Subtask {$subtask->id} toggled from {$oldStatus} to {$newStatus}"
        ]);
    });
});

// Auto login route for testing
Route::get('/auto-login', function() {
    // Login as the user who owns the first project
    $project = \App\Models\Project::first();
    $user = \App\Models\User::find($project->user_id);
    Auth::login($user);
    return redirect('/dashboard');
});

// Auto login route for specific project
Route::get('/auto-login/{project}', function(\App\Models\Project $project) {
    $user = \App\Models\User::find($project->user_id);
    Auth::login($user);
    return redirect("/projects/{$project->id}");
});

// Test route without auth
Route::get('/test-simple', function() {
    return response()->json([
        'success' => true,
        'message' => 'API is working',
        'time' => now()->toISOString()
    ]);
});

// Test project detail without auth (for debugging)
Route::get('/test-project/{project}', function(\App\Models\Project $project) {
    // Auto login correct user for testing
    if (!Auth::check()) {
        $user = \App\Models\User::find($project->user_id);
        Auth::login($user);
    }
    
    $project->load(['subtasks', 'category', 'tags']);
    return view('projects.show', compact('project'));
});

// Test subtask toggle without auth (for debugging)
Route::patch('/test-toggle/{subtask}', function(\App\Models\Subtask $subtask) {
    $oldStatus = $subtask->is_completed;
    $subtask->update(['is_completed' => !$subtask->is_completed]);
    
    // Reload project with fresh data
    $project = $subtask->project->fresh();
    $project->load('subtasks');
    
    return response()->json([
        'success' => true,
        'subtask' => [
            'id' => $subtask->id,
            'is_completed' => $subtask->fresh()->is_completed,
            'title' => $subtask->title,
        ],
        'project' => [
            'id' => $project->id,
            'progress_percentage' => $project->progress_percentage,
            'final_status' => $project->final_status,
            'subtasks_count' => $project->subtasks->count(),
            'completed_subtasks_count' => $project->subtasks->where('is_completed', true)->count(),
        ],
    ]);
});

// Test project detail without auth (for debugging)
Route::get('/test-project/{project}', function(\App\Models\Project $project) {
    $project->load(['subtasks', 'category', 'tags']);
    return view('projects.show', compact('project'));
});

// Test toggle page
Route::get('/test-toggle-page', function() {
    // Login as owner of first project
    $project = \App\Models\Project::first();
    $user = \App\Models\User::find($project->user_id);
    Auth::login($user);
    return view('test-toggle');
});

require __DIR__.'/auth.php';
