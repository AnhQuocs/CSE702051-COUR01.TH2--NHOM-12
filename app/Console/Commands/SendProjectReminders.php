<?php

namespace App\Console\Commands;

use App\Mail\ProjectReminderMail;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class SendProjectReminders extends Command
{
    protected $signature = 'projects:send-reminders';
    protected $description = 'Send reminder emails for projects with upcoming reminder_time';

    public function handle()
    {
        $now = Carbon::now();
        $projects = Project::whereNotNull('reminder_time')
            ->where('reminder_time', '<=', $now)
            ->whereNotIn('status', ['Đã hoàn thành', 'Hoàn thành muộn'])
            ->with('user:id,email,name') // Eager load user
            ->limit(100) // Giới hạn 100 projects mỗi lần chạy
            ->get();

        $sentCount = 0;
        foreach ($projects as $project) {
            if ($project->user && $project->user->email) {
                try {
                    Mail::to($project->user->email)->send(new ProjectReminderMail($project));
                    
                    // Reset reminder_time sau khi gửi thành công
                    $project->update(['reminder_time' => null]);
                    $sentCount++;
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder for project {$project->id}: {$e->getMessage()}");
                }
            }
        }
        
        $this->info("Project reminders sent successfully. Total: {$sentCount}");
    }
}
