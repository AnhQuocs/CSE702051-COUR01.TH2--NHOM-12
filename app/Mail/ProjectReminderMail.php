<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProjectReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function build()
    {
        // Debug: Xem project có những thuộc tính gì
        Log::info('Project attributes:', $this->project->getAttributes());
        Log::info('Project fillable:', $this->project->getFillable());
        
        // Kiểm tra title an toàn
        $title = $this->project->getAttribute('title') ?? 'Dự án không có tên';
        
        return $this->subject('Nhắc nhở: Dự án sắp đến hạn - ' . $title)
            ->view('emails.project_reminder');
    }
}
