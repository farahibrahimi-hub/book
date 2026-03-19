<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            $reservations = Reservation::with(['user', 'book'])->get();
        } else {
            $reservations = Reservation::with('book')->where('user_id', $user->id)->get();
        }

        foreach ($reservations as $reservation) {
            if ($reservation->isLate()) {
                $reservation->status = 'late';
                $reservation->save();
            }
        }

        return view('reservations.index', compact('reservations'));
    }

    public function store(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();

        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($data['book_id']);

        if ($book->available_copies < 1) {
            return redirect()->back()->with('error', 'No copies available.');
        }

        $activeReservations = $user->reservations()->where('status', 'active')->count();
        if ($activeReservations >= 3) {
            return redirect()->back()->with('error', 'You can only have 3 active reservations.');
        }

        $hasActiveBook = $user->reservations()->where('book_id', $book->id)->where('status', 'active')->exists();
        if ($hasActiveBook) {
            return redirect()->back()->with('error', 'You already have an active reservation for this book.');
        }

        $reservation = $user->reservations()->create([
            'book_id' => $book->id,
            'reserved_at' => now(),
            'return_date' => now()->addDays(7),
            'status' => 'active',
        ]);

        $book->decrement('available_copies');

        return redirect()->route('reservations.index')->with('success', 'Book reserved successfully.');
    }

    public function returnBook(Reservation $reservation)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user->isAdmin() && $reservation->user_id !== $user->id) {
            abort(403);
        }

        if ($reservation->status === 'returned') {
            return redirect()->back()->with('error', 'Reservation already returned.');
        }

        $reservation->status = 'returned';
        $reservation->save();

        $reservation->book->increment('available_copies');

        return redirect()->route('reservations.index')->with('success', 'Book returned successfully.');
    }
}
