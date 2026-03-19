<?php

use App\Enums\UserRole;
use App\Models\Book;
use App\Models\User;

test('guest cannot view books', function () {
    $response = $this->get(route('books.index'));

    $response->assertRedirect(route('login'));
});

test('user can view books list', function () {
    $user = User::factory()->create(['role' => UserRole::User]);

    $response = $this->actingAs($user)->get(route('books.index'));

    $response->assertStatus(200);
});

test('admin can create a book', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $bookData = [
        'title' => 'New Book',
        'author' => 'Author Name',
        'isbn' => '978-3-16-148410-0',
        'published_year' => 2024,
        'genre' => 'Science Fiction',
        'description' => 'A great sci-fi book',
    ];

    $response = $this->actingAs($admin)->post(route('admin.books.store'), $bookData);

    $response->assertRedirect(route('admin.books'));
    $this->assertDatabaseHas('books', ['title' => 'New Book']);
});

test('user cannot create a book', function () {
    $user = User::factory()->create(['role' => UserRole::User]);

    $bookData = [
        'title' => 'New Book',
        'author' => 'Author Name',
        'isbn' => '978-3-16-148410-1',
        'published_year' => 2024,
        'genre' => 'Science Fiction',
    ];

    $response = $this->actingAs($user)->post(route('admin.books.store'), $bookData);

    $response->assertForbidden();
    $this->assertDatabaseMissing('books', ['title' => 'New Book']);
});

test('admin can add copies to a book', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $book = Book::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.books.copies.store', $book), [
        'inventory_code' => 'CPY-001',
        'condition' => 'new',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('copies', [
        'book_id' => $book->id,
        'inventory_code' => 'CPY-001',
    ]);
});
