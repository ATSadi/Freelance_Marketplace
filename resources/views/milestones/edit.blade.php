<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Milestone') }} &mdash; {{ $project->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('client.projects.milestones.update', [$project, $milestone]) }}">
                        @csrf
                        @method('PUT')
                        @include('milestones._form', ['project' => $project, 'milestone' => $milestone])

                        <div class="mt-6 flex items-center gap-4">
                            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                            <a href="{{ route('client.projects.milestones.index', $project) }}"
                                class="text-sm text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('client.projects.milestones.destroy', [$project, $milestone]) }}"
                        class="mt-6 pt-6 border-t border-gray-200"
                        onsubmit="return confirm('{{ __('Delete this milestone?') }}');">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>{{ __('Delete Milestone') }}</x-danger-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
