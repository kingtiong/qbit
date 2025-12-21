<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if (!app()->environment('testing'))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="page-wrap">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="border-b border-white/10 bg-black/50 backdrop-blur-xl">
                    <div class="page-container py-7">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="page-section">
                <div class="page-container">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
