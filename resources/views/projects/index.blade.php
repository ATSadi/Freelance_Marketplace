<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Projects') }}
            </h2>
            <a href="{{ route('client.projects.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('Post New Project') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status') === 'project-deleted')
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                    {{ __('Project deleted successfully.') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($projects->isEmpty())
                        <p class="text-gray-600">{{ __('No projects yet.') }}</p>
                        <a href="{{ route('client.projects.create') }}"
                            class="mt-4 inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            {{ __('Post your first project') }} &rarr;
                        </a>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Title') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Budget') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Deadline') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($projects as $project)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                                    {{ $project->title }}
                                                </a>
                                                <p class="text-xs text-gray-500">{{ $project->category }}</p>
                                            </td>
                                            <td class="px-4 py-3 text-sm">{{ $project->budgetRange() }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $project->deadline->format('M j, Y') }}</td>
                                            <td class="px-4 py-3">
                                                <x-project-status-badge :status="$project->status" />
                                            </td>
                                            <td class="px-4 py-3 text-sm space-x-2">
                                                <a href="{{ route('client.projects.edit', $project) }}" class="text-gray-600 hover:text-gray-900">{{ __('Edit') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
