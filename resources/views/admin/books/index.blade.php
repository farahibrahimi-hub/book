@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Admin Books Management</h1>

    <a href="{{ route('admin.books.create') }}" class="px-4 py-2 bg-green-500 text-white rounded">+ Add Book</a>

    <table class="w-full mt-4 border">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">Title</th>
                <th class="px-4 py-2 border">Author</th>
                <th class="px-4 py-2 border">Copies</th>
                <th class="px-4 py-2 border">Published</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $book->title }}</td>
                    <td class="px-4 py-2">{{ $book->author }}</td>
                    <td class="px-4 py-2">
                        <span class="{{ ($book->available_copies_count ?? 0) > 0 ? 'text-green-600' : 'text-red-600' }} font-bold">
                            {{ $book->available_copies_count ?? 0 }} / {{ $book->copies_count ?? 0 }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ optional($book->published_at)->format('d M Y') ?? 'N/A' }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ route('admin.books.copies.index', $book) }}" class="px-2 py-1 bg-purple-500 text-white rounded">Copies</a>
                        <a href="{{ route('admin.books.edit', $book) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Edit</a>
                        <form action="{{ route('admin.books.destroy', $book) }}" method="POST" onsubmit="return confirm('Delete this book?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $books->links() }}</div>
</div>
@endsection