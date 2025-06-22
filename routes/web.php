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
    ->middleware(['auth', 'verified', 'prevent.back', 'check.auth'])
    ->name('dashboard');

Route::middleware(['auth', 'prevent.back', 'check.auth'])->group(function () {
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
    Route::delete('/subtasks/{subtask}', [SubtaskController::class, 'destroy'])->name('subtasks.destroy');

});



require __DIR__.'/auth.php';
