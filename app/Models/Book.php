<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
        'available_copies',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
