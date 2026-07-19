<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WorkVault') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|plus-jakarta-sans:500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased">
        <div class="min-h-screen grid lg:grid-cols-2">
            <!-- Brand panel -->
            <div class="relative hidden lg:flex flex-col justify-between overflow-hidden text-white p-12 bg-ink-900">
                <img src="{{ asset('images/wv-auth.png') }}" alt=""
                    class="absolute inset-0 h-full w-full object-cover opacity-90">
                <div class="absolute inset-0 bg-gradient-to-t from-ink-900 via-ink-900/50 to-transparent"></div>

                <a href="/" class="relative z-10 inline-flex items-center gap-2.5">
                    <x-application-logo />
                    <span class="font-display text-lg font-extrabold tracking-tight text-white">Work<span class="text-brand-300">Vault</span></span>
                </a>

                <div class="relative z-10 animate-fade-up">
                    <h2 class="font-display text-3xl font-bold leading-tight">Hire with milestones.<br>Pay with confidence.</h2>
                    <p class="mt-4 max-w-md text-white/70">
                        Milestone payments, Stripe funding, delivery tracking, invoices, and admin dispute mediation — all in one workspace.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-2">
                        <span class="wv-badge glass-dark text-white/90">Mock escrow ledger</span>
                        <span class="wv-badge glass-dark text-white/90">Milestone tracking</span>
                        <span class="wv-badge glass-dark text-white/90">Fair disputes</span>
                    </div>
                </div>
            </div>

            <!-- Form panel -->
            <div class="flex flex-col justify-center items-center px-6 py-12 app-canvas">
                <div class="w-full sm:max-w-md">
                    <a href="/" class="lg:hidden flex items-center justify-center gap-2 mb-8">
                        <x-application-logo :with-text="true" />
                    </a>
                    <div class="wv-card p-8 animate-scale-in">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
