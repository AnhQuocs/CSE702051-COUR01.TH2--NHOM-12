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
        'title',
        'description',
        'priority',
        'status',
        'deadline',
        'reminder_time',
        'completed_late',
    ];

    protected $casts = [
        'deadline' => 'date',
        'reminder_time' => 'datetime',
        'completed_late' => 'boolean',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes for better query performance
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['Đã hoàn thành', 'Hoàn thành muộn']);
    }

    public function scopeIncomplete($query)
    {
        return $query->whereNotIn('status', ['Đã hoàn thành', 'Hoàn thành muộn']);
    }

    public function scopeOverdue($query)
    {
        return $query->incomplete()->where('deadline', '<', now()->toDateString());
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
