<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if (!app()->environment('testing'))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="/">
                    <x-application-logo class="block h-20 w-auto fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-5 surface-solid">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
