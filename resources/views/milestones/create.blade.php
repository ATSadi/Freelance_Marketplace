<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Milestone') }} &mdash; {{ $project->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('client.projects.milestones.store', $project) }}">
                        @csrf
                        @include('milestones._form', ['project' => $project])

                        <div class="mt-6 flex items-center gap-4">
                            <x-primary-button>{{ __('Add Milestone') }}</x-primary-button>
                            <a href="{{ route('client.projects.milestones.index', $project) }}"
                                class="text-sm text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
