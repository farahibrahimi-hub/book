<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Carbon;

class ReservationService
{
    public function reserve(User $user, Book $book, ?string $notes = null): Reservation
    {
        $activeReservations = $user->reservations()->where('status', 'active')->count();
        if ($activeReservations >= 3) {
            throw new \InvalidArgumentException('Max 3 active reservations allowed.');
        }

        if ($book->available_copies < 1) {
            throw new \InvalidArgumentException('No copies available.');
        }

        $existing = $user->reservations()->where('book_id', $book->id)->where('status', 'active')->first();
        if ($existing) {
            throw new \InvalidArgumentException('You already reserved this book.');
        }

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'reserved_at' => now(),
            'return_date' => now()->addDays(7),
            'status' => 'active',
            'notes' => $notes,
        ]);

        $book->decrement('available_copies');

        return $reservation;
    }

    public function return(Reservation $reservation): Reservation
    {
        if ($reservation->status !== 'active' && $reservation->status !== 'late') {
            throw new \InvalidArgumentException('This reservation cannot be returned.');
        }

        $reservation->status = 'returned';
        $reservation->returned_at = now();
        $reservation->save();

        $reservation->book->increment('available_copies');

        return $reservation;
    }

    public function markLate(): int
    {
        $toLate = Reservation::where('status', 'active')->whereDate('return_date', '<', now()->toDateString())->get();
        foreach ($toLate as $reservation) {
            $reservation->status = 'late';
            $reservation->save();
        }

        return $toLate->count();
    }
}