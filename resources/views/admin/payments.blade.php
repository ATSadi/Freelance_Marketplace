<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Administration</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Payments</h2>
            <p class="mt-1 text-sm text-slate-500">Audit mock escrow events and Stripe funding attempts.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div class="stat-card">
            <span class="eyebrow">Total mock escrow released</span>
            <p class="mt-2 font-display text-3xl font-bold text-emerald-600">${{ number_format($releasedTotal, 2) }}</p>
        </div>

        <section class="wv-card overflow-hidden">
            <div class="border-b border-slate-100 p-5"><h3 class="font-display text-lg font-bold">Escrow ledger</h3></div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500"><tr><th class="p-4">Date</th><th class="p-4">Project</th><th class="p-4">Type</th><th class="p-4">Parties</th><th class="p-4">Amount</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($transactions as $transaction)
                            <tr><td class="p-4 text-slate-500">{{ $transaction->created_at->format('M j, Y') }}</td><td class="p-4 font-medium">{{ $transaction->project->title }}</td><td class="p-4"><x-transaction-type-badge :type="$transaction->type" /></td><td class="p-4 text-slate-500">{{ $transaction->payer->name }} → {{ $transaction->payee->name }}</td><td class="p-4 font-semibold">${{ number_format($transaction->amount, 2) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        {{ $transactions->links() }}

        <section class="wv-card p-5">
            <h3 class="font-display text-lg font-bold">Stripe funding</h3>
            <div class="mt-4 space-y-3">
                @forelse ($stripePayments as $payment)
                    <div class="flex flex-wrap justify-between gap-3 rounded-xl border border-slate-200 p-4">
                        <div><p class="font-semibold">{{ $payment->milestone->project->title }}</p><p class="text-xs text-slate-500">{{ $payment->user->name }} · {{ $payment->stripe_session_id ?? 'Session pending' }}</p></div>
                        <div class="text-right"><p class="font-semibold">${{ number_format($payment->amount / 100, 2) }}</p><span class="wv-badge bg-slate-100 text-slate-700">{{ ucfirst($payment->status) }}</span></div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No Stripe funding attempts yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
