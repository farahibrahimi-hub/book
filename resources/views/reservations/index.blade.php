@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">My Reservations</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th class="px-4 py-2 border">Book</th>
                <th class="px-4 py-2 border">Reserved At</th>
                <th class="px-4 py-2 border">Return Date</th>
                <th class="px-4 py-2 border">Status</th>
                <th class="px-4 py-2 border">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr class="{{ $reservation->isLate() ? 'bg-red-100' : '' }}">
                    <td class="px-4 py-2 border">{{ $reservation->book->title }}</td>
                    <td class="px-4 py-2 border">{{ $reservation->reserved_at->format('d M Y') }}</td>
                    <td class="px-4 py-2 border">{{ $reservation->return_date->format('d M Y') }}</td>
                    <td class="px-4 py-2 border">{{ ucfirst($reservation->status) }}</td>
                    <td class="px-4 py-2 border">
                        @if($reservation->status !== 'returned')
                            <form action="{{ route('reservations.return', $reservation) }}" method="POST">
                                @csrf
                                <button class="px-3 py-1 bg-indigo-600 text-white rounded">Return</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-2 border">No reservations yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection