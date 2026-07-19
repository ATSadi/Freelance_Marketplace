<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Finance operations</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Withdrawal Requests</h2>
            <p class="mt-1 text-sm text-slate-500">Review freelancer payout details, approve requests, and record completed transfers.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <x-flash-status />
        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">{{ $errors->first() }}</div>
        @endif

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="stat-card"><p class="eyebrow">Awaiting review</p><p class="mt-3 font-display text-3xl font-bold text-amber-600">${{ number_format($pendingTotal, 2) }}</p></div>
            <div class="stat-card"><p class="eyebrow">Approved</p><p class="mt-3 font-display text-3xl font-bold text-blue-600">${{ number_format($approvedTotal, 2) }}</p></div>
            <div class="stat-card"><p class="eyebrow">Paid all-time</p><p class="mt-3 font-display text-3xl font-bold text-emerald-600">${{ number_format($paidTotal, 2) }}</p></div>
        </div>

        <div class="wv-card p-5">
            <div class="flex flex-wrap gap-2">
                @foreach (['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'paid' => 'Paid', 'rejected' => 'Rejected'] as $key => $label)
                    <a href="{{ route('admin.withdrawals.index', $key ? ['status' => $key] : []) }}"
                        class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ $status === $key ? 'bg-brand-600 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        <div class="wv-card overflow-hidden">
            @if ($withdrawals->isEmpty())
                <div class="p-14 text-center text-sm text-slate-500">No withdrawal requests match this filter.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] text-left text-sm">
                        <thead class="border-b border-slate-200 bg-slate-50/80 text-xs uppercase tracking-wide text-slate-400">
                            <tr><th class="px-5 py-4">Freelancer</th><th class="px-5 py-4">Destination</th><th class="px-5 py-4">Amount</th><th class="px-5 py-4">Status</th><th class="px-5 py-4">Requested</th><th class="px-5 py-4">Action</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($withdrawals as $withdrawal)
                                <tr class="align-top">
                                    <td class="px-5 py-5"><p class="font-semibold text-slate-900">{{ $withdrawal->user->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $withdrawal->user->email }}</p></td>
                                    <td class="px-5 py-5"><p class="font-semibold text-slate-800">{{ $withdrawal->payoutMethod->bank_name }}</p><p class="mt-1 text-xs text-slate-500">{{ $withdrawal->payoutMethod->account_name }} · {{ $withdrawal->payoutMethod->maskedAccount() }} · {{ $withdrawal->payoutMethod->currency }}</p></td>
                                    <td class="px-5 py-5 font-display text-lg font-bold text-slate-900">${{ number_format($withdrawal->amount, 2) }}</td>
                                    <td class="px-5 py-5"><span class="wv-badge {{ match($withdrawal->status) { 'paid' => 'bg-emerald-50 text-emerald-700', 'rejected' => 'bg-rose-50 text-rose-700', 'approved' => 'bg-blue-50 text-blue-700', default => 'bg-amber-50 text-amber-700' } }}">{{ ucfirst($withdrawal->status) }}</span>@if($withdrawal->processor)<p class="mt-2 text-xs text-slate-400">by {{ $withdrawal->processor->name }}</p>@endif</td>
                                    <td class="px-5 py-5 text-slate-500">{{ $withdrawal->created_at->format('M j, Y') }}<p class="mt-1 text-xs">{{ $withdrawal->created_at->format('g:i A') }}</p></td>
                                    <td class="px-5 py-5">
                                        @if (in_array($withdrawal->status, ['pending', 'approved'], true))
                                            <form method="POST" action="{{ route('admin.withdrawals.update', $withdrawal) }}" class="space-y-2">
                                                @csrf @method('PATCH')
                                                <select name="status" class="wv-input !py-2 text-xs">
                                                    @if ($withdrawal->status === 'pending')<option value="approved">Approve request</option>@endif
                                                    @if ($withdrawal->status === 'approved')<option value="paid">Mark transfer paid</option>@endif
                                                    <option value="rejected">Reject request</option>
                                                </select>
                                                <input name="admin_notes" class="wv-input !py-2 text-xs" placeholder="Reference or note" value="{{ $withdrawal->admin_notes }}" />
                                                <button class="btn-primary !px-3 !py-2 text-xs">Update</button>
                                            </form>
                                        @else
                                            <p class="max-w-56 text-xs text-slate-500">{{ $withdrawal->admin_notes ?: 'No processing note.' }}</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 p-5">{{ $withdrawals->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
