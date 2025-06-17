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

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('projects:send-reminders')->everyTenMinutes(); // Thay đổi từ everyMinute sang everyTenMinutes cho production
        // ...existing code...
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}