<?php

namespace App\Actions;

use App\Jobs\ProcessWaitingQueueJob;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class ReturnBookAction
{
    /**
     * Return a reservation: mark it returned, release the copy,
     * and dispatch queue processing for the book.
     *
     * @throws \InvalidArgumentException
     */
    public function execute(Reservation $reservation): Reservation
    {
        if (! $reservation->canBeReturned()) {
            throw new \InvalidArgumentException('This reservation cannot be returned.');
        }

        return DB::transaction(function () use ($reservation) {
            $reservation->markReturned();

            $copy = $reservation->copy;
            $copy->markAvailable();

            // Process waiting queue for this book
            $bookId = $copy->book_id;
            ProcessWaitingQueueJob::dispatch($bookId);

            return $reservation->fresh();
        });
    }
}
