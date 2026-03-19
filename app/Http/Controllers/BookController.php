<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\User;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function __construct(protected BookService $service)
    {
        $this->middleware(['auth', 'verified', \App\Http\Middleware\EnsureUserIsActive::class]);
    }

    public function index(Request $request)
    {
        $books = $this->service->list($request->only(['search', 'genre', 'available']));

        return view('books.index', compact('books'));
    }

    public function create()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        return view('books.create');
    }

    public function store(StoreBookRequest $request)
    {
        $book = $this->service->create($request->validated());
        return redirect()->route('books.index')->with('success', 'Book created successfully.');
    }

    public function edit(Book $book)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        return view('books.edit', compact('book'));
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->service->update($book, $request->validated());

        return redirect()->route('books.index')->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        $this->service->delete($book);

        return redirect()->route('books.index')->with('success', 'Book deleted successfully.');
    }
}

