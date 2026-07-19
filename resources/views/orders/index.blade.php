<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Delivery workspace</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">My Orders</h2>
                <p class="mt-1 text-sm text-slate-500">Track every contract, milestone, deadline, and payment in one place.</p>
            </div>
            @if ($user->role === \App\Models\User::ROLE_FREELANCER)
                <a href="{{ route('wallet.index') }}" class="btn-primary">Open Wallet</a>
            @endif
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div class="inline-flex max-w-full gap-1 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-1.5 shadow-soft">
            @foreach (['active' => 'Active orders', 'completed' => 'Past orders', 'cancelled' => 'Cancelled'] as $key => $label)
                <a href="{{ route('orders.index', ['tab' => $key]) }}"
                    class="inline-flex whitespace-nowrap items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition {{ $tab === $key ? 'bg-brand-600 text-white shadow-soft' : 'text-slate-600 hover:bg-slate-50' }}">
                    {{ $label }}
                    <span class="rounded-full px-2 py-0.5 text-xs {{ $tab === $key ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $counts[$key] }}</span>
                </a>
            @endforeach
        </div>

        @if ($orders->isEmpty())
            <div class="wv-card px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.1a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25v-4.1m16.5 0a2.25 2.25 0 0 0 .75-1.68V6.75A2.25 2.25 0 0 0 18.75 4.5H5.25A2.25 2.25 0 0 0 3 6.75v5.72c0 .67.3 1.3.75 1.68m16.5 0-6.62 3.31a3.75 3.75 0 0 1-3.26 0L3.75 14.15"/></svg>
                </div>
                <h3 class="mt-4 font-display text-xl font-bold text-slate-900">No {{ $tab }} orders</h3>
                <p class="mt-2 text-sm text-slate-500">Orders will appear here as contracts move through their lifecycle.</p>
            </div>
        @else
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($orders as $order)
                    @php
                        $complete = $order->milestones->filter->isCompleted()->count();
                        $total = $order->milestones->count();
                        $progress = $total ? round($complete / $total * 100) : 0;
                        $next = $order->milestones->first(fn ($milestone) => ! $milestone->isCompleted());
                        $partner = $user->role === \App\Models\User::ROLE_CLIENT ? $order->freelancer : $order->client;
                    @endphp
                    <article class="wv-card group flex flex-col overflow-hidden transition hover:-translate-y-1 hover:shadow-card">
                        <div class="h-1.5 bg-gradient-to-r from-brand-500 via-violet-500 to-cyan-400"></div>
                        <div class="flex flex-1 flex-col p-5">
                            <div class="flex items-start justify-between gap-3">
                                <span class="wv-badge bg-brand-50 text-brand-700">{{ $order->category }}</span>
                                <x-project-status-badge :status="$order->status" />
                            </div>
                            <h3 class="mt-4 font-display text-lg font-bold text-slate-900">{{ $order->title }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $user->role === \App\Models\User::ROLE_CLIENT ? 'Freelancer' : 'Client' }}: {{ $partner?->name }}</p>

                            <div class="mt-5">
                                <div class="flex justify-between text-xs font-medium text-slate-500">
                                    <span>{{ $complete }} of {{ $total }} milestones done</span>
                                    <span>{{ $progress }}%</span>
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-brand-gradient transition-all" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            <div class="mt-4 rounded-xl bg-slate-50 p-3 text-sm">
                                @if ($next)
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Next milestone</p>
                                    <div class="mt-1 flex items-center justify-between gap-3">
                                        <span class="font-semibold text-slate-700">{{ $next->title }}</span>
                                        <span class="whitespace-nowrap text-xs text-slate-500">{{ $next->due_date->format('M j') }}</span>
                                    </div>
                                @else
                                    <p class="font-medium text-emerald-700">All milestones completed</p>
                                @endif
                            </div>

                            <div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4">
                                <div>
                                    <p class="text-xs text-slate-400">Contract value</p>
                                    <p class="font-display text-lg font-bold text-slate-900">${{ number_format($order->acceptedProposal->first()?->proposed_amount ?? $order->milestones->sum('amount'), 2) }}</p>
                                </div>
                                <a href="{{ route('orders.show', $order) }}" class="btn-primary">View Order</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            <div>{{ $orders->links() }}</div>
        @endif
    </div>
</x-app-layout>
