<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'description',
        'cover_image',
        'genre',
        'isbn',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];

    // ── Relationships ─────────────────────────────────

    public function copies(): HasMany
    {
        return $this->hasMany(Copy::class);
    }

    public function reservations(): HasManyThrough
    {
        return $this->hasManyThrough(Reservation::class, Copy::class);
    }

    public function waitingQueues(): HasMany
    {
        return $this->hasMany(WaitingQueue::class);
    }

    // ── Computed Attributes ───────────────────────────

    public function getTotalCopiesCountAttribute(): int
    {
        return $this->copies()->count();
    }

    public function getAvailableCopiesCountAttribute(): int
    {
        return $this->copies()->available()->count();
    }

    public function getAvailabilityAttribute(): string
    {
        $total = $this->total_copies_count;

        return $total > 0
            ? sprintf('%d/%d', $this->available_copies_count, $total)
            : '0/0';
    }

    public function getAvailabilityPercentAttribute(): float
    {
        $total = $this->total_copies_count;

        if ($total <= 0) {
            return 0;
        }

        return round(($this->available_copies_count / $total) * 100, 2);
    }

    public function getQueueLengthAttribute(): int
    {
        return $this->waitingQueues()->count();
    }

    // ── Domain Methods ────────────────────────────────

    public function hasAvailableCopies(): bool
    {
        return $this->copies()->available()->exists();
    }

    public function findAvailableCopy(): ?Copy
    {
        return $this->copies()->available()->first();
    }
}
