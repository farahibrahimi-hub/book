<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function __construct(protected ReservationService $service)
    {
        $this->middleware(['auth', 'verified', \App\Http\Middleware\EnsureUserIsActive::class]);
    }

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

    public function store(StoreReservationRequest $request)
    {
        $user = Auth::user();
        $book = Book::findOrFail($request->book_id);

        try {
            $this->service->reserve($user, $book, $request->input('notes'));

            return redirect()->route('reservations.index')->with('success', 'Book reserved successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function returnBook(Reservation $reservation)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user->isAdmin() && $reservation->user_id !== $user->id) {
            abort(403);
        }

        try {
            $this->service->return($reservation);

            return redirect()->route('reservations.index')->with('success', 'Book returned successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
