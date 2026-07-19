<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Milestone</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Edit Milestone</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $project->title }}</p>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="wv-card">
            <div class="p-6 sm:p-8">
                <form method="POST" action="{{ route('client.projects.milestones.update', [$project, $milestone]) }}">
                    @csrf
                    @method('PUT')
                    @include('milestones._form', ['project' => $project, 'milestone' => $milestone])

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>Save Changes</x-primary-button>
                        <a href="{{ route('client.projects.milestones.index', $project) }}"
                            class="text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</a>
                    </div>
                </form>

                <form method="POST" action="{{ route('client.projects.milestones.destroy', [$project, $milestone]) }}"
                    class="mt-6 pt-6 border-t border-slate-200"
                    onsubmit="return confirm('{{ __('Delete this milestone?') }}');">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>Delete Milestone</x-danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
