<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'genre' => $this->faker->randomElement(['Fiction', 'Non-Fiction', 'Science', 'Fantasy', 'Mystery']),
            'isbn' => $this->faker->unique()->isbn13(),
            'published_at' => $this->faker->date(),
        ];
    }

    /**
     * Create a book with a specified number of copies.
     */
    public function withCopies(int $count = 3): static
    {
        return $this->afterCreating(function (Book $book) use ($count) {
            CopyFactory::new()
                ->count($count)
                ->create(['book_id' => $book->id]);
        });
    }
}
