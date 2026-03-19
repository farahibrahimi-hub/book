<?php

namespace Database\Factories;

use App\Enums\ReservationStatus;
use App\Models\Copy;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'copy_id' => Copy::factory(),
            'reserved_at' => now(),
            'return_date' => now()->addDays(7),
            'expires_at' => now()->addHours(24),
            'status' => ReservationStatus::Active,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function returned(): static
    {
        return $this->state(fn () => [
            'status' => ReservationStatus::Returned,
            'returned_at' => now(),
        ]);
    }

    public function late(): static
    {
        return $this->state(fn () => [
            'status' => ReservationStatus::Late,
            'return_date' => now()->subDays(2),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => ReservationStatus::Expired,
            'expires_at' => now()->subHours(1),
        ]);
    }
}
