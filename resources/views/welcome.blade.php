<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WorkVault — Escrow & Milestone Freelance Platform</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="font-sans antialiased text-slate-900 bg-slate-50">
    <div class="min-h-screen flex flex-col">
        <header class="border-b border-slate-200/80 bg-white/80 backdrop-blur sticky top-0 z-10">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="/" class="text-xl font-bold tracking-tight text-slate-900">WorkVault</a>
                <nav class="flex items-center gap-2 sm:gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold bg-slate-900 text-white hover:bg-slate-800">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900">
                            Log in
                        </a>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold bg-slate-900 text-white hover:bg-slate-800">
                            Get started
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="flex-1">
            <section class="relative overflow-hidden">
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-sky-100 via-slate-50 to-slate-100"></div>
                <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
                    <p class="text-sm font-semibold uppercase tracking-widest text-sky-700">WorkVault</p>
                    <h1 class="mt-3 max-w-3xl text-4xl sm:text-5xl font-bold tracking-tight text-slate-900 leading-tight">
                        Hire with milestones. Pay with confidence.
                    </h1>
                    <p class="mt-5 max-w-2xl text-lg text-slate-600">
                        A freelance marketplace with role-based accounts, proposals, milestone delivery, mock escrow, invoices, and admin dispute mediation.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        @guest
                            <a href="{{ route('register', ['role' => 'client']) }}"
                                class="inline-flex items-center px-5 py-2.5 rounded-md text-sm font-semibold bg-slate-900 text-white hover:bg-slate-800">
                                Hire as a client
                            </a>
                            <a href="{{ route('register', ['role' => 'freelancer']) }}"
                                class="inline-flex items-center px-5 py-2.5 rounded-md text-sm font-semibold bg-white text-slate-900 border border-slate-300 hover:bg-slate-50">
                                Work as a freelancer
                            </a>
                        @else
                            <a href="{{ url('/dashboard') }}"
                                class="inline-flex items-center px-5 py-2.5 rounded-md text-sm font-semibold bg-slate-900 text-white hover:bg-slate-800">
                                Go to dashboard
                            </a>
                        @endguest
                    </div>
                </div>
            </section>

            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <h2 class="text-2xl font-bold text-slate-900">How it works</h2>
                <p class="mt-2 text-slate-600 max-w-2xl">Clear account paths for clients, freelancers, and admins — from signup to payout.</p>

                <ol class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <li class="border border-slate-200 rounded-xl p-6 bg-white">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sky-700">01 — Sign up</p>
                        <h3 class="mt-2 font-semibold text-lg">Choose your role</h3>
                        <p class="mt-2 text-sm text-slate-600">Register as a client or freelancer. Complete your profile before posting or bidding.</p>
                    </li>
                    <li class="border border-slate-200 rounded-xl p-6 bg-white">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sky-700">02 — Agree & deliver</p>
                        <h3 class="mt-2 font-semibold text-lg">Proposals & milestones</h3>
                        <p class="mt-2 text-sm text-slate-600">Clients post projects, freelancers bid, then work is tracked milestone by milestone.</p>
                    </li>
                    <li class="border border-slate-200 rounded-xl p-6 bg-white">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sky-700">03 — Escrow & resolve</p>
                        <h3 class="mt-2 font-semibold text-lg">Approve, pay, mediate</h3>
                        <p class="mt-2 text-sm text-slate-600">Funds hold in mock escrow until approval. Disputes go to admin review with invoices for paid work.</p>
                    </li>
                </ol>
            </section>

            <section class="border-t border-slate-200 bg-white">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-center sm:text-left">
                    <div>
                        <p class="text-2xl font-bold text-slate-900">3 roles</p>
                        <p class="mt-1 text-sm text-slate-600">Client, freelancer, admin</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900">Milestones</p>
                        <p class="mt-1 text-sm text-slate-600">Submit, review, approve</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900">Escrow</p>
                        <p class="mt-1 text-sm text-slate-600">Hold, release, refund ledger</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900">Invoices</p>
                        <p class="mt-1 text-sm text-slate-600">Print-ready payment records</p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 py-8">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between gap-3 text-sm text-slate-500">
                <p>&copy; {{ date('Y') }} WorkVault</p>
                <p>University lab project — escrow & milestone tracking</p>
            </div>
        </footer>
    </div>
</body>
</html>
