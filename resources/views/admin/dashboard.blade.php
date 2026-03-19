@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-white border rounded shadow">
            <h2 class="text-xl font-semibold">Total Books</h2>
            <p class="text-3xl">{{ $totalBooks }}</p>
            <a href="{{ route('admin.books') }}" class="text-blue-500">Manage books</a>
        </div>

        <div class="p-4 bg-white border rounded shadow">
            <h2 class="text-xl font-semibold">Total Reservations</h2>
            <p class="text-3xl">{{ $totalReservations }}</p>
            <a href="{{ route('admin.reservations') }}" class="text-blue-500">View reservations</a>
        </div>

        <div class="p-4 bg-white border rounded shadow">
            <h2 class="text-xl font-semibold">Late Reservations</h2>
            <p class="text-3xl">{{ $lateReservations }}</p>
            <a href="{{ route('admin.reservations') }}" class="text-blue-500">Check late</a>
        </div>
    </div>

    <div class="p-4 bg-white border rounded shadow">
        <h2 class="text-xl font-semibold mb-2">Quick actions</h2>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('books.create') }}" class="px-4 py-2 bg-green-500 text-white rounded">Add Book</a>
            <a href="{{ route('admin.books') }}" class="px-4 py-2 bg-blue-500 text-white rounded">Books</a>
            <a href="{{ route('admin.reservations') }}" class="px-4 py-2 bg-indigo-500 text-white rounded">Reservations</a>
        </div>
    </div>
</div>
@endsection