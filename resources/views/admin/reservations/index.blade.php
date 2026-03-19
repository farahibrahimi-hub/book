@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Admin Reservations</h1>

    <table class="w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">User</th>
                <th class="px-4 py-2 border">Book</th>
                <th class="px-4 py-2 border">Reserved At</th>
                <th class="px-4 py-2 border">Return Date</th>
                <th class="px-4 py-2 border">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $reservation)
                <tr class="border-b {{ $reservation->isLate() ? 'bg-red-100' : '' }}">
                    <td class="px-4 py-2">{{ $reservation->user->name }} ({{ $reservation->user->email }})</td>
                    <td class="px-4 py-2">{{ $reservation->book->title }}</td>
                    <td class="px-4 py-2">{{ $reservation->reserved_at->format('d M Y') }}</td>
                    <td class="px-4 py-2">{{ $reservation->return_date->format('d M Y') }}</td>
                    <td class="px-4 py-2">{{ ucfirst($reservation->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $reservations->links() }}</div>
</div>
@endsection