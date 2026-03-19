<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penalty extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reservation_id',
        'reason',
        'penalty_until',
        'is_active',
    ];

    protected $casts = [
        'penalty_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    // ── Scopes ────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('penalty_until', '>', now());
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // ── Domain Methods ────────────────────────────────

    public function isExpired(): bool
    {
        return now()->greaterThan($this->penalty_until);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
