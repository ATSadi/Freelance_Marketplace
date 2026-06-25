<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Client Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-profile-dashboard-card :user="$user" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ __('Your Projects') }}</h3>
                    <p class="mt-1 text-3xl font-bold text-indigo-600">{{ $projectCount }}</p>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Total projects posted') }}</p>

                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('client.projects.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                            {{ __('My Projects') }}
                        </a>
                        <a href="{{ route('client.projects.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            {{ __('Post New Project') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ __('Active Projects') }}</h3>

                    @if ($activeProjects->isEmpty())
                        <p class="mt-2 text-sm text-gray-600">{{ __('No active projects right now.') }}</p>
                    @else
                        <ul class="mt-4 divide-y divide-gray-200">
                            @foreach ($activeProjects as $project)
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <a href="{{ route('projects.show', $project) }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                                            {{ $project->title }}
                                        </a>
                                        @if ($project->freelancer)
                                            <p class="text-xs text-gray-500">{{ __('Freelancer:') }} {{ $project->freelancer->name }}</p>
                                        @endif
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
