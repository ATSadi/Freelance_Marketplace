<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Shortlist</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Saved Projects</h2>
        </div>
    </x-slot>

    <div class="mx-auto grid max-w-7xl gap-5 px-4 sm:grid-cols-2 sm:px-6 lg:px-8">
        @forelse ($projects as $project)
            <article class="wv-card wv-card-hover p-6">
                <div class="flex items-start justify-between gap-3">
                    <a href="{{ route('projects.show', $project) }}" class="font-display text-lg font-bold text-slate-900 hover:text-brand-700">{{ $project->title }}</a>
                    <x-project-status-badge :status="$project->status" />
                </div>
                <p class="mt-3 line-clamp-2 text-sm text-slate-500">{{ $project->description }}</p>
                <p class="mt-4 font-semibold text-slate-900">{{ $project->budgetRange() }}</p>
                <form method="POST" action="{{ route('saved-projects.toggle', $project) }}" class="mt-4">
                    @csrf
                    <button class="text-sm font-semibold text-rose-600">Remove from saved</button>
                </form>
            </article>
        @empty
            <div class="wv-card p-12 text-center sm:col-span-2">
                <p class="font-medium text-slate-700">No saved projects yet</p>
                <a href="{{ route('freelancer.projects.browse') }}" class="mt-3 btn-primary">Browse projects</a>
            </div>
        @endforelse
    </div>
</x-app-layout>
