@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-stat-card title="Total Books" :value="$totalBooks" />
        <x-stat-card title="Available Books" :value="\App\Models\Book::where('available_copies', '>', 0)->count()" />
        <x-stat-card title="Total Users" :value="\App\Models\User::count()" />
        <x-stat-card title="Active Users" :value="\App\Models\User::where('is_active', true)->count()" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-card>
            <h3 class="text-lg font-semibold mb-2">Reservations Over 30 Days</h3>
            <canvas id="reservationsLineChart"></canvas>
        </x-card>

        <x-card>
            <h3 class="text-lg font-semibold mb-2">Reservation Status Breakdown</h3>
            <canvas id="statusChart"></canvas>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <x-card>
            <h3 class="text-lg font-semibold mb-2">Most Active Users</h3>
            <ul class="space-y-2">
                @foreach(\App\Models\User::withCount(['reservations'])->orderBy('reservations_count', 'desc')->limit(5)->get() as $user)
                    <li class="flex justify-between">{{ $user->name }} <span class="font-bold">{{ $user->reservations_count }}</span></li>
                @endforeach
            </ul>
        </x-card>

        <x-card>
            <h3 class="text-lg font-semibold mb-2">Late Reservations</h3>
            <ul class="space-y-2">
                @foreach(\App\Models\Reservation::where('status', 'late')->with('user', 'book')->latest()->limit(5)->get() as $res)
                    <li class="p-2 bg-red-50 dark:bg-red-900 rounded">
                        {{ $res->book->title }} by {{ $res->user->name }} ({{ $res->return_date->diffInDays(now()) }} days overdue)
                    </li>
                @endforeach
            </ul>
        </x-card>

        <x-card>
            <h3 class="text-lg font-semibold mb-2">Top Books</h3>
            <ul class="space-y-2">
                @foreach(\App\Models\Book::withCount('reservations')->orderBy('reservations_count','desc')->limit(5)->get() as $book)
                    <li class="flex justify-between">{{ $book->title }} <span class="font-bold">{{ $book->reservations_count }}</span></li>
                @endforeach
            </ul>
        </x-card>
    </div>
</div>

<script>
let ctx = document.getElementById('reservationsLineChart').getContext('2d');
let dates = [];
let counts = [];
for (let i = 29; i >= 0; i--) {
    let date = new Date();
    date.setDate(date.getDate() - i);
    dates.push(date.toISOString().slice(0, 10));
    counts.push(0);
}
@php
$resData = \App\Models\Reservation::where('created_at','>=', now()->subDays(30))->get()->groupBy(function($r){ return $r->created_at->format('Y-m-d'); });
@endphp
@foreach($resData as $day => $items)
    counts[dates.indexOf('{{ $day }}')] = {{ $items->count() }};
@endforeach

new Chart(ctx, {
    type: 'line',
    data: {
        labels: dates,
        datasets: [{
            label: 'Reservations',
            data: counts,
            fill: false,
            borderColor: 'rgb(99 102 241)',
            tension: 0.3
        }]
    }
});

let statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Active', 'Returned', 'Late'],
        datasets: [{
            data: [
                {{ \App\Models\Reservation::where('status', 'active')->count() }},
                {{ \App\Models\Reservation::where('status', 'returned')->count() }},
                {{ \App\Models\Reservation::where('status', 'late')->count() }}
            ],
            backgroundColor: ['#4f46e5', '#10b981', '#f59e0b']
        }]
    }
});
</script>
@endsection