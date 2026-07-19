<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Project</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">{{ $project->title }}</h2>
            </div>
            @if (Auth::id() === $project->client_id)
                <a href="{{ route('client.projects.edit', $project) }}" class="btn-secondary">Edit Project</a>
            @endif
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-flash-status />

            <div class="wv-card">
                <div class="p-6 text-slate-800 space-y-6">
                    <div class="flex flex-wrap items-center gap-3">
                        <x-project-status-badge :status="$project->status" />
                        @if ($project->category)
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ $project->category }}</span>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Description') }}</h3>
                        <p class="mt-1 whitespace-pre-line text-slate-700">{{ $project->description }}</p>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs uppercase tracking-wide text-slate-400">{{ __('Budget') }}</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $project->budgetRange() }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs uppercase tracking-wide text-slate-400">{{ __('Deadline') }}</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $project->deadline->format('M j, Y') }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs uppercase tracking-wide text-slate-400">{{ __('Posted') }}</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $project->created_at->format('M j, Y') }}</dd>
                        </div>
                    </dl>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Client') }}</h3>
                            <p class="mt-1 font-semibold text-slate-900">{{ $project->client->name }}</p>
                            @if ($project->client->profile?->company_name)
                                <p class="text-sm text-slate-500">{{ $project->client->profile->company_name }}</p>
                            @endif
                        </div>
                        @if ($project->freelancer)
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Assigned Freelancer') }}</h3>
                                <p class="mt-1 font-semibold text-slate-900">{{ $project->freelancer->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Milestones: visible to owning client and assigned freelancer --}}
            @if (Auth::id() === $project->client_id || Auth::id() === $project->freelancer_id)
                <div class="mt-6 wv-card">
                    <div class="p-6 text-slate-800">
                        @include('milestones._list', [
                            'project' => $project,
                            'canManage' => Auth::id() === $project->client_id && $project->status === \App\Models\Project::STATUS_IN_PROGRESS,
                        ])
                    </div>
                </div>
            @endif

            {{-- Client view: proposals received --}}
            @if (Auth::id() === $project->client_id)
                <div class="mt-6 wv-card">
                    <div class="p-6 text-slate-800">
                        <h3 class="text-lg font-display font-bold text-slate-900">
                            {{ __('Proposals Received') }} ({{ $receivedProposals->count() }})
                        </h3>

                        @if ($receivedProposals->isEmpty())
                            <p class="mt-2 text-sm text-slate-500">{{ __('No proposals yet.') }}</p>
                        @else
                            <div class="mt-4 space-y-4">
                                @foreach ($receivedProposals as $proposal)
                                    <div class="rounded-xl border border-slate-200 p-4 hover:border-brand-200 transition">
                                        <div class="flex flex-wrap justify-between items-start gap-2">
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $proposal->freelancer->name }}</p>
                                                @if ($proposal->freelancer->profile?->skills)
                                                    <p class="text-xs text-slate-500">{{ $proposal->freelancer->profile->skills }}</p>
                                                @endif
                                            </div>
                                            <x-proposal-status-badge :status="$proposal->status" />
                                        </div>

                                        <p class="mt-3 text-sm text-slate-600 whitespace-pre-line">{{ $proposal->cover_letter }}</p>

                                        <div class="mt-3 flex flex-wrap gap-4 text-sm text-slate-500">
                                            <span><span class="font-medium">{{ __('Amount:') }}</span> ${{ number_format($proposal->proposed_amount, 2) }}</span>
                                            <span><span class="font-medium">{{ __('Duration:') }}</span> {{ $proposal->proposed_duration_days }} {{ __('days') }}</span>
                                        </div>

                                        @if ($project->status === \App\Models\Project::STATUS_OPEN && $proposal->status === \App\Models\Proposal::STATUS_PENDING)
                                            <div class="mt-4 flex gap-3">
                                                <form method="POST" action="{{ route('client.proposals.accept', $proposal) }}">
                                                    @csrf
                                                    <x-primary-button>{{ __('Accept') }}</x-primary-button>
                                                </form>
                                                <form method="POST" action="{{ route('client.proposals.reject', $proposal) }}">
                                                    @csrf
                                                    <x-danger-button>{{ __('Reject') }}</x-danger-button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if (Auth::user()->role === \App\Models\User::ROLE_FREELANCER)
                <div class="mt-6 wv-card">
                    <div class="p-6 text-slate-800">
                        @if ($existingProposal)
                            <h3 class="text-lg font-display font-bold text-slate-900">{{ __('Your Proposal') }}</h3>
                            <p class="mt-2 text-sm text-slate-500">
                                {{ __('You already submitted a proposal for this project.') }}
                            </p>
                            <div class="mt-3 flex items-center gap-3">
                                <x-proposal-status-badge :status="$existingProposal->status" />
                                <span class="text-sm text-slate-500">
                                    ${{ number_format($existingProposal->proposed_amount, 2) }}
                                    &middot;
                                    {{ $existingProposal->proposed_duration_days }} {{ __('days') }}
                                </span>
                            </div>
                            <a href="{{ route('freelancer.proposals.index') }}"
                                class="mt-3 inline-block text-sm font-medium text-brand-600 hover:text-brand-800">
                                {{ __('View all my proposals') }}
                            </a>
                        @elseif ($canSubmitProposal)
                            @include('projects.partials.proposal-form', ['project' => $project])
                        @elseif ($project->status !== \App\Models\Project::STATUS_OPEN)
                            <p class="text-sm text-slate-500">{{ __('This project is no longer accepting proposals.') }}</p>
                        @endif
                    </div>
                </div>
            @endif

            @if ($project->status === \App\Models\Project::STATUS_COMPLETED && $project->freelancer)
                <section class="mt-6 wv-card p-6">
                    <h3 class="font-display text-lg font-bold text-slate-900">Project reviews</h3>
                    <div class="mt-4 space-y-3">
                        @foreach ($project->reviews as $review)
                            <div class="rounded-xl bg-slate-50 p-4">
                                <div class="flex justify-between gap-3"><p class="font-semibold">{{ $review->reviewer->name }}</p><p class="text-amber-500">{{ str_repeat('★', $review->rating) }}<span class="text-slate-300">{{ str_repeat('★', 5 - $review->rating) }}</span></p></div>
                                <p class="mt-2 text-sm text-slate-600">{{ $review->comment }}</p>
                            </div>
                        @endforeach
                    </div>

                    @if (in_array(Auth::id(), [$project->client_id, $project->freelancer_id], true) && ! $project->reviews->contains('reviewer_id', Auth::id()))
                        <form method="POST" action="{{ route('reviews.store', $project) }}" class="mt-5 space-y-3 border-t border-slate-100 pt-5">
                            @csrf
                            <div>
                                <x-input-label for="rating" value="Rating" />
                                <select id="rating" name="rating" class="wv-input">
                                    @foreach ([5, 4, 3, 2, 1] as $rating)<option value="{{ $rating }}">{{ $rating }} stars</option>@endforeach
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

            @if (
                $project->status === \App\Models\Project::STATUS_IN_PROGRESS
                && (Auth::id() === $project->client_id || Auth::id() === $project->freelancer_id)
            )
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('disputes.create', $project) }}"
                        class="btn inline-flex bg-white text-rose-700 border border-rose-200 hover:bg-rose-50 hover:border-rose-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                        {{ __('Open Dispute') }}
                    </a>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ match(Auth::user()->role) {
                    \App\Models\User::ROLE_CLIENT => route('client.projects.index'),
                    \App\Models\User::ROLE_FREELANCER => route('freelancer.projects.browse'),
                    default => Auth::user()->dashboardRoute(),
                } }}"
                    class="inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-brand-700">&larr; {{ __('Back') }}</a>
            </div>
    </div>
</x-app-layout>
