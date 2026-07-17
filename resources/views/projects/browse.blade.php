<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Browse Projects') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('freelancer.projects.browse') }}" class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div class="lg:col-span-2">
                        <x-input-label for="q" :value="__('Search')" />
                        <x-text-input id="q" name="q" type="text" class="mt-1 block w-full" :value="$filters['q'] ?? ''" placeholder="{{ __('Title, skills, keywords…') }}" />
                    </div>
                    <div>
                        <x-input-label for="category" :value="__('Category')" />
                        <select id="category" name="category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="budget_min" :value="__('Min budget')" />
                        <x-text-input id="budget_min" name="budget_min" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="$filters['budget_min'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label for="budget_max" :value="__('Max budget')" />
                        <x-text-input id="budget_max" name="budget_max" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="$filters['budget_max'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label for="sort" :value="__('Sort')" />
                        <select id="sort" name="sort" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>{{ __('Newest') }}</option>
                            <option value="deadline" @selected(($filters['sort'] ?? '') === 'deadline')>{{ __('Deadline') }}</option>
                            <option value="budget_high" @selected(($filters['sort'] ?? '') === 'budget_high')>{{ __('Budget: high to low') }}</option>
                            <option value="budget_low" @selected(($filters['sort'] ?? '') === 'budget_low')>{{ __('Budget: low to high') }}</option>
                        </select>
                    </div>
                    <div class="lg:col-span-6 flex flex-wrap gap-3">
                        <x-primary-button>{{ __('Apply filters') }}</x-primary-button>
                        <a href="{{ route('freelancer.projects.browse') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($projects->isEmpty())
                        <p class="text-gray-600">{{ __('No open projects match your filters.') }}</p>
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

                        <div class="mt-6">
                            {{ $projects->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
