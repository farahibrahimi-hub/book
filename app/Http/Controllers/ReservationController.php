<?php

namespace App\Http\Controllers;

use App\Actions\ReserveBookAction;
use App\Actions\ReturnBookAction;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $service,
        protected ReserveBookAction $reserveAction,
        protected ReturnBookAction $returnAction,
    ) {
        $this->middleware(['auth', 'verified', EnsureUserIsActive::class]);
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $reservations = $this->service->listForUser($user);

        return view('reservations.index', compact('reservations'));
    }

    public function store(StoreReservationRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $book = Book::findOrFail($request->book_id);

        try {
            $result = $this->reserveAction->execute($user, $book, $request->input('notes'));

            if ($result['queued']) {
                return redirect()->route('reservations.index')
                    ->with('info', "No copies available. You've been added to the waiting queue at position #{$result['position']}.");
            }

            return redirect()->route('reservations.index')
                ->with('success', 'Book reserved successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function returnBook(Reservation $reservation)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->isAdmin() && $reservation->user_id !== $user->id) {
            abort(403);
        }

        try {
            $this->returnAction->execute($reservation);

            return redirect()->route('reservations.index')
                ->with('success', 'Book returned successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
