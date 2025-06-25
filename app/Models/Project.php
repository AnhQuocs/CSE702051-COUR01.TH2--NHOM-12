<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Project extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'priority',
        'status',
        'start_date',
        'end_date',
        'reminder_time',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reminder_time' => 'datetime',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'project_tag');
    }

    public function subtasks()
    {
        return $this->hasMany(Subtask::class)->orderBy('order');
    }

    /**
     * Calculate progress percentage based on completed subtasks
     */
    public function getProgressPercentageAttribute()
    {
        $totalSubtasks = $this->subtasks()->count();
        
        if ($totalSubtasks === 0) {
            return 0;
        }
        
        $completedSubtasks = $this->subtasks()->completed()->count();
        
        return round(($completedSubtasks / $totalSubtasks) * 100);
    }

    /**
     * Get automatic status based on subtasks completion
     */
    public function getAutoStatusAttribute()
    {
        $totalSubtasks = $this->subtasks()->count();
        $completedSubtasks = $this->subtasks()->completed()->count();
        
        // If no subtasks exist - project hasn't been planned yet
        if ($totalSubtasks === 0) {
            return 'not_planned';
        }
        
        // If all subtasks completed
        if ($completedSubtasks === $totalSubtasks) {
            return 'completed';
        }
        
        // If some subtasks completed
        if ($completedSubtasks > 0) {
            return 'in_progress';
        }
        
        // If no subtasks completed but subtasks exist
        return 'not_started';
    }

    /**
     * Check if project is overdue
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->end_date || $this->auto_status === 'completed') {
            return false;
        }
        
        return $this->end_date < now()->toDateString();
    }

    /**
     * Get final status (considering overdue)
     */
    public function getFinalStatusAttribute()
    {
        if ($this->is_overdue && $this->auto_status !== 'completed') {
            return 'overdue';
        }
        
        return $this->auto_status;
    }

    // Scopes for better query performance
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeIncomplete($query)
    {
        return $query->where('status', '!=', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->incomplete()
            ->where('end_date', '<', now()->toDateString())
            ->whereNotNull('end_date');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
