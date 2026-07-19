<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Order #WV-{{ str_pad($project->id, 5, '0', STR_PAD_LEFT) }}</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">{{ $project->title }}</h2>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('messages.show', $project) }}" class="btn-secondary">Message {{ $user->role === \App\Models\User::ROLE_CLIENT ? 'Freelancer' : 'Client' }}</a>
                @if ($project->status === \App\Models\Project::STATUS_IN_PROGRESS)
                    <a href="{{ route('disputes.create', $project) }}" class="btn border border-rose-200 bg-white text-rose-700 hover:bg-rose-50">Open Dispute</a>
                    <form method="POST" action="{{ route('orders.cancel', $project) }}" onsubmit="return confirm('Cancel this order and refund any unreleased escrow holds?')">
                        @csrf
                        <button type="submit" class="btn border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">Cancel Order</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <x-flash-status />

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="stat-card">
                <p class="eyebrow">Order status</p>
                <div class="mt-3"><x-project-status-badge :status="$project->status" /></div>
            </div>
            <div class="stat-card">
                <p class="eyebrow">Contract value</p>
                <p class="mt-2 font-display text-2xl font-bold text-slate-900">${{ number_format($project->acceptedProposal->first()?->proposed_amount ?? $project->milestones->sum('amount'), 2) }}</p>
            </div>
            <div class="stat-card">
                <p class="eyebrow">Funded / released</p>
                <p class="mt-2 font-display text-2xl font-bold text-slate-900">${{ number_format($funded, 2) }} <span class="text-sm font-medium text-slate-400">/ ${{ number_format($released, 2) }}</span></p>
            </div>
            <div class="stat-card">
                <p class="eyebrow">Final deadline</p>
                <p class="mt-2 font-display text-2xl font-bold text-slate-900">{{ $project->deadline->format('M j, Y') }}</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="space-y-6">
                <section class="wv-card p-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="eyebrow">Delivery progress</p>
                            <h3 class="mt-2 font-display text-xl font-bold text-slate-900">{{ $progress }}% complete</h3>
                        </div>
                        <p class="text-sm text-slate-500">{{ $project->milestones->filter->isCompleted()->count() }} / {{ $project->milestones->count() }} milestones</p>
                    </div>
                    <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-brand-gradient" style="width: {{ $progress }}%"></div>
                    </div>
                </section>

                <section class="wv-card p-6">
                    @include('milestones._list', [
                        'project' => $project,
                        'canManage' => $user->id === $project->client_id && $project->status === \App\Models\Project::STATUS_IN_PROGRESS,
                    ])
                </section>

                @if ($project->disputes->isNotEmpty())
                    <section class="wv-card p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="font-display text-lg font-bold text-slate-900">Disputes for this order</h3>
                            <a href="{{ route('disputes.index') }}" class="text-sm font-semibold text-brand-600">View all</a>
                        </div>
                        <div class="mt-4 space-y-3">
                            @foreach ($project->disputes as $dispute)
                                <a href="{{ route('disputes.show', $dispute) }}" class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/30">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $dispute->reason }}</p>
                                        <p class="mt-1 text-xs text-slate-500">Opened {{ $dispute->created_at->diffForHumans() }}</p>
                                    </div>
                                    <x-dispute-status-badge :status="$dispute->status" />
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($project->status === \App\Models\Project::STATUS_COMPLETED)
                    <section class="wv-card p-6">
                        <h3 class="font-display text-lg font-bold text-slate-900">Order reviews</h3>
                        <div class="mt-4 space-y-3">
                            @forelse ($project->reviews as $review)
                                <div class="rounded-xl bg-slate-50 p-4">
                                    <div class="flex justify-between gap-3">
                                        <p class="font-semibold">{{ $review->reviewer->name }}</p>
                                        <p class="text-amber-500">{{ str_repeat('★', $review->rating) }}<span class="text-slate-300">{{ str_repeat('★', 5 - $review->rating) }}</span></p>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-600">{{ $review->comment }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">No reviews yet for this completed order.</p>
                            @endforelse
                        </div>

                        @if (! $project->reviews->contains('reviewer_id', $user->id))
                            <form method="POST" action="{{ route('reviews.store', $project) }}" class="mt-5 space-y-3 border-t border-slate-100 pt-5">
                                @csrf
                                <div>
                                    <x-input-label for="rating" value="Rating" />
                                    <select id="rating" name="rating" class="wv-input">
                                        @foreach ([5, 4, 3, 2, 1] as $rating)
                                            <option value="{{ $rating }}">{{ $rating }} stars</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="comment" value="Review" />
                                    <textarea id="comment" name="comment" rows="3" class="wv-input" required placeholder="Share your experience…"></textarea>
                                </div>
                                <x-primary-button>Publish review</x-primary-button>
                            </form>
                        @endif
                    </section>
                @endif
            </div>

            <aside class="space-y-5">
                @php $partner = $user->role === \App\Models\User::ROLE_CLIENT ? $project->freelancer : $project->client; @endphp
                <section class="wv-card p-5">
                    <p class="eyebrow">{{ $user->role === \App\Models\User::ROLE_CLIENT ? 'Freelancer' : 'Client' }}</p>
                    <div class="mt-4 flex items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-gradient font-display text-lg font-bold text-white">{{ strtoupper(substr($partner->name, 0, 1)) }}</span>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $partner->name }}</p>
                            <p class="text-xs text-slate-500">{{ $partner->profile?->company_name ?: $partner->profile?->skills }}</p>
                        </div>
                    </div>
                    <a href="{{ route('messages.show', $project) }}" class="mt-4 btn-secondary w-full">Open Conversation</a>
                </section>

                <section class="wv-card p-5">
                    <p class="eyebrow">Contract details</p>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Started</dt><dd class="font-semibold text-slate-800">{{ $project->updated_at->format('M j, Y') }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Category</dt><dd class="font-semibold text-slate-800">{{ $project->category }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Milestones</dt><dd class="font-semibold text-slate-800">{{ $project->milestones->count() }}</dd></div>
                    </dl>
                    <a href="{{ route('projects.show', $project) }}" class="mt-4 inline-flex text-sm font-semibold text-brand-600 hover:text-brand-800">View original project brief →</a>
                </section>
            </aside>
        </div>

        <a href="{{ route('orders.index') }}" class="inline-flex text-sm font-semibold text-slate-500 hover:text-brand-700">← Back to orders</a>
    </div>
</x-app-layout>
