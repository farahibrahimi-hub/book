<?php

namespace App\Actions;

use App\Enums\ReservationStatus;
use App\Models\Book;
use App\Models\Copy;
use App\Models\Reservation;
use App\Models\User;
use App\Models\WaitingQueue;
use Illuminate\Support\Facades\DB;

class ReserveBookAction
{
    /**
     * Attempt to reserve a book for a user.
     * If a copy is available, create a reservation with DB locking.
     * If no copies available, add the user to the waiting queue.
     *
     * @return array{reservation: Reservation|null, queued: bool, position: int|null}
     *
     * @throws \InvalidArgumentException
     */
    public function execute(User $user, Book $book, ?string $notes = null): array
    {
        $this->validateUserCanReserve($user, $book);

        return DB::transaction(function () use ($user, $book, $notes) {
            // Lock a single available copy to prevent race conditions
            $copy = Copy::forBook($book->id)
                ->available()
                ->lockForUpdate()
                ->first();

            if ($copy) {
                return $this->createReservation($user, $copy, $notes);
            }

            return $this->enqueueUser($user, $book);
        });
    }

    private function validateUserCanReserve(User $user, Book $book): void
    {
        if (! $user->canReserve()) {
            if ($user->hasActivePenalty()) {
                throw new \InvalidArgumentException('You have an active penalty and cannot reserve books.');
            }

            if ($user->activeReservationsCount() >= 3) {
                throw new \InvalidArgumentException('Max 3 active reservations allowed.');
            }

            throw new \InvalidArgumentException('Your account is not active.');
        }

        // Check if user already has an active reservation for any copy of this book
        $hasExisting = Reservation::where('user_id', $user->id)
            ->where('status', ReservationStatus::Active)
            ->whereHas('copy', fn ($q) => $q->where('book_id', $book->id))
            ->exists();

        if ($hasExisting) {
            throw new \InvalidArgumentException('You already have an active reservation for this book.');
        }

        // Check if user is already in the queue for this book
        $inQueue = WaitingQueue::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->exists();

        if ($inQueue) {
            throw new \InvalidArgumentException('You are already in the waiting queue for this book.');
        }
    }

    /**
     * @return array{reservation: Reservation, queued: false, position: null}
     */
    private function createReservation(User $user, Copy $copy, ?string $notes): array
    {
        $copy->markReserved();

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'copy_id' => $copy->id,
            'reserved_at' => now(),
            'return_date' => now()->addDays(7),
            'expires_at' => now()->addHours(24),
            'status' => ReservationStatus::Active,
            'notes' => $notes,
        ]);

        return [
            'reservation' => $reservation,
            'queued' => false,
            'position' => null,
        ];
    }

    /**
     * @return array{reservation: null, queued: true, position: int}
     */
    private function enqueueUser(User $user, Book $book): array
    {
        $position = WaitingQueue::nextPosition($book->id);

        WaitingQueue::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'position' => $position,
        ]);

        return [
            'reservation' => null,
            'queued' => true,
            'position' => $position,
        ];
    }
}
