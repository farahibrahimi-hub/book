<?php

namespace App\Console\Commands;

use App\Services\ReservationService;
use Illuminate\Console\Command;

class MarkLateReservations extends Command
{
    protected $signature = 'reservations:mark-late';
    protected $description = 'Mark overdue reservations as late';

    public function handle(ReservationService $service)
    {
        $count = $service->markLate();
        $this->info("Marked {$count} reservation(s) as late.");
        return 0;
    }
}
