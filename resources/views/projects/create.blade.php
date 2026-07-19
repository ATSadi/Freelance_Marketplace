<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> New project</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Post a Project</h2>
            <p class="mt-1 text-sm text-slate-500">Describe the work, set a budget range, and a deadline.</p>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="wv-card">
            <div class="p-6 sm:p-8">
                <form method="POST" action="{{ route('client.projects.store') }}">
                    @csrf
                    @include('projects._form')

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>Post Project</x-primary-button>
                        <a href="{{ route('client.projects.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
