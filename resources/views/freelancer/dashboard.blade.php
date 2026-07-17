<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Freelancer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-profile-dashboard-card :user="$user" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ __('Your Proposals') }}</h3>
                    <p class="mt-1 text-3xl font-bold text-indigo-600">{{ $proposalCount }}</p>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Total proposals submitted') }}</p>

                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('freelancer.projects.browse') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                            {{ __('Browse Projects') }}
                        </a>
                        <a href="{{ route('freelancer.proposals.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            {{ __('My Proposals') }}
                        </a>
                        <a href="{{ route('transactions.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                            {{ __('Transactions') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ __('Active Projects') }}</h3>

                    @if ($activeProjects->isEmpty())
                        <p class="mt-2 text-sm text-gray-600">{{ __('No active projects yet. Win a proposal to get started.') }}</p>
                    @else
                        <ul class="mt-4 divide-y divide-gray-200">
                            @foreach ($activeProjects as $project)
                                <li class="py-3 flex flex-wrap justify-between items-center gap-2">
                                    <div>
                                        <a href="{{ route('projects.show', $project) }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                                            {{ $project->title }}
                                        </a>
                                        <p class="text-xs text-gray-500">{{ __('Client:') }} {{ $project->client->name }}</p>
                                    </div>
                                    <x-project-status-badge :status="$project->status" />
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
