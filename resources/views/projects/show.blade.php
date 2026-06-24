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
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ Auth::user()->role === \App\Models\User::ROLE_CLIENT ? route('client.projects.index') : Auth::user()->dashboardRoute() }}"
                    class="text-sm text-gray-600 hover:text-gray-900">&larr; {{ __('Back') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>
