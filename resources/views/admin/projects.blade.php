<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Administration</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Projects</h2>
            <p class="mt-1 text-sm text-slate-500">Inspect marketplace work and moderate project status.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
        <form class="wv-card grid gap-3 p-4 sm:grid-cols-[1fr_12rem_auto]" method="GET">
            <input class="wv-input" name="q" value="{{ request('q') }}" placeholder="Search projects">
            <select class="wv-input" name="status">
                <option value="">All statuses</option>
                @foreach (['open', 'in_progress', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <x-primary-button>Filter</x-primary-button>
        </form>

        <div class="grid gap-4">
            @foreach ($projects as $project)
                <article class="wv-card p-5">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <a href="{{ route('projects.show', $project) }}" class="font-display text-lg font-bold text-slate-900 hover:text-brand-700">{{ $project->title }}</a>
                            <p class="mt-1 text-sm text-slate-500">{{ $project->client->name }} · {{ $project->category }} · {{ $project->budgetRange() }}</p>
                            <p class="mt-2 text-xs text-slate-400">{{ $project->proposals_count }} proposals · {{ $project->milestones_count }} milestones · {{ $project->disputes_count }} disputes</p>
                        </div>
                        <form method="POST" action="{{ route('admin.projects.update', $project) }}" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <select name="status" class="wv-input py-2 text-sm">
                                @foreach (['open', 'in_progress', 'completed', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected($project->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                            <x-primary-button>Save</x-primary-button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
        {{ $projects->links() }}
    </div>
</x-app-layout>
