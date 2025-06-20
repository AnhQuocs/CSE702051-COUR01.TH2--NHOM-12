<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subtask extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'is_completed',
        'order',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the project that owns the subtask.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope a query to only include completed subtasks.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope a query to only include pending subtasks.
     */
    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }
}
