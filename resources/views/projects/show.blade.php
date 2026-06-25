<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $project->title }}
            </h2>
            @if (Auth::id() === $project->client_id)
                <a href="{{ route('client.projects.edit', $project) }}"
                    class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('Edit Project') }}</a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('status') === 'project-created')
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                    {{ __('Project posted successfully.') }}
                </div>
            @endif

            @if (session('status') === 'project-updated')
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                    {{ __('Project updated successfully.') }}
                </div>
            @endif

            @if (session('status') === 'proposal-accepted')
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                    {{ __('Proposal accepted! Project is now in progress.') }}
                </div>
            @endif

            @if (session('status') === 'proposal-rejected')
                <div class="mb-4 rounded-md bg-yellow-50 border border-yellow-200 p-4 text-sm text-yellow-800">
                    {{ __('Proposal rejected.') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div class="flex flex-wrap items-center gap-3">
                        <x-project-status-badge :status="$project->status" />
                        <span class="text-sm text-gray-500">{{ $project->category }}</span>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Description') }}</h3>
                        <p class="mt-1 whitespace-pre-line">{{ $project->description }}</p>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">{{ __('Budget') }}</dt>
                            <dd class="mt-1">{{ $project->budgetRange() }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">{{ __('Deadline') }}</dt>
                            <dd class="mt-1">{{ $project->deadline->format('M j, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">{{ __('Posted') }}</dt>
                            <dd class="mt-1">{{ $project->created_at->format('M j, Y') }}</dd>
                        </div>
                    </dl>

                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Client') }}</h3>
                        <p class="mt-1 font-medium">{{ $project->client->name }}</p>
                        @if ($project->client->profile?->company_name)
                            <p class="text-sm text-gray-600">{{ $project->client->profile->company_name }}</p>
                        @endif
                    </div>

                    @if ($project->freelancer)
                        <div class="pt-4 border-t border-gray-200">
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Assigned Freelancer') }}</h3>
                            <p class="mt-1 font-medium">{{ $project->freelancer->name }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Client view: proposals received --}}
            @if (Auth::id() === $project->client_id)
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium">
                            {{ __('Proposals Received') }} ({{ $receivedProposals->count() }})
                        </h3>

                        @if ($receivedProposals->isEmpty())
                            <p class="mt-2 text-sm text-gray-600">{{ __('No proposals yet.') }}</p>
                        @else
                            <div class="mt-4 space-y-4">
                                @foreach ($receivedProposals as $proposal)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex flex-wrap justify-between items-start gap-2">
                                            <div>
                                                <p class="font-medium">{{ $proposal->freelancer->name }}</p>
                                                @if ($proposal->freelancer->profile?->skills)
                                                    <p class="text-xs text-gray-500">{{ $proposal->freelancer->profile->skills }}</p>
                                                @endif
                                            </div>
                                            <x-proposal-status-badge :status="$proposal->status" />
                                        </div>

                                        <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $proposal->cover_letter }}</p>

                                        <div class="mt-3 flex flex-wrap gap-4 text-sm text-gray-600">
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
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        @if ($existingProposal)
                            <h3 class="text-lg font-medium">{{ __('Your Proposal') }}</h3>
                            <p class="mt-2 text-sm text-gray-600">
                                {{ __('You already submitted a proposal for this project.') }}
                            </p>
                            <div class="mt-3 flex items-center gap-3">
                                <x-proposal-status-badge :status="$existingProposal->status" />
                                <span class="text-sm text-gray-600">
                                    ${{ number_format($existingProposal->proposed_amount, 2) }}
                                    &middot;
                                    {{ $existingProposal->proposed_duration_days }} {{ __('days') }}
                                </span>
                            </div>
                            <a href="{{ route('freelancer.proposals.index') }}"
                                class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-800">
                                {{ __('View all my proposals') }}
                            </a>
                        @elseif ($canSubmitProposal)
                            @include('projects.partials.proposal-form', ['project' => $project])
                        @elseif ($project->status !== \App\Models\Project::STATUS_OPEN)
                            <p class="text-sm text-gray-600">{{ __('This project is no longer accepting proposals.') }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ match(Auth::user()->role) {
                    \App\Models\User::ROLE_CLIENT => route('client.projects.index'),
                    \App\Models\User::ROLE_FREELANCER => route('freelancer.projects.browse'),
                    default => Auth::user()->dashboardRoute(),
                } }}"
                    class="text-sm text-gray-600 hover:text-gray-900">&larr; {{ __('Back') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>
