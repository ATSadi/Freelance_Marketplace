@php
    $status = session('status');
    $map = [
        'project-created' => ['ok', 'Project posted successfully.'],
        'project-updated' => ['ok', 'Project updated successfully.'],
        'project-deleted' => ['ok', 'Project deleted successfully.'],
        'proposal-accepted' => ['ok', 'Proposal accepted! A starter milestone plan was created for this order.'],
        'proposal-rejected' => ['warn', 'Proposal rejected.'],
        'proposal-submitted' => ['ok', 'Proposal submitted successfully.'],
        'milestone-created' => ['ok', 'Milestone added.'],
        'milestone-started' => ['info', 'Milestone marked as in progress.'],
        'milestone-submitted' => ['info', 'Milestone submitted for client review.'],
        'milestone-approved' => ['ok', 'Milestone approved and escrow payment released.'],
        'milestone-changes-requested' => ['warn', 'Changes requested. Freelancer can revise and resubmit.'],
        'dispute-opened' => ['warn', 'Dispute opened. An admin will review it shortly.'],
        'dispute-resolved' => ['ok', 'Dispute resolved and financial action applied.'],
        'stripe-funded' => ['ok', 'Stripe payment confirmed. Escrow funding is now recorded for this milestone.'],
        'review-submitted' => ['ok', 'Your review was published.'],
        'user-activated' => ['ok', 'User access restored.'],
        'user-suspended' => ['warn', 'User account suspended.'],
        'project-moderated' => ['ok', 'Project status updated.'],
        'order-cancelled' => ['warn', 'Order cancelled. Unreleased escrow holds were refunded.'],
        'payout-method-added' => ['ok', 'Your payout method was saved securely.'],
        'payout-method-removed' => ['info', 'Payout method removed.'],
        'withdrawal-requested' => ['ok', 'Withdrawal request sent to the admin team.'],
        'withdrawal-updated' => ['ok', 'Withdrawal status updated.'],
    ];
@endphp

@if ($status && isset($map[$status]))
    @php
        [$tone, $message] = $map[$status];
        $styles = [
            'ok' => ['bg-emerald-50 border-emerald-200 text-emerald-800', 'text-emerald-500'],
            'info' => ['bg-blue-50 border-blue-200 text-blue-800', 'text-blue-500'],
            'warn' => ['bg-amber-50 border-amber-200 text-amber-800', 'text-amber-500'],
        ][$tone];
    @endphp
    <div x-data="{ show: true }" x-show="show" x-transition
        class="mb-5 flex items-start gap-3 rounded-xl border p-4 text-sm animate-fade-up {{ $styles[0] }}">
        <svg class="h-5 w-5 shrink-0 {{ $styles[1] }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12.75 2.25 2.25 4.5-4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        <span class="flex-1 font-medium">{{ $message }}</span>
        <button type="button" @click="show = false" class="shrink-0 opacity-60 hover:opacity-100">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>
@endif
