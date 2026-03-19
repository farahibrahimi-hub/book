<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        return view('admin.dashboard', [
            'totalBooks' => Book::count(),
            'totalReservations' => Reservation::count(),
            'lateReservations' => Reservation::where('status', 'late')->count(),
        ]);
    }

    public function books()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $books = Book::paginate(12);

        return view('admin.books.index', compact('books'));
    }

    public function reservations()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $reservations = Reservation::with(['user', 'book'])->paginate(12);

        return view('admin.reservations.index', compact('reservations'));
    }
}

