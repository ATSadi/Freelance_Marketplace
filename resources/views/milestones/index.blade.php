<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Milestones</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">{{ $project->title }}</h2>
            </div>
            <a href="{{ route('projects.show', $project) }}" class="btn-secondary">View Project</a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-flash-status />

        <div class="wv-card">
            <div class="p-6">
                @include('milestones._list', [
                    'project' => $project,
                    'canManage' => Auth::id() === $project->client_id && $project->status === \App\Models\Project::STATUS_IN_PROGRESS,
                ])
            </div>
        </div>
    </div>
</x-app-layout>
