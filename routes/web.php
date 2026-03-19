<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CopyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('books.index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // ── Public (authenticated) routes ─────────────────
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations/{reservation}/return', [ReservationController::class, 'returnBook'])->name('reservations.return');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // ── Profile ───────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Admin routes ──────────────────────────────────
    Route::middleware(AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Book management
        Route::get('/books', [AdminController::class, 'books'])->name('books');
        Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
        Route::post('/books', [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
        Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

        // Copy management
        Route::get('/books/{book}/copies', [CopyController::class, 'index'])->name('books.copies.index');
        Route::post('/books/{book}/copies', [CopyController::class, 'store'])->name('books.copies.store');
        Route::delete('/books/{book}/copies/{copy}', [CopyController::class, 'destroy'])->name('books.copies.destroy');
        Route::patch('/books/{book}/copies/{copy}/maintenance', [CopyController::class, 'toggleMaintenance'])->name('books.copies.maintenance');

        // Reservations management
        Route::get('/reservations', [AdminController::class, 'reservations'])->name('reservations');
    });
});

require __DIR__.'/auth.php';
