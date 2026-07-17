<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Milestones') }} &mdash; {{ $project->title }}
            </h2>
            <a href="{{ route('projects.show', $project) }}"
                class="text-sm text-gray-600 hover:text-gray-900">{{ __('View Project') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('status') === 'milestone-created')
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                    {{ __('Milestone created successfully.') }}
                </div>
            @endif
            @if (session('status') === 'milestone-updated')
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                    {{ __('Milestone updated successfully.') }}
                </div>
            @endif
            @if (session('status') === 'milestone-deleted')
                <div class="mb-4 rounded-md bg-yellow-50 border border-yellow-200 p-4 text-sm text-yellow-800">
                    {{ __('Milestone deleted.') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @include('milestones._list', [
                        'project' => $project,
                        'canManage' => Auth::id() === $project->client_id && $project->status === \App\Models\Project::STATUS_IN_PROGRESS,
                    ])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
