<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

class ReservationService
{
    /**
     * List reservations for a user (or all if admin).
     */
    public function listForUser(User $user, array $filters = []): Paginator
    {
        $query = Reservation::with(['copy.book', 'user']);

        if (! $user->isAdmin()) {
            $query->forUser($user->id);
        }

        if (! empty($filters['status'])) {
            $status = ReservationStatus::tryFrom($filters['status']);
            if ($status) {
                $query->where('status', $status);
            }
        }

        return $query->latest('reserved_at')->paginate(12);
    }

    /**
     * Mark overdue reservations as late.
     */
    public function markLate(): int
    {
        $overdue = Reservation::overdue()->get();

        foreach ($overdue as $reservation) {
            $reservation->markLate();
        }

        return $overdue->count();
    }

    /**
     * Get dashboard statistics.
     */
    public function getStats(): array
    {
        return [
            'total' => Reservation::count(),
            'active' => Reservation::active()->count(),
            'late' => Reservation::late()->count(),
            'returned' => Reservation::where('status', ReservationStatus::Returned)->count(),
            'expired' => Reservation::expired()->count(),
        ];
    }
}
