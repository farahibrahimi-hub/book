<?php

namespace App\Console\Commands;

use App\Jobs\ExpireReservationsJob;
use App\Services\ReservationService;
use Illuminate\Console\Command;

class MarkLateReservations extends Command
{
    protected $signature = 'reservations:process-overdue';

    protected $description = 'Mark overdue reservations as late and process expired reservations';

    public function handle(ReservationService $service): int
    {
        // Mark late reservations
        $lateCount = $service->markLate();
        $this->info("Marked {$lateCount} reservation(s) as late.");

        // Process expired reservations
        ExpireReservationsJob::dispatchSync();
        $this->info('Processed expired reservations.');

        return self::SUCCESS;
    }
}
