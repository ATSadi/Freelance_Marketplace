<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WorkVault — Escrow & Milestone Freelance Marketplace</title>
    <meta name="description" content="A freelance marketplace with milestone delivery, mock escrow payments, invoices, and admin dispute mediation.">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|plus-jakarta-sans:500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800 bg-white">
    <!-- Nav -->
    <header x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 20"
        :class="scrolled ? 'glass border-b border-white/40 shadow-soft' : 'bg-transparent'"
        class="fixed top-0 inset-x-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5">
                <x-application-logo />
                <span class="font-display text-lg font-extrabold tracking-tight transition-colors"
                    :class="scrolled ? 'text-slate-900' : 'text-white'">Work<span class="text-accent-400">Vault</span></span>
            </a>
            <nav class="flex items-center gap-2 sm:gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost transition-colors" :class="scrolled ? 'text-slate-600' : 'text-white/90 hover:bg-white/10'">Log in</a>
                    <a href="{{ route('register') }}" class="btn-primary">Get started</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative overflow-hidden text-white">
        <img src="{{ asset('images/wv-hero.png') }}" alt="" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-ink-900 via-ink-900/80 to-ink-900/30"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-40 pb-28">
            <div class="max-w-2xl">
                <span class="eyebrow text-brand-300 animate-fade-up">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-300"></span> WorkVault Platform
                </span>
                <h1 class="mt-5 font-display text-4xl sm:text-6xl font-extrabold leading-[1.05] animate-fade-up animate-delay-100">
                    Hire with milestones.<br>
                    <span class="bg-gradient-to-r from-brand-300 to-accent-400 bg-clip-text text-transparent">Pay with confidence.</span>
                </h1>
                <p class="mt-6 text-lg text-white/75 max-w-xl animate-fade-up animate-delay-200">
                    A freelance marketplace with role-based accounts, proposals, milestone delivery,
                    mock escrow, invoices, and admin dispute mediation — beautifully organized.
                </p>
                <div class="mt-9 flex flex-wrap gap-4 animate-fade-up animate-delay-300">
                    @guest
                        <a href="{{ route('register', ['role' => 'client']) }}" class="btn-primary text-base px-6 py-3">
                            Hire as a client
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                        <a href="{{ route('register', ['role' => 'freelancer']) }}" class="btn text-base px-6 py-3 glass-dark text-white hover:bg-white/10">
                            Work as a freelancer
                        </a>
                    @else
                        <a href="{{ url('/dashboard') }}" class="btn-primary text-base px-6 py-3">Go to dashboard</a>
                    @endguest
                </div>

                <dl class="mt-14 grid grid-cols-3 gap-6 max-w-lg animate-fade-up animate-delay-400">
                    <div>
                        <dt class="text-3xl font-display font-bold">3</dt>
                        <dd class="text-sm text-white/60">User roles</dd>
                    </div>
                    <div>
                        <dt class="text-3xl font-display font-bold">100%</dt>
                        <dd class="text-sm text-white/60">Mock escrow ledger</dd>
                    </div>
                    <div>
                        <dt class="text-3xl font-display font-bold">5★</dt>
                        <dd class="text-sm text-white/60">Workflow clarity</dd>
                    </div>
                </dl>
            </div>
        </div>
        <div class="absolute bottom-0 inset-x-0 h-24 bg-gradient-to-t from-white to-transparent"></div>
    </section>

    <!-- Features -->
    <section class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 -mt-10">
        <div class="text-center max-w-2xl mx-auto">
            <span class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> How it works</span>
            <h2 class="mt-3 font-display text-3xl sm:text-4xl font-bold text-slate-900">Everything a project needs, end to end</h2>
            <p class="mt-3 text-slate-500">From signup to payout, WorkVault keeps clients and freelancers aligned.</p>
        </div>

        <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $features = [
                    ['01 — Sign up', 'Choose your role', 'Register as a client or freelancer. Complete your profile before posting or bidding.', 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 19.5a7.5 7.5 0 0 1 15 0'],
                    ['02 — Agree & deliver', 'Proposals & milestones', 'Clients post projects, freelancers bid, then work is tracked milestone by milestone.', 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                    ['03 — Escrow & resolve', 'Approve, pay, mediate', 'Funds hold in mock escrow until approval. Disputes go to admin review with invoices.', 'M12 2.25 3 6v6c0 5.25 3.6 8.4 9 9.75 5.4-1.35 9-4.5 9-9.75V6l-9-3.75Z'],
                ];
            @endphp
            @foreach ($features as $i => $f)
                <div class="wv-card wv-card-hover p-7 animate-fade-up" style="animation-delay: {{ $i * 0.1 }}s">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-brand-gradient text-white shadow-glow">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $f[3] }}"/></svg>
                    </div>
                    <p class="mt-5 eyebrow">{{ $f[0] }}</p>
                    <h3 class="mt-2 text-xl font-display font-bold text-slate-900">{{ $f[1] }}</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">{{ $f[2] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Feature strip -->
    <section class="bg-slate-50 border-y border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-2 lg:grid-cols-4 gap-8">
            @php
                $stats = [
                    ['Milestones', 'Submit, review, approve, and auto-complete projects.'],
                    ['Mock escrow', 'Hold, release, and refund with a full transaction ledger.'],
                    ['Invoices', 'Print-ready payment records for every paid milestone.'],
                    ['Disputes', 'Structured mediation handled by platform admins.'],
                ];
            @endphp
            @foreach ($stats as $s)
                <div class="animate-fade-up">
                    <div class="h-10 w-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center shadow-soft mb-4">
                        <span class="h-2 w-2 rounded-full bg-brand-gradient"></span>
                    </div>
                    <h4 class="font-display font-bold text-slate-900">{{ $s[0] }}</h4>
                    <p class="mt-1 text-sm text-slate-500">{{ $s[1] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- CTA -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="relative overflow-hidden rounded-3xl bg-ink-900 px-8 py-14 sm:px-16 text-center shadow-glow-lg">
            <div class="absolute inset-0 bg-brand-radial opacity-80"></div>
            <div class="relative">
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-white">Ready to start your next project?</h2>
                <p class="mt-3 text-white/70 max-w-xl mx-auto">Join WorkVault and manage freelance work with confidence — from proposal to payout.</p>
                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    @guest
                        <a href="{{ route('register') }}" class="btn-primary text-base px-6 py-3">Create free account</a>
                        <a href="{{ route('login') }}" class="btn text-base px-6 py-3 glass-dark text-white hover:bg-white/10">Log in</a>
                    @else
                        <a href="{{ url('/dashboard') }}" class="btn-primary text-base px-6 py-3">Open dashboard</a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <footer class="border-t border-slate-200 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-3 text-sm text-slate-500">
            <a href="/" class="flex items-center gap-2"><x-application-logo :with-text="true" /></a>
            <p>&copy; {{ date('Y') }} WorkVault — Escrow & milestone freelance platform</p>
        </div>
    </footer>
</body>
</html>
