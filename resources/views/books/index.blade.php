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
    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
            {{ session('info') }}
        </div>
    @endif

    <form method="GET" action="{{ route('books.index') }}" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or author..." class="px-3 py-2 border rounded"/>
        @if(isset($genres) && count($genres) > 0)
            <select name="genre" class="px-3 py-2 border rounded">
                <option value="">All Genres</option>
                @foreach($genres as $genre)
                    <option value="{{ $genre }}" {{ request('genre') === $genre ? 'selected' : '' }}>{{ $genre }}</option>
                @endforeach
            </select>
        @endif
        <label class="flex items-center gap-1">
            <input type="checkbox" name="available" value="1" {{ request('available') ? 'checked' : '' }}>
            Available only
        </label>
        <button type="submit" class="px-3 py-2 bg-blue-500 text-white rounded">Filter</button>
    </form>



    <div class="mt-4 grid grid-cols-1 gap-4">
        @forelse($books as $book)
            <div class="border rounded-lg p-4 {{ ($book->available_copies_count ?? 0) > 0 ? 'bg-white' : 'bg-red-50' }}">
                <h2 class="text-xl font-semibold">{{ $book->title }}</h2>
                <p class="text-gray-700">Author: {{ $book->author }}</p>
                <p class="text-gray-600">Published: {{ optional($book->published_at)->format('d M Y') ?? 'N/A' }}</p>
                @if($book->genre)
                    <span class="inline-block mt-1 px-2 py-1 text-xs bg-gray-200 rounded">{{ $book->genre }}</span>
                @endif
                <p class="mt-2">{{ Str::limit($book->description, 200) }}</p>
                <p class="mt-2 font-bold">
                    Copies: {{ $book->available_copies_count ?? 0 }} / {{ $book->copies_count ?? 0 }} available
                </p>

                <div class="mt-2 flex flex-wrap gap-2">
                    @auth
                        <form method="POST" action="{{ route('reservations.store') }}">
                            @csrf
                            <input type="hidden" name="book_id" value="{{ $book->id }}" />
                            <button class="px-3 py-1 bg-blue-600 text-white rounded">
                                {{ ($book->available_copies_count ?? 0) > 0 ? 'Reserve' : 'Join Queue' }}
                            </button>
                        </form>
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
