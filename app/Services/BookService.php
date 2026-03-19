<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Collection;

class BookService
{
    public function list(array $filters = []): \Illuminate\Contracts\Pagination\Paginator
    {
        $query = Book::query();

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%'.$filters['search'].'%')
                ->orWhere('author', 'like', '%'.$filters['search'].'%');
        }

        if (!empty($filters['genre'])) {
            $query->where('genre', $filters['genre']);
        }

        if (!empty($filters['available'])) {
            $query->where('available_copies', '>', 0);
        }

        return $query->orderBy('title')->paginate(12);
    }

    public function create(array $data): Book
    {
        $book = Book::create($data);
        return $book;
    }

    public function update(Book $book, array $data): Book
    {
        $book->update($data);
        return $book;
    }

    public function delete(Book $book): bool
    {
        return $book->delete();
    }

    public function calculateAvailabilityPercent(Book $book): float
    {
        if ($book->total_copies <= 0) {
            return 0;
        }

        return round(($book->available_copies / $book->total_copies) * 100, 2);
    }
}