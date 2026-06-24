<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Browse Projects') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($projects->isEmpty())
                        <p class="text-gray-600">{{ __('No open projects available right now.') }}</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($projects as $project)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition">
                                    <div class="flex justify-between items-start gap-2">
                                        <a href="{{ route('projects.show', $project) }}"
                                            class="text-lg font-semibold text-indigo-600 hover:text-indigo-800">
                                            {{ $project->title }}
                                        </a>
                                        <x-project-status-badge :status="$project->status" />
                                    </div>

                                    <p class="mt-2 text-sm text-gray-500">{{ $project->category }}</p>
                                    <p class="mt-2 text-sm text-gray-700 line-clamp-2">{{ Str::limit($project->description, 120) }}</p>

                                    <dl class="mt-4 grid grid-cols-2 gap-2 text-sm">
                                        <div>
                                            <dt class="text-gray-500">{{ __('Budget') }}</dt>
                                            <dd class="font-medium">{{ $project->budgetRange() }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-gray-500">{{ __('Deadline') }}</dt>
                                            <dd class="font-medium">{{ $project->deadline->format('M j, Y') }}</dd>
                                        </div>
                                    </dl>

                                    <a href="{{ route('projects.show', $project) }}"
                                        class="mt-4 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                        {{ __('View & Submit Proposal') }} &rarr;
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
