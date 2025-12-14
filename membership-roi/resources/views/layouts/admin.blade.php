<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'QuantumBit') }} - Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-100">
<div class="min-h-screen bg-gray-950">
    <nav class="bg-gray-950 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                    <x-application-logo class="h-8 w-8 text-slate-100" />
                    <span class="font-semibold text-slate-100">QuantumBit Admin</span>
                </a>
                <div class="hidden md:flex items-center gap-4 text-sm text-slate-200">
                    <a class="hover:underline hover:text-white" href="{{ route('admin.users.index') }}">Users</a>
                    <a class="hover:underline hover:text-white" href="{{ route('admin.deposits.index') }}">Deposits</a>
                    <a class="hover:underline hover:text-white" href="{{ route('admin.withdrawals.index') }}">Withdrawals</a>
                    <a class="hover:underline hover:text-white" href="{{ route('admin.investments.index') }}">Investments</a>
                    <a class="hover:underline hover:text-white" href="{{ route('admin.settings.index') }}">Settings</a>
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <span class="text-slate-300">{{ auth('admin')->user()?->email }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="px-3 py-1 rounded bg-emerald-600 text-white hover:bg-emerald-500">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    @isset($header)
        <header class="bg-gray-950/80 backdrop-blur border-b border-white/10">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main>
        {{ $slot }}
    </main>
</div>
</body>
</html>
