<?php

use App\Models\Book;
use App\Models\Copy;
use App\Services\BookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('list returns paginated books with copy counts', function () {
    Book::factory()->count(15)->create()->each(function (Book $book) {
        Copy::factory()->count(3)->create(['book_id' => $book->id]);
    });

    $service = new BookService;
    $result = $service->list([]);

    expect($result->count())->toBe(12);
    expect($result->total())->toBe(15);
});

test('list filters by search term', function () {
    Book::factory()->create(['title' => 'Clean Code']);
    Book::factory()->create(['title' => 'Design Patterns']);

    $service = new BookService;
    $result = $service->list(['search' => 'Clean']);

    expect($result->total())->toBe(1);
});

test('list filters available books only', function () {
    $bookWithCopies = Book::factory()->create();
    Copy::factory()->create(['book_id' => $bookWithCopies->id]);

    $bookWithoutCopies = Book::factory()->create();

    $service = new BookService;
    $result = $service->list(['available' => true]);

    expect($result->total())->toBe(1);
});

test('get unique genres', function () {
    Book::factory()->create(['genre' => 'Fiction']);
    Book::factory()->create(['genre' => 'Science']);
    Book::factory()->create(['genre' => 'Fiction']); // Duplicate

    $service = new BookService;
    $genres = $service->getGenres();

    expect($genres)->toHaveCount(2);
    expect($genres)->toContain('Fiction', 'Science');
});
