<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'available_copies',
        'total_copies',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function getAvailabilityAttribute(): string
    {
        return $this->total_copies > 0
            ? sprintf('%d/%d', $this->available_copies, $this->total_copies)
            : '0/0';
    }

    public function getAvailabilityPercentAttribute(): float
    {
        if ($this->total_copies <= 0) {
            return 0;
        }

        return round(($this->available_copies / $this->total_copies) * 100, 2);
    }
}
