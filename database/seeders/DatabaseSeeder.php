<?php

namespace Database\Seeders;

use App\Enums\CopyStatus;
use App\Enums\ReservationStatus;
use App\Models\Book;
use App\Models\Copy;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Users ──────────────────────────────────────
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@tt.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@tt.com',
            'password' => Hash::make('user'),
            'role' => 'user',
        ]);

        $extraUsers = User::factory()->count(5)->create(['role' => 'user']);

        // ── Books with Copies ──────────────────────────
        $sampleBooks = [
            ['title' => 'Clean Code', 'author' => 'Robert C. Martin', 'genre' => 'Non-Fiction', 'isbn' => '9780132350884'],
            ['title' => 'Design Patterns', 'author' => 'Gang of Four', 'genre' => 'Non-Fiction', 'isbn' => '9780201633610'],
            ['title' => 'The Pragmatic Programmer', 'author' => 'David Thomas', 'genre' => 'Non-Fiction', 'isbn' => '9780135957059'],
            ['title' => 'Dune', 'author' => 'Frank Herbert', 'genre' => 'Fiction', 'isbn' => '9780441172719'],
            ['title' => 'The Hobbit', 'author' => 'J.R.R. Tolkien', 'genre' => 'Fantasy', 'isbn' => '9780547928227'],
            ['title' => 'A Brief History of Time', 'author' => 'Stephen Hawking', 'genre' => 'Science', 'isbn' => '9780553380163'],
            ['title' => 'Murder on the Orient Express', 'author' => 'Agatha Christie', 'genre' => 'Mystery', 'isbn' => '9780062693662'],
        ];

        foreach ($sampleBooks as $bookData) {
            $book = Book::create(array_merge($bookData, [
                'description' => fake()->paragraph(3),
                'published_at' => fake()->date(),
            ]));

            // Create 2-5 copies per book
            $copyCount = rand(2, 5);
            for ($i = 1; $i <= $copyCount; $i++) {
                Copy::create([
                    'book_id' => $book->id,
                    'inventory_code' => strtoupper(substr($book->title, 0, 3)).'-'.str_pad($book->id, 3, '0', STR_PAD_LEFT).'-'.str_pad($i, 2, '0', STR_PAD_LEFT),
                    'condition' => fake()->randomElement(['new', 'good', 'fair']),
                    'status' => CopyStatus::Available,
                ]);
            }
        }

        // Create some extra random books with copies
        Book::factory()->count(8)->create()->each(function (Book $book) {
            Copy::factory()->count(rand(1, 4))->create(['book_id' => $book->id]);
        });

        // ── Sample Reservations ────────────────────────
        $availableCopy = Copy::available()->first();
        if ($availableCopy) {
            $availableCopy->update(['status' => CopyStatus::Reserved]);
            Reservation::create([
                'user_id' => $user->id,
                'copy_id' => $availableCopy->id,
                'reserved_at' => now(),
                'return_date' => now()->addDays(7),
                'expires_at' => now()->addHours(24),
                'status' => ReservationStatus::Active,
                'notes' => 'First reservation!',
            ]);
        }
    }
}
