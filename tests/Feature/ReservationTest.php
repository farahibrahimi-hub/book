<?php

use App\Enums\CopyStatus;
use App\Enums\ReservationStatus;
use App\Models\Book;
use App\Models\Copy;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can view their reservations', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create(['title' => 'Reserved Book']);
    $copy = Copy::factory()->create(['book_id' => $book->id]);
    Reservation::factory()->create([
        'user_id' => $user->id,
        'copy_id' => $copy->id,
    ]);

    $response = $this->actingAs($user)->get(route('reservations.index'));

    $response->assertStatus(200);
    $response->assertSee('Reserved Book');
});

test('user can reserve an available book', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();
    $copy = Copy::factory()->create(['book_id' => $book->id, 'status' => CopyStatus::Available]);

    $response = $this->actingAs($user)->post(route('reservations.store'), [
        'book_id' => $book->id,
    ]);

    $response->assertRedirect(route('reservations.index'));
    $this->assertDatabaseHas('reservations', [
        'user_id' => $user->id,
        'copy_id' => $copy->id,
        'status' => ReservationStatus::Active->value,
    ]);

    expect($copy->fresh()->status)->toBe(CopyStatus::Reserved);
});

test('user gets queued when no copies available', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();
    Copy::factory()->create(['book_id' => $book->id, 'status' => CopyStatus::Reserved]);

    $response = $this->actingAs($user)->post(route('reservations.store'), [
        'book_id' => $book->id,
    ]);

    $response->assertRedirect(route('reservations.index'));
    $response->assertSessionHas('info');
    $this->assertDatabaseHas('waiting_queues', [
        'user_id' => $user->id,
        'book_id' => $book->id,
    ]);
});

test('user can return a book', function () {
    $user = User::factory()->create();
    $copy = Copy::factory()->reserved()->create();
    $reservation = Reservation::factory()->create([
        'user_id' => $user->id,
        'copy_id' => $copy->id,
        'status' => ReservationStatus::Active,
    ]);

    $response = $this->actingAs($user)->post(route('reservations.return', $reservation));

    $response->assertRedirect();
    $this->assertDatabaseHas('reservations', [
        'id' => $reservation->id,
        'status' => ReservationStatus::Returned->value,
    ]);
    expect($copy->fresh()->status)->toBe(CopyStatus::Available);
});
