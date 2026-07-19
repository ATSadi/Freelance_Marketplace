<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WorkVault') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|plus-jakarta-sans:500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800">
        <div class="min-h-screen app-canvas">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="relative">
                    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 animate-fade-up">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="pb-16">
                {{ $slot }}
            </main>

            <footer class="border-t border-slate-200/70 py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-400">
                    <span class="inline-flex items-center gap-2">
                        <x-application-logo />
                        <span>&copy; {{ date('Y') }} WorkVault</span>
                    </span>
                    <span>Escrow &amp; milestone freelance platform</span>
                </div>
            </footer>
        </div>
    </body>
</html>
