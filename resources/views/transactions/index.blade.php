<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> {{ $user->role === \App\Models\User::ROLE_CLIENT ? 'Client billing' : 'Escrow ledger' }}</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">{{ $user->role === \App\Models\User::ROLE_CLIENT ? 'Billing & Payments' : 'Transactions' }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $user->role === \App\Models\User::ROLE_CLIENT ? 'Fund project milestones securely with Stripe and track your payments.' : 'Escrow records for milestone payments.' }}</p>
            </div>
            @if ($user->role === \App\Models\User::ROLE_CLIENT && $fundableMilestones->isNotEmpty())
                <a href="#fund-milestones" class="btn-primary">Add funds with Stripe</a>
            @endif
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <x-flash-status />

        @if ($errors->has('stripe'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                {{ $errors->first('stripe') }}
            </div>
        @endif

        @if ($user->role === \App\Models\User::ROLE_CLIENT)
            <section id="fund-milestones" class="wv-card overflow-hidden">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 bg-gradient-to-r from-brand-50/80 to-white p-6">
                    <div>
                        <p class="eyebrow">Secure checkout @if (! $stripeConfigured)<span class="ml-2 rounded-full bg-brand-100 px-2 py-0.5 text-[10px] text-brand-700">Demo</span>@endif</p>
                        <h3 class="mt-2 font-display text-xl font-bold text-slate-900">Fund your milestones</h3>
                        <p class="mt-1 max-w-2xl text-sm text-slate-500">Choose a milestone below. Stripe handles the card payment and the funds are recorded in escrow for that project.</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-right shadow-soft">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Ready to fund</p>
                        <p class="mt-1 font-display text-xl font-bold text-slate-900">${{ number_format($fundableMilestones->sum('amount'), 2) }}</p>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($fundableMilestones as $milestone)
                        <div class="flex flex-wrap items-center justify-between gap-4 p-5">
                            <div class="min-w-0">
                                <a href="{{ route('orders.show', $milestone->project) }}" class="font-semibold text-slate-900 hover:text-brand-700">{{ $milestone->title }}</a>
                                <p class="mt-1 truncate text-sm text-slate-500">{{ $milestone->project->title }} · {{ $milestone->project->freelancer?->name ?? 'Freelancer not assigned' }}</p>
                                <p class="mt-1 text-xs text-slate-400">Due {{ $milestone->due_date->format('M j, Y') }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <p class="font-display text-xl font-bold text-slate-900">${{ number_format($milestone->amount, 2) }}</p>
                                @if ($stripeConfigured)
                                    <form method="POST" action="{{ route('stripe.checkout', $milestone) }}">
                                        @csrf
                                        <button type="submit" class="btn-primary whitespace-nowrap">Fund with Stripe</button>
                                    </form>
                                @else
                                    <button type="button" class="btn-primary whitespace-nowrap" title="Stripe demo">
                                        Fund with Stripe
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <p class="font-medium text-slate-700">No milestones need funding</p>
                            <p class="mt-1 text-sm text-slate-500">New milestones from active orders will appear here.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            @if ($stripePayments->isNotEmpty())
                <section class="wv-card p-6">
                    <h3 class="font-display text-lg font-bold text-slate-900">Stripe payment activity</h3>
                    <div class="mt-4 divide-y divide-slate-100">
                        @foreach ($stripePayments as $payment)
                            <div class="flex items-center justify-between gap-4 py-3">
                                <div class="min-w-0">
                                    <p class="truncate font-medium text-slate-800">{{ $payment->milestone->title }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $payment->milestone->project->title }} · {{ $payment->created_at->format('M j, Y g:i A') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-slate-900">${{ number_format($payment->amount / 100, 2) }}</p>
                                    <span class="wv-badge {{ $payment->status === \App\Models\StripePayment::STATUS_PAID ? 'bg-emerald-50 text-emerald-700' : ($payment->status === \App\Models\StripePayment::STATUS_FAILED ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst($payment->status) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @if ($user->role === \App\Models\User::ROLE_CLIENT)
                <div class="stat-card">
                    <span class="eyebrow">Currently in escrow</span>
                    <p class="mt-2 text-3xl font-display font-bold text-amber-600">${{ number_format($totalHeld, 2) }}</p>
                </div>
                <div class="stat-card">
                    <span class="eyebrow">Released to freelancers</span>
                    <p class="mt-2 text-3xl font-display font-bold text-emerald-600">${{ number_format($totalReleased, 2) }}</p>
                </div>
            @else
                <div class="stat-card sm:col-span-2">
                    <span class="eyebrow">Total earned</span>
                    <p class="mt-2 text-3xl font-display font-bold text-emerald-600">${{ number_format($totalEarned, 2) }}</p>
                </div>
            @endif
        </div>

        <div class="wv-card">
            <div class="p-6">
                <h3 class="text-lg font-display font-bold text-slate-900">Transaction history</h3>
                <p class="mt-1 text-sm text-slate-500">Escrow ledger for milestone payments.</p>

                @if ($transactions->isEmpty())
                    <p class="mt-6 text-sm text-slate-500">No transactions yet.</p>
                @else
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-left text-slate-400">
                                    <th class="py-2 pr-4 font-semibold uppercase text-xs tracking-wide">Date</th>
                                    <th class="py-2 pr-4 font-semibold uppercase text-xs tracking-wide">Type</th>
                                    <th class="py-2 pr-4 font-semibold uppercase text-xs tracking-wide">Project</th>
                                    <th class="py-2 pr-4 font-semibold uppercase text-xs tracking-wide">Amount</th>
                                    <th class="py-2 font-semibold uppercase text-xs tracking-wide">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($transactions as $transaction)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 pr-4 whitespace-nowrap text-slate-500">{{ $transaction->created_at->format('M j, Y') }}</td>
                                        <td class="py-3 pr-4"><x-transaction-type-badge :type="$transaction->type" /></td>
                                        <td class="py-3 pr-4">
                                            <a href="{{ route('projects.show', $transaction->project) }}" class="font-medium text-brand-600 hover:text-brand-800">{{ $transaction->project->title }}</a>
                                        </td>
                                        <td class="py-3 pr-4 font-semibold text-slate-900">${{ number_format($transaction->amount, 2) }}</td>
                                        <td class="py-3 text-slate-500">{{ $transaction->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $transactions->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
