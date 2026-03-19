@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Manage Copies — {{ $book->title }}</h1>
        <a href="{{ route('admin.books') }}" class="px-3 py-1 bg-gray-500 text-white rounded">← Back to Books</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    {{-- Add Copy Form --}}
    <div class="bg-white border rounded-lg p-4 mb-6">
        <h3 class="text-lg font-semibold mb-3">Add New Copy</h3>
        <form method="POST" action="{{ route('admin.books.copies.store', $book) }}" class="flex gap-3 items-end">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Inventory Code</label>
                <input type="text" name="inventory_code" required placeholder="e.g. CPY-001-01" class="mt-1 px-3 py-2 border rounded" />
                @error('inventory_code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Condition</label>
                <select name="condition" class="mt-1 px-3 py-2 border rounded">
                    <option value="new">New</option>
                    <option value="good" selected>Good</option>
                    <option value="fair">Fair</option>
                    <option value="worn">Worn</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Add Copy</button>
        </form>
    </div>

    {{-- Copies Table --}}
    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th class="px-4 py-2 border">Inventory Code</th>
                <th class="px-4 py-2 border">Condition</th>
                <th class="px-4 py-2 border">Status</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($copies as $copy)
                <tr>
                    <td class="px-4 py-2 border font-mono">{{ $copy->inventory_code }}</td>
                    <td class="px-4 py-2 border capitalize">{{ $copy->condition }}</td>
                    <td class="px-4 py-2 border">
                        <span class="px-2 py-1 text-xs rounded bg-{{ $copy->status->color() }}-100 text-{{ $copy->status->color() }}-800">
                            {{ $copy->status->label() }}
                        </span>
                    </td>
                    <td class="px-4 py-2 border">
                        <div class="flex gap-2">
                            <form action="{{ route('admin.books.copies.maintenance', [$book, $copy]) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button class="px-2 py-1 text-xs bg-yellow-500 text-white rounded">
                                    {{ $copy->status === \App\Enums\CopyStatus::Maintenance ? 'Mark Available' : 'Maintenance' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.books.copies.destroy', [$book, $copy]) }}" method="POST" onsubmit="return confirm('Delete this copy?')">
                                @csrf
                                @method('DELETE')
                                <button class="px-2 py-1 text-xs bg-red-600 text-white rounded">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-2 border text-center">No copies yet. Add one above.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $copies->links() }}</div>
</div>
@endsection
