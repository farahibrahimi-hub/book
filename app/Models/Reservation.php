<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'reserved_at',
        'return_date',
        'returned_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'return_date' => 'date',
        'returned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function isLate(): bool
    {
        return $this->status === 'active' && $this->return_date && now()->greaterThan($this->return_date);
    }
}
