<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> {{ $user->role === \App\Models\User::ROLE_ADMIN ? 'Moderation' : 'Support' }}</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">
                {{ $user->role === \App\Models\User::ROLE_ADMIN ? __('Dispute Moderation') : __('My Disputes') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-flash-status />

        <div class="wv-card">
            <div class="p-6">
                @if ($disputes->isEmpty())
                    <div class="text-center py-10">
                        <div class="mx-auto h-12 w-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12.75 2.25 2.25 4.5-4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </div>
                        <p class="mt-4 text-sm text-slate-500">No disputes found.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($disputes as $dispute)
                            <div class="rounded-xl border border-slate-200 p-4 hover:border-brand-200 transition">
                                <div class="flex flex-wrap justify-between items-start gap-2">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $dispute->reason }}</p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $dispute->project->title }} &middot; Opened by {{ $dispute->opener->name }}
                                        </p>
                                    </div>
                                    <x-dispute-status-badge :status="$dispute->status" />
                                </div>
                                <div class="mt-3 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-3">
                                    <p class="text-xs text-slate-400">{{ $dispute->created_at->format('M j, Y g:i A') }}</p>
                                    <a href="{{ route('disputes.show', $dispute) }}" class="btn-primary !px-3 !py-2 text-xs">
                                        Open dispute details →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">{{ $disputes->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
