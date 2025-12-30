<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'IQBIT') }}</title>
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
<body class="min-h-screen flex items-center justify-center">
    <div class="surface-solid p-8 w-full max-w-md">
        <div class="flex items-center gap-3 mb-6">
            <x-application-logo class="w-12 h-12 text-gray-800" />
            <div>
                <div class="text-xl font-semibold">IQBIT</div>
                <div class="text-sm text-gray-600">{{ __('Member & Admin access') }}</div>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 surface-muted text-sm text-gray-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex gap-3">
            <a href="{{ route('login') }}" class="btn-dark normal-case text-sm">{{ __('Member Login') }}</a>
            <a href="{{ route('admin.login') }}" class="btn-neutral normal-case text-sm">{{ __('Admin Login') }}</a>
        </div>

        <div class="mt-6 text-sm text-gray-600">
            {{ __('Registration is invitation-only.') }}
        </div>
    </div>
</body>
</html>
