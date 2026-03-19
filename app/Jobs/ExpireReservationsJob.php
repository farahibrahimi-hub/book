<?php

namespace App\Jobs;

use App\Actions\ApplyPenaltyAction;
use App\Models\Reservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExpireReservationsJob implements ShouldQueue
{
    use Queueable;

    public function handle(ApplyPenaltyAction $penaltyAction): void
    {
        $expired = Reservation::pastExpiration()->with('copy')->get();

        foreach ($expired as $reservation) {
            $reservation->markExpired();

            // Release the copy
            $reservation->copy->markAvailable();

            // Apply penalty to user
            $penaltyAction->execute($reservation->user, $reservation);

            // Dispatch queue processing for this book
            ProcessWaitingQueueJob::dispatch($reservation->copy->book_id);
        }
    }
}
