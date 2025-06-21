<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SubtaskController;
use Illuminate\Support\Facades\Route;

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
    
    // Subtask routes with logging
    Route::patch('/subtasks/{subtask}/toggle', [SubtaskController::class, 'toggle'])
          ->middleware(\App\Http\Middleware\LogRequests::class)
          ->name('subtasks.toggle');
    Route::post('/projects/{project}/subtasks', [SubtaskController::class, 'store'])->name('subtasks.store');
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

require __DIR__.'/auth.php';
