<?php

use App\Actions\ReserveBookAction;
use App\Actions\ReturnBookAction;
use App\Enums\CopyStatus;
use App\Enums\ReservationStatus;
use App\Models\Book;
use App\Models\Copy;
use App\Models\Penalty;
use App\Models\Reservation;
use App\Models\User;
use App\Models\WaitingQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

// ── ReserveBookAction ─────────────────────────────────

test('user can reserve a book with available copy', function () {
    $action = app(ReserveBookAction::class);
    $user = User::factory()->create();
    $book = Book::factory()->create();
    $copy = Copy::factory()->create(['book_id' => $book->id, 'status' => CopyStatus::Available]);

    $result = $action->execute($user, $book, 'Handle with care');

    expect($result['reservation'])->toBeInstanceOf(Reservation::class);
    expect($result['queued'])->toBeFalse();
    expect($result['reservation']->notes)->toBe('Handle with care');
    expect($result['reservation']->status)->toBe(ReservationStatus::Active);
    expect($copy->fresh()->status)->toBe(CopyStatus::Reserved);
});

test('user is enqueued when no copies available', function () {
    $action = app(ReserveBookAction::class);
    $user = User::factory()->create();
    $book = Book::factory()->create();
    Copy::factory()->create(['book_id' => $book->id, 'status' => CopyStatus::Reserved]);

    $result = $action->execute($user, $book);

    expect($result['reservation'])->toBeNull();
    expect($result['queued'])->toBeTrue();
    expect($result['position'])->toBe(1);
    expect(WaitingQueue::where('user_id', $user->id)->where('book_id', $book->id)->exists())->toBeTrue();
});

test('user cannot reserve same book twice', function () {
    $action = app(ReserveBookAction::class);
    $user = User::factory()->create();
    $book = Book::factory()->create();
    Copy::factory()->count(2)->create(['book_id' => $book->id, 'status' => CopyStatus::Available]);

    $action->execute($user, $book);

    expect(fn () => $action->execute($user, $book))
        ->toThrow(InvalidArgumentException::class, 'You already have an active reservation for this book.');
});

test('user with penalty cannot reserve', function () {
    $action = app(ReserveBookAction::class);
    $user = User::factory()->create();
    $book = Book::factory()->create();
    Copy::factory()->create(['book_id' => $book->id, 'status' => CopyStatus::Available]);

    Penalty::create([
        'user_id' => $user->id,
        'reason' => 'Test penalty',
        'penalty_until' => now()->addDays(3),
        'is_active' => true,
    ]);

    expect(fn () => $action->execute($user, $book))
        ->toThrow(InvalidArgumentException::class, 'You have an active penalty and cannot reserve books.');
});

test('user cannot exceed max 3 active reservations', function () {
    $action = app(ReserveBookAction::class);
    $user = User::factory()->create();

    // Create 3 active reservations
    for ($i = 0; $i < 3; $i++) {
        $book = Book::factory()->create();
        $copy = Copy::factory()->create(['book_id' => $book->id]);
        $action->execute($user, $book);
    }

    $newBook = Book::factory()->create();
    Copy::factory()->create(['book_id' => $newBook->id]);

    expect(fn () => $action->execute($user, $newBook))
        ->toThrow(InvalidArgumentException::class, 'Max 3 active reservations allowed.');
});

// ── ReturnBookAction ──────────────────────────────────

test('user can return a reservation', function () {
    $action = app(ReturnBookAction::class);
    $user = User::factory()->create();
    $copy = Copy::factory()->reserved()->create();
    $reservation = Reservation::factory()->create([
        'user_id' => $user->id,
        'copy_id' => $copy->id,
        'status' => ReservationStatus::Active,
    ]);

    $returned = $action->execute($reservation);

    expect($returned->status)->toBe(ReservationStatus::Returned);
    expect($returned->returned_at)->not->toBeNull();
    expect($copy->fresh()->status)->toBe(CopyStatus::Available);
});

test('cannot return already returned reservation', function () {
    $action = app(ReturnBookAction::class);
    $reservation = Reservation::factory()->returned()->create();

    expect(fn () => $action->execute($reservation))
        ->toThrow(InvalidArgumentException::class, 'This reservation cannot be returned.');
});
