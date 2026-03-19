@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Books</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="{{ route('books.index') }}" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title..." class="px-3 py-2 border rounded"/>
        <label class="flex items-center gap-1">
            <input type="checkbox" name="available" value="1" {{ request('available') ? 'checked' : '' }}>
            available only
        </label>
        <button type="submit" class="px-3 py-2 bg-blue-500 text-white rounded">Filter</button>
    </form>

    @auth
    @if(auth()->user()->isAdmin())
        <a href="{{ route('books.create') }}" class="px-4 py-2 bg-green-500 text-white rounded">Add Book</a>
    @endif
    @endauth

    <div class="mt-4 grid grid-cols-1 gap-4">
        @forelse($books as $book)
            <div class="border rounded-lg p-4 {{ $book->available_copies > 0 ? 'bg-white' : 'bg-red-50' }}">
                <h2 class="text-xl font-semibold">{{ $book->title }}</h2>
                <p class="text-gray-700">Author: {{ $book->author }}</p>
                <p class="text-gray-600">Published: {{ optional($book->published_at)->format('d M Y') ?? 'N/A' }}</p>
                <p class="mt-2">{{ $book->description }}</p>
                <p class="mt-2 font-bold">Available: {{ $book->available_copies }}</p>

                <div class="mt-2 flex flex-wrap gap-2">
                    @auth
                        @if($book->available_copies > 0)
                            <form method="POST" action="{{ route('reservations.store') }}">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}" />
                                <button class="px-3 py-1 bg-blue-600 text-white rounded">Reserve</button>
                            </form>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('books.edit', $book) }}" class="px-3 py-1 bg-yellow-500 text-white rounded">Edit</a>
                            <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Delete?')">
                                @csrf
                                @method('DELETE')
                                <button class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        @empty
            <p>No books found.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $books->withQueryString()->links() }}</div>
</div>
@endsection
