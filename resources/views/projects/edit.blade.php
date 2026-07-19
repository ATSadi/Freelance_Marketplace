<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Edit</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Edit Project</h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="wv-card">
            <div class="p-6 sm:p-8">
                <form method="POST" action="{{ route('client.projects.update', $project) }}">
                    @csrf
                    @method('PUT')
                    @include('projects._form', ['project' => $project])

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>Save Changes</x-primary-button>
                        <a href="{{ route('projects.show', $project) }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</a>
                    </div>
                </form>

                <form method="POST" action="{{ route('client.projects.destroy', $project) }}" class="mt-6 pt-6 border-t border-slate-200"
                    onsubmit="return confirm('{{ __('Are you sure you want to delete this project?') }}');">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>Delete Project</x-danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
