<?php

namespace App\Actions;

use App\Models\Penalty;
use App\Models\Reservation;
use App\Models\User;

class ApplyPenaltyAction
{
    /**
     * Apply a penalty to a user for failing to complete a reservation.
     *
     * @param  int  $penaltyDays  Number of days the user cannot reserve
     */
    public function execute(User $user, Reservation $reservation, int $penaltyDays = 3): Penalty
    {
        return Penalty::create([
            'user_id' => $user->id,
            'reservation_id' => $reservation->id,
            'reason' => 'Reservation expired without completion.',
            'penalty_until' => now()->addDays($penaltyDays),
            'is_active' => true,
        ]);
    }
}
