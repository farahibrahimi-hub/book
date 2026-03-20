<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin</title>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-white dark:bg-gray-900 border-r dark:border-gray-800">
            <div class="p-4 text-xl font-bold">Admin Panel</div>
            <nav class="space-y-1 p-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-indigo-50 dark:hover:bg-gray-800">Dashboard</a>
                <a href="{{ route('admin.books') }}" class="block px-3 py-2 rounded hover:bg-indigo-50 dark:hover:bg-gray-800">Books</a>
                <a href="{{ route('admin.reservations') }}" class="block px-3 py-2 rounded hover:bg-indigo-50 dark:hover:bg-gray-800">Reservations</a>
                <hr class="my-4 border-gray-200 dark:border-gray-700">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded hover:bg-indigo-50 dark:hover:bg-gray-800">My Profile</a>
                <a href="{{ route('books.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-50 dark:hover:bg-gray-800">Back to Public Site</a>
            </nav>
        </aside>
        <div class="flex-1">
            <header class="flex justify-between items-center p-4 border-b bg-white dark:bg-gray-800 dark:border-gray-700">
                <div class="p-2 font-semibold">{{ $title ?? 'Dashboard' }}</div>
                <div class="flex items-center gap-4">
                    <button id="dark-toggle" class="px-2 py-1 bg-gray-200 rounded dark:bg-gray-700 dark:text-white text-sm">Theme</button>
                    <div class="relative">
                        <img src="{{ auth()->user()->avatar ?? 'https://i.pravatar.cc/40'}}" class="w-8 h-8 rounded-full" alt="avatar" />
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">Log Out</button>
                    </form>
                </div>
            </header>
            <main class="p-6">
                @include('components.alert')
                @yield('content')
            </main>
        </div>
    </div>
    <script>
        document.getElementById('dark-toggle').addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
        });
    </script>
</body>
</html>