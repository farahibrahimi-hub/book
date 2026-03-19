<?php

namespace Database\Factories;

use App\Enums\CopyStatus;
use App\Models\Book;
use App\Models\Copy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Copy>
 */
class CopyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'inventory_code' => strtoupper($this->faker->unique()->bothify('CPY-####-???')),
            'condition' => $this->faker->randomElement(['new', 'good', 'fair', 'worn']),
            'status' => CopyStatus::Available,
        ];
    }

    public function reserved(): static
    {
        return $this->state(fn () => ['status' => CopyStatus::Reserved]);
    }

    public function maintenance(): static
    {
        return $this->state(fn () => ['status' => CopyStatus::Maintenance]);
    }
}
