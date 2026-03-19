<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaitingQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'position',
        'notified_at',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    // ── Scopes ────────────────────────────────────────

    public function scopeForBook(Builder $query, int $bookId): Builder
    {
        return $query->where('book_id', $bookId)->orderBy('position');
    }

    public function scopeNotNotified(Builder $query): Builder
    {
        return $query->whereNull('notified_at');
    }

    // ── Domain Methods ────────────────────────────────

    public static function nextPosition(int $bookId): int
    {
        return (int) static::where('book_id', $bookId)->max('position') + 1;
    }

    public static function nextInLine(int $bookId): ?self
    {
        return static::forBook($bookId)->notNotified()->first();
    }
}
