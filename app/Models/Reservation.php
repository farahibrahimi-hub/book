<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'copy_id',
        'reserved_at',
        'return_date',
        'returned_at',
        'expires_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'return_date' => 'date',
        'returned_at' => 'datetime',
        'expires_at' => 'datetime',
        'status' => ReservationStatus::class,
    ];

    // ── Relationships ─────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }

    /**
     * Access the book through the copy relationship.
     */
    public function book(): BelongsTo
    {
        return $this->copy->book();
    }

    public function penalty(): HasOne
    {
        return $this->hasOne(Penalty::class);
    }

    // ── Scopes ────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ReservationStatus::Active);
    }

    public function scopeLate(Builder $query): Builder
    {
        return $query->where('status', ReservationStatus::Late);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', ReservationStatus::Expired);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', ReservationStatus::Active)
            ->whereNotNull('return_date')
            ->whereDate('return_date', '<', now()->toDateString());
    }

    public function scopePastExpiration(Builder $query): Builder
    {
        return $query->where('status', ReservationStatus::Active)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    // ── Domain Methods ────────────────────────────────

    public function isLate(): bool
    {
        return $this->status === ReservationStatus::Active
            && $this->return_date
            && now()->greaterThan($this->return_date);
    }

    public function isExpired(): bool
    {
        return $this->status === ReservationStatus::Active
            && $this->expires_at
            && now()->greaterThan($this->expires_at);
    }

    public function canBeReturned(): bool
    {
        return in_array($this->status, [
            ReservationStatus::Active,
            ReservationStatus::Late,
        ]);
    }

    public function markReturned(): void
    {
        $this->update([
            'status' => ReservationStatus::Returned,
            'returned_at' => now(),
        ]);
    }

    public function markLate(): void
    {
        $this->update(['status' => ReservationStatus::Late]);
    }

    public function markExpired(): void
    {
        $this->update(['status' => ReservationStatus::Expired]);
    }
}
