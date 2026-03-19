<?php

namespace App\Models;

use App\Enums\CopyStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Copy extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'inventory_code',
        'condition',
        'status',
    ];

    protected $casts = [
        'status' => CopyStatus::class,
    ];

    // ── Relationships ─────────────────────────────────

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // ── Scopes ────────────────────────────────────────

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', CopyStatus::Available);
    }

    public function scopeForBook(Builder $query, int $bookId): Builder
    {
        return $query->where('book_id', $bookId);
    }

    // ── Domain Methods ────────────────────────────────

    public function markReserved(): void
    {
        $this->update(['status' => CopyStatus::Reserved]);
    }

    public function markAvailable(): void
    {
        $this->update(['status' => CopyStatus::Available]);
    }

    public function isAvailable(): bool
    {
        return $this->status === CopyStatus::Available;
    }
}
