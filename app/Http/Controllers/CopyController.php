<?php

namespace App\Http\Controllers;

use App\Enums\CopyStatus;
use App\Models\Book;
use App\Models\Copy;
use Illuminate\Http\Request;

class CopyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Book $book)
    {
        $this->authorize('update', $book);

        $copies = $book->copies()->paginate(20);

        return view('admin.copies.index', compact('book', 'copies'));
    }

    public function store(Request $request, Book $book)
    {
        $this->authorize('update', $book);

        $validated = $request->validate([
            'inventory_code' => ['required', 'string', 'max:50', 'unique:copies,inventory_code'],
            'condition' => ['required', 'string', 'max:255'],
        ]);

        $book->copies()->create([
            'inventory_code' => $validated['inventory_code'],
            'condition' => $validated['condition'],
            'status' => CopyStatus::Available,
        ]);

        return redirect()->route('admin.books.copies.index', $book)
            ->with('success', 'Copy added successfully.');
    }

    public function destroy(Book $book, Copy $copy)
    {
        $this->authorize('delete', $book);

        if ($copy->status === CopyStatus::Reserved) {
            return redirect()->back()->with('error', 'Cannot delete a copy that is currently reserved.');
        }

        $copy->delete();

        return redirect()->route('admin.books.copies.index', $book)
            ->with('success', 'Copy removed successfully.');
    }

    public function toggleMaintenance(Book $book, Copy $copy)
    {
        $this->authorize('update', $book);

        if ($copy->status === CopyStatus::Reserved) {
            return redirect()->back()->with('error', 'Cannot change status of a currently reserved copy.');
        }

        $newStatus = $copy->status === CopyStatus::Maintenance
            ? CopyStatus::Available
            : CopyStatus::Maintenance;

        $copy->update(['status' => $newStatus]);

        return redirect()->route('admin.books.copies.index', $book)
            ->with('success', "Copy marked as {$newStatus->label()}.");
    }
}
