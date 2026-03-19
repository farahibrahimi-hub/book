@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Add New Book</h1>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 p-4 mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.books.store') }}" method="POST" class="space-y-4 max-w-xl">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Author</label>
            <input type="text" name="author" value="{{ old('author') }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Genre</label>
                <input type="text" name="genre" value="{{ old('genre') }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">ISBN</label>
                <input type="text" name="isbn" value="{{ old('isbn') }}" class="w-full border rounded px-3 py-2">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Published At</label>
            <input type="date" name="published_at" value="{{ old('published_at') }}" class="w-full border rounded px-3 py-2">
        </div>
        <p class="text-sm text-gray-500">After creating the book, you can add physical copies from the Copies management page.</p>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Create Book</button>
    </form>
</div>
@endsection