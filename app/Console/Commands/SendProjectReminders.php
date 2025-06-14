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
            ->where('status', '!=', 'Đã hoàn thành')
            ->where('status', '!=', 'Hoàn thành muộn')
            ->get();

        foreach ($projects as $project) {
            if ($project->user && $project->user->email) {
                Mail::to($project->user->email)->send(new ProjectReminderMail($project));
            }
        }
        $this->info('Project reminders sent successfully.');
    }
}
