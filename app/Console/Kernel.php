<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SendProjectReminders;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        SendProjectReminders::class,
        // ...existing code...
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('projects:send-reminders')->everyMinute();
        // ...existing code...
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}