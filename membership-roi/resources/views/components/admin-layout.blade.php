<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'IQBIT') }} - Admin</title>
    @php
        $faviconCandidates = [
            'images/favicon.ico',
            'images/favicon.png',
            'favicon.ico',
        ];
        $faviconPath = null;
        foreach ($faviconCandidates as $p) {
            if (file_exists(public_path($p))) {
                $faviconPath = $p;
                break;
            }
        }
    @endphp
    <link rel="icon" href="{{ $faviconPath ? asset($faviconPath) : '/favicon.ico' }}">

    @if (!app()->environment('testing'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="font-sans antialiased">
<div class="page-wrap">
    <nav class="topbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                    <x-application-logo class="h-8 w-8 text-gray-800" />
                    <span class="font-semibold text-gray-800">IQBIT Admin</span>
                </a>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-700">
                    <a class="hover:underline" href="{{ route('admin.users.index') }}">Users</a>
                    <a class="hover:underline" href="{{ route('admin.deposits.index') }}">Deposits</a>
                    <a class="hover:underline" href="{{ route('admin.withdrawals.index') }}">Withdrawals</a>
                    <a class="hover:underline" href="{{ route('admin.investments.index') }}">Investments</a>
                    <a class="hover:underline" href="{{ route('admin.wallet_adjustments.index') }}">Wallet Adjust</a>
                    <a class="hover:underline" href="{{ route('admin.settings.index') }}">Settings</a>
                    <a class="hover:underline" href="{{ route('admin.roi_rates.edit') }}">ROI Rates</a>
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <span class="text-gray-600">{{ auth('admin')->user()?->email }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="btn-dark px-3 py-1">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    @isset($header)
        <header class="border-b border-slate-900/5 bg-white/40 backdrop-blur-xl">
            <div class="page-container py-7">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main class="page-section">
        <div class="page-container">
            {{ $slot }}
        </div>
    </main>
</div>
</body>
</html>
