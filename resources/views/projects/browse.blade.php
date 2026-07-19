<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Marketplace</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Browse Projects</h2>
            <p class="mt-1 text-sm text-slate-500">Find work that matches your skills and budget.</p>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="wv-card p-5 sm:p-6">
            <form method="GET" action="{{ route('freelancer.projects.browse') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="lg:col-span-2">
                    <x-input-label for="q" :value="__('Search')" />
                    <x-text-input id="q" name="q" type="text" class="block w-full" :value="$filters['q'] ?? ''" placeholder="Title, skills, keywords…" />
                </div>
                <div>
                    <x-input-label for="category" :value="__('Category')" />
                    <select id="category" name="category" class="wv-input">
                        <option value="">All</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="budget_min" :value="__('Min budget')" />
                    <x-text-input id="budget_min" name="budget_min" type="number" min="0" step="0.01" class="block w-full" :value="$filters['budget_min'] ?? ''" />
                </div>
                <div>
                    <x-input-label for="budget_max" :value="__('Max budget')" />
                    <x-text-input id="budget_max" name="budget_max" type="number" min="0" step="0.01" class="block w-full" :value="$filters['budget_max'] ?? ''" />
                </div>
                <div>
                    <x-input-label for="sort" :value="__('Sort')" />
                    <select id="sort" name="sort" class="wv-input">
                        <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Newest</option>
                        <option value="deadline" @selected(($filters['sort'] ?? '') === 'deadline')>Deadline</option>
                        <option value="budget_high" @selected(($filters['sort'] ?? '') === 'budget_high')>Budget: high to low</option>
                        <option value="budget_low" @selected(($filters['sort'] ?? '') === 'budget_low')>Budget: low to high</option>
                    </select>
                </div>
                <div class="lg:col-span-6 flex flex-wrap gap-3">
                    <x-primary-button>Apply filters</x-primary-button>
                    <a href="{{ route('freelancer.projects.browse') }}" class="btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        @if ($projects->isEmpty())
            <div class="wv-card p-12 text-center">
                <div class="mx-auto h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </div>
                <p class="mt-4 text-slate-500">No open projects match your filters.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach ($projects as $project)
                    <div class="wv-card wv-card-hover p-6 flex flex-col animate-fade-up">
                        <div class="flex justify-between items-start gap-3">
                            <a href="{{ route('projects.show', $project) }}" class="text-lg font-display font-bold text-slate-900 hover:text-brand-700">{{ $project->title }}</a>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('saved-projects.toggle', $project) }}">
                                    @csrf
                                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-lg transition hover:border-brand-300 hover:text-brand-600" title="Save project">
                                        {{ $project->is_saved ? '♥' : '♡' }}
                                    </button>
                                </form>
                                <x-project-status-badge :status="$project->status" />
                            </div>
                        </div>

                        @if ($project->category)
                            <span class="mt-2 inline-flex w-fit items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ $project->category }}</span>
                        @endif
                        <p class="mt-3 text-sm text-slate-600 line-clamp-2">{{ Str::limit($project->description, 130) }}</p>

                        <dl class="mt-5 grid grid-cols-2 gap-3 text-sm border-t border-slate-100 pt-4">
                            <div>
                                <dt class="text-slate-400 text-xs uppercase tracking-wide">Budget</dt>
                                <dd class="font-semibold text-slate-900">{{ $project->budgetRange() }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 text-xs uppercase tracking-wide">Deadline</dt>
                                <dd class="font-semibold text-slate-900">{{ $project->deadline->format('M j, Y') }}</dd>
                            </div>
                        </dl>

                        <a href="{{ route('projects.show', $project) }}" class="mt-5 btn-primary w-full">
                            View &amp; Submit Proposal
                        </a>
                    </div>
                @endforeach
            </div>

            <div>{{ $projects->links() }}</div>
        @endif
    </div>
</x-app-layout>
