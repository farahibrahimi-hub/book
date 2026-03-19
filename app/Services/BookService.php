<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Contracts\Pagination\Paginator;

class BookService
{
    public function list(array $filters = []): Paginator
    {
        $query = Book::withCount([
            'copies',
            'copies as available_copies_count' => fn ($q) => $q->available(),
        ]);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('author', 'like', '%'.$search.'%');
            });
        }

        if (! empty($filters['genre'])) {
            $query->where('genre', $filters['genre']);
        }

        if (! empty($filters['available'])) {
            $query->having('available_copies_count', '>', 0);
        }

        return $query->orderBy('title')->paginate(12);
    }

    public function create(array $data): Book
    {
        return Book::create($data);
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

    /**
     * Get list of unique genres for filter dropdowns.
     */
    public function getGenres(): array
    {
        return Book::query()
            ->distinct()
            ->whereNotNull('genre')
            ->pluck('genre')
            ->sort()
            ->values()
            ->toArray();
    }
}
