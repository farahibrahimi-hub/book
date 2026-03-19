<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        if ($request->boolean('available')) {
            $query->where('available_copies', '>', 0);
        }

        $books = $query->paginate(10);

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

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'available_copies' => 'required|integer|min:0',
            'published_at' => 'nullable|date',
        ]);

        Book::create($data);

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

    public function update(Request $request, Book $book)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'available_copies' => 'required|integer|min:0',
            'published_at' => 'nullable|date',
        ]);

        $book->update($data);

        return redirect()->route('books.index')->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Book deleted successfully.');
    }
}

