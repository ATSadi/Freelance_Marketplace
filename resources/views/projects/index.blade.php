<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Your workspace</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">My Projects</h2>
            </div>
            <a href="{{ route('client.projects.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Post New Project
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('status') === 'project-deleted')
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800">Project deleted successfully.</div>
        @endif

        <div class="wv-card overflow-hidden">
            @if ($projects->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-slate-500">No projects yet.</p>
                    <a href="{{ route('client.projects.create') }}" class="mt-4 btn-primary">Post your first project</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Title</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Budget</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Deadline</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($projects as $project)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-5 py-4">
                                        <a href="{{ route('projects.show', $project) }}" class="font-semibold text-slate-900 hover:text-brand-700">{{ $project->title }}</a>
                                        <p class="text-xs text-slate-500">{{ $project->category }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-slate-700">{{ $project->budgetRange() }}</td>
                                    <td class="px-5 py-4 text-sm text-slate-700">{{ $project->deadline->format('M j, Y') }}</td>
                                    <td class="px-5 py-4"><x-project-status-badge :status="$project->status" /></td>
                                    <td class="px-5 py-4 text-right text-sm">
                                        <a href="{{ route('client.projects.edit', $project) }}" class="font-medium text-brand-600 hover:text-brand-800">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
