<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->authorizeResource(Book::class, null, ['only' => []]);
    }

    public function dashboard(ReservationService $reservationService)
    {
        $this->authorize('create', Book::class);

        $stats = $reservationService->getStats();

        return view('admin.dashboard', [
            'totalBooks' => Book::count(),
            'totalUsers' => User::count(),
            'reservationStats' => $stats,
        ]);
    }

    public function books()
    {
        $this->authorize('create', Book::class);

        $books = Book::withCount([
            'copies',
            'copies as available_copies_count' => fn ($q) => $q->available(),
        ])->paginate(12);

        return view('admin.books.index', compact('books'));
    }

    public function reservations()
    {
        $this->authorize('create', Book::class);

        $reservations = Reservation::with(['user', 'copy.book'])->paginate(12);

        return view('admin.reservations.index', compact('reservations'));
    }
}
