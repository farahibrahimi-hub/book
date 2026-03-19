@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Book</h1>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 p-4 mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('books.update', $book) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title', $book->title) }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label>Author</label>
            <input type="text" name="author" value="{{ old('author', $book->author) }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label>Description</label>
            <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description', $book->description) }}</textarea>
        </div>
        <div>
            <label>Available Copies</label>
            <input type="number" name="available_copies" value="{{ old('available_copies', $book->available_copies) }}" min="0" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label>Published At</label>
            <input type="date" name="published_at" value="{{ old('published_at', optional($book->published_at)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Update</button>
    </form>
</div>
@endsection