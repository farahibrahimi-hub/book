<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(protected BookService $service)
    {
        $this->middleware(['auth', 'verified', EnsureUserIsActive::class]);
    }

    public function index(Request $request)
    {
        $books = $this->service->list($request->only(['search', 'genre', 'available']));
        $genres = $this->service->getGenres();

        return view('books.index', compact('books', 'genres'));
    }

    public function create()
    {
        $this->authorize('create', Book::class);

        return view('books.create');
    }

    public function store(StoreBookRequest $request)
    {
        $this->authorize('create', Book::class);

        $this->service->create($request->validated());

        return redirect()->route('admin.books')->with('success', 'Book created successfully.');
    }

    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        $book->load('copies');

        return view('books.edit', compact('book'));
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->authorize('update', $book);

        $this->service->update($book, $request->validated());

        return redirect()->route('books.index')->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        $this->service->delete($book);

        return redirect()->route('books.index')->with('success', 'Book deleted successfully.');
    }
}
