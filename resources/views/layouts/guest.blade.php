<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WorkVault') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-b from-sky-50 to-slate-100 px-4">
            <div class="text-center">
                <a href="/" class="inline-flex flex-col items-center gap-2">
                    <span class="text-2xl font-bold tracking-tight text-slate-900">WorkVault</span>
                    <span class="text-xs text-slate-500">{{ __('Escrow & milestone freelance platform') }}</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-5 bg-white shadow-md overflow-hidden sm:rounded-xl border border-slate-200/80">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
