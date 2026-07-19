<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Freelancer finances</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Wallet</h2>
                <p class="mt-1 text-sm text-slate-500">Track funded work, released earnings, and withdrawals.</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="btn-secondary">Full Transaction Ledger</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8" x-data="{ addingMethod: {{ $errors->hasAny(['account_name', 'bank_name', 'account_number', 'routing_number']) ? 'true' : 'false' }} }">
        <x-flash-status />

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                <ul class="list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-950 via-indigo-950 to-brand-800 p-6 text-white shadow-card sm:col-span-2">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-200">Available to withdraw</p>
                <p class="mt-3 font-display text-4xl font-bold">${{ number_format($available, 2) }}</p>
                <p class="mt-2 text-sm text-indigo-200">USD balance after pending and completed withdrawals</p>
                @if ($payoutMethods->isNotEmpty() && $available >= 10)
                    <a href="#withdraw" class="mt-6 inline-flex rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-brand-700 shadow-soft transition hover:bg-indigo-50">Withdraw funds</a>
                @endif
            </div>
            <div class="stat-card">
                <p class="eyebrow">Total earned</p>
                <p class="mt-3 font-display text-3xl font-bold text-slate-900">${{ number_format($released, 2) }}</p>
                <p class="mt-1 text-sm text-slate-500">Released by clients</p>
            </div>
            <div class="stat-card">
                <p class="eyebrow">Pending release</p>
                <p class="mt-3 font-display text-3xl font-bold text-amber-600">${{ number_format($pendingPayments->sum('amount'), 2) }}</p>
                <p class="mt-1 text-sm text-slate-500">Across {{ $pendingPayments->count() }} funded milestones</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(320px,.65fr)]">
            <div class="space-y-6">
                <section class="wv-card p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="eyebrow">Release schedule</p>
                            <h3 class="mt-2 font-display text-xl font-bold text-slate-900">Pending payments</h3>
                        </div>
                        <span class="wv-badge bg-amber-50 text-amber-700">{{ $pendingPayments->count() }} pending</span>
                    </div>
                    @if ($pendingPayments->isEmpty())
                        <p class="mt-5 rounded-xl bg-slate-50 p-5 text-sm text-slate-500">No payments are waiting for release.</p>
                    @else
                        <div class="mt-4 divide-y divide-slate-100">
                            @foreach ($pendingPayments as $payment)
                                <div class="flex flex-wrap items-center justify-between gap-4 py-4">
                                    <div>
                                        <a href="{{ route('orders.show', $payment->project) }}" class="font-semibold text-slate-900 hover:text-brand-700">{{ $payment->milestone?->title }}</a>
                                        <p class="mt-1 text-xs text-slate-500">{{ $payment->project->title }} · Due {{ $payment->milestone?->due_date?->format('M j, Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-display text-lg font-bold text-slate-900">${{ number_format($payment->amount, 2) }}</p>
                                        <x-milestone-status-badge :status="$payment->milestone?->status" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section class="wv-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="eyebrow">Money out</p>
                            <h3 class="mt-2 font-display text-xl font-bold text-slate-900">Withdrawal history</h3>
                        </div>
                        <p class="text-sm font-semibold text-slate-500">${{ number_format($reserved, 2) }} requested</p>
                    </div>
                    @if ($withdrawals->isEmpty())
                        <p class="mt-5 rounded-xl bg-slate-50 p-5 text-sm text-slate-500">You have not requested a withdrawal yet.</p>
                    @else
                        <div class="mt-4 overflow-x-auto">
                            <table class="w-full min-w-[620px] text-left text-sm">
                                <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-400">
                                    <tr><th class="pb-3">Requested</th><th class="pb-3">Method</th><th class="pb-3">Amount</th><th class="pb-3">Status</th><th class="pb-3">Admin note</th></tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($withdrawals as $withdrawal)
                                        <tr>
                                            <td class="py-4 text-slate-500">{{ $withdrawal->created_at->format('M j, Y') }}</td>
                                            <td class="py-4 font-medium text-slate-700">{{ $withdrawal->payoutMethod->bank_name }} {{ $withdrawal->payoutMethod->maskedAccount() }}</td>
                                            <td class="py-4 font-bold text-slate-900">${{ number_format($withdrawal->amount, 2) }}</td>
                                            <td class="py-4"><span class="wv-badge {{ match($withdrawal->status) { 'paid' => 'bg-emerald-50 text-emerald-700', 'rejected' => 'bg-rose-50 text-rose-700', 'approved' => 'bg-blue-50 text-blue-700', default => 'bg-amber-50 text-amber-700' } }}">{{ ucfirst($withdrawal->status) }}</span></td>
                                            <td class="py-4 text-slate-500">{{ $withdrawal->admin_notes ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $withdrawals->links() }}</div>
                    @endif
                </section>

                <section class="wv-card p-6">
                    <p class="eyebrow">Money in</p>
                    <h3 class="mt-2 font-display text-xl font-bold text-slate-900">Released payment history</h3>
                    <div class="mt-4 divide-y divide-slate-100">
                        @forelse ($paymentHistory as $payment)
                            <div class="flex items-center justify-between gap-4 py-4">
                                <div><p class="font-semibold text-slate-800">{{ $payment->milestone?->title }}</p><p class="text-xs text-slate-500">{{ $payment->project->title }} · {{ $payment->created_at->format('M j, Y') }}</p></div>
                                <p class="font-display text-lg font-bold text-emerald-600">+${{ number_format($payment->amount, 2) }}</p>
                            </div>
                        @empty
                            <p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-500">Released payments will appear here.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="wv-card p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div><p class="eyebrow">Payout settings</p><h3 class="mt-2 font-display text-lg font-bold text-slate-900">Withdrawal methods</h3></div>
                        <button type="button" @click="addingMethod = !addingMethod" class="text-sm font-bold text-brand-600">+ Add</button>
                    </div>

                    <div class="mt-4 space-y-3">
                        @foreach ($payoutMethods as $method)
                            <div class="rounded-xl border border-slate-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div><p class="font-semibold text-slate-800">{{ $method->bank_name }}</p><p class="mt-1 text-sm text-slate-500">{{ $method->account_name }} · {{ $method->maskedAccount() }}</p></div>
                                    @if ($method->is_default)<span class="wv-badge bg-emerald-50 text-emerald-700">Default</span>@endif
                                </div>
                                <form method="POST" action="{{ route('wallet.methods.destroy', $method) }}" class="mt-3">
                                    @csrf @method('DELETE')
                                    <button class="text-xs font-semibold text-rose-600 hover:text-rose-800">Remove method</button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <form x-show="addingMethod" x-transition method="POST" action="{{ route('wallet.methods.store') }}" class="mt-5 space-y-3 border-t border-slate-100 pt-5">
                        @csrf
                        <div><x-input-label for="type" value="Method type" /><select id="type" name="type" class="wv-input"><option value="bank">Bank account</option><option value="mobile_wallet">Mobile wallet</option></select></div>
                        <div><x-input-label for="account_name" value="Account holder" /><input id="account_name" name="account_name" value="{{ old('account_name') }}" class="wv-input" required /></div>
                        <div><x-input-label for="bank_name" value="Bank / wallet provider" /><input id="bank_name" name="bank_name" value="{{ old('bank_name') }}" class="wv-input" required /></div>
                        <div><x-input-label for="account_number" value="Account number / IBAN" /><input id="account_number" name="account_number" class="wv-input" required autocomplete="off" /></div>
                        <div><x-input-label for="routing_number" value="Routing / SWIFT (optional)" /><input id="routing_number" name="routing_number" class="wv-input" autocomplete="off" /></div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><x-input-label for="country" value="Country" /><input id="country" name="country" value="{{ old('country', 'US') }}" maxlength="2" class="wv-input uppercase" required /></div>
                            <div><x-input-label for="currency" value="Currency" /><input id="currency" name="currency" value="{{ old('currency', 'USD') }}" maxlength="3" class="wv-input uppercase" required /></div>
                        </div>
                        <x-primary-button class="w-full justify-center">Save payout method</x-primary-button>
                        <p class="text-xs leading-relaxed text-slate-400">Account and routing numbers are encrypted before storage. Only the last four digits remain visible.</p>
                    </form>
                </section>

                <section id="withdraw" class="wv-card p-6">
                    <p class="eyebrow">New request</p>
                    <h3 class="mt-2 font-display text-lg font-bold text-slate-900">Withdraw earnings</h3>
                    @if ($payoutMethods->isEmpty())
                        <p class="mt-4 rounded-xl bg-amber-50 p-4 text-sm text-amber-800">Add a withdrawal method before requesting funds.</p>
                    @elseif ($available < 10)
                        <p class="mt-4 rounded-xl bg-slate-50 p-4 text-sm text-slate-500">A minimum available balance of $10.00 is required.</p>
                    @else
                        <form method="POST" action="{{ route('wallet.withdrawals.store') }}" class="mt-4 space-y-4">
                            @csrf
                            <div><x-input-label for="payout_method_id" value="Send to" /><select id="payout_method_id" name="payout_method_id" class="wv-input">@foreach ($payoutMethods as $method)<option value="{{ $method->id }}">{{ $method->bank_name }} {{ $method->maskedAccount() }}</option>@endforeach</select></div>
                            <div><x-input-label for="amount" value="Amount (USD)" /><input id="amount" name="amount" type="number" min="10" max="{{ $available }}" step="0.01" class="wv-input" placeholder="0.00" required /></div>
                            <p class="text-xs text-slate-500">Available: <strong>${{ number_format($available, 2) }}</strong>. The admin team will review this request before payout.</p>
                            <x-primary-button class="w-full justify-center">Request Withdrawal</x-primary-button>
                        </form>
                    @endif
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
