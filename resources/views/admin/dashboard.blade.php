<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500">{{ __('Users') }}</p>
                    <p class="mt-1 text-2xl font-semibold">{{ $userCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $clientCount }} {{ __('clients') }} / {{ $freelancerCount }} {{ __('freelancers') }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500">{{ __('Projects') }}</p>
                    <p class="mt-1 text-2xl font-semibold">{{ $projectCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $activeProjects }} {{ __('active') }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500">{{ __('Open disputes') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-red-600">{{ $openDisputes }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500">{{ __('Escrow released') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600">${{ number_format($totalEscrowReleased, 2) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium">{{ __('Recent disputes') }}</h3>
                            <a href="{{ route('admin.disputes.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('View all') }}</a>
                        </div>
                        @if ($recentDisputes->isEmpty())
                            <p class="mt-3 text-sm text-gray-600">{{ __('No disputes yet.') }}</p>
                        @else
                            <ul class="mt-4 divide-y divide-gray-100">
                                @foreach ($recentDisputes as $dispute)
                                    <li class="py-3 flex justify-between gap-2">
                                        <div>
                                            <a href="{{ route('disputes.show', $dispute) }}" class="font-medium text-indigo-600 hover:text-indigo-800">{{ $dispute->reason }}</a>
                                            <p class="text-xs text-gray-500">{{ $dispute->project->title }}</p>
                                        </div>
                                        <x-dispute-status-badge :status="$dispute->status" />
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">{{ __('Recent projects') }}</h3>
                        @if ($recentProjects->isEmpty())
                            <p class="mt-3 text-sm text-gray-600">{{ __('No projects yet.') }}</p>
                        @else
                            <ul class="mt-4 divide-y divide-gray-100">
                                @foreach ($recentProjects as $project)
                                    <li class="py-3 flex justify-between gap-2">
                                        <div>
                                            <a href="{{ route('projects.show', $project) }}" class="font-medium text-indigo-600 hover:text-indigo-800">{{ $project->title }}</a>
                                            <p class="text-xs text-gray-500">{{ $project->client->name }}</p>
                                        </div>
                                        <x-project-status-badge :status="$project->status" />
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium">{{ __('Pipeline') }}</h3>
                <dl class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Open') }}</dt>
                        <dd class="mt-1 text-xl font-semibold">{{ $openProjects }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('In progress') }}</dt>
                        <dd class="mt-1 text-xl font-semibold">{{ $activeProjects }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Completed') }}</dt>
                        <dd class="mt-1 text-xl font-semibold">{{ $completedProjects }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
