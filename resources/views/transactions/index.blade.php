<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Escrow ledger</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Transactions</h2>
            <p class="mt-1 text-sm text-slate-500">Mock escrow records for milestone payments.</p>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
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
                <p class="mt-1 text-sm text-slate-500">Mock escrow ledger for milestone payments.</p>

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
