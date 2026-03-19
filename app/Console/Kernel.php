<?php

namespace App\Console;

use App\Console\Commands\MarkLateReservations;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        MarkLateReservations::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('reservations:mark-late')->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
