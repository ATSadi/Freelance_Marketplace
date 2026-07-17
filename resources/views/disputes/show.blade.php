<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dispute') }}: {{ $dispute->reason }}
            </h2>
            <x-dispute-status-badge :status="$dispute->status" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status') === 'dispute-under-review')
                <div class="rounded-md bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800">
                    {{ __('Dispute marked as under review.') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Project') }}</p>
                        <a href="{{ route('projects.show', $dispute->project) }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                            {{ $dispute->project->title }}
                        </a>
                    </div>

                    @if ($dispute->milestone)
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Milestone') }}</p>
                            <p class="font-medium">{{ $dispute->milestone->title }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">{{ __('Opened by') }}</p>
                            <p class="font-medium">{{ $dispute->opener->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">{{ __('Against') }}</p>
                            <p class="font-medium">{{ $dispute->againstUser->name }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">{{ __('Description') }}</p>
                        <p class="mt-1 whitespace-pre-line">{{ $dispute->description }}</p>
                    </div>

                    @if ($dispute->admin_notes)
                        <div class="rounded-md bg-gray-50 border border-gray-200 p-4">
                            <p class="text-sm font-medium text-gray-700">{{ __('Admin decision') }}</p>
                            <p class="mt-1 text-sm whitespace-pre-line">{{ $dispute->admin_notes }}</p>
                            @if ($dispute->resolver)
                                <p class="mt-2 text-xs text-gray-500">
                                    {{ $dispute->resolver->name }} &middot; {{ $dispute->resolved_at?->format('M j, Y g:i A') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @can('moderate', $dispute)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">
                        <h3 class="text-lg font-medium">{{ __('Admin actions') }}</h3>

                        @if ($dispute->status === \App\Models\Dispute::STATUS_OPEN)
                            <form method="POST" action="{{ route('admin.disputes.review', $dispute) }}">
                                @csrf
                                <x-secondary-button>{{ __('Mark Under Review') }}</x-secondary-button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.disputes.resolve', $dispute) }}" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="status" :value="__('Resolution')" />
                                <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="resolved">{{ __('Resolve in favor / close') }}</option>
                                    <option value="dismissed">{{ __('Dismiss dispute') }}</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="admin_notes" :value="__('Admin notes')" />
                                <textarea id="admin_notes" name="admin_notes" rows="4"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>{{ old('admin_notes') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('admin_notes')" />
                            </div>
                            <x-primary-button>{{ __('Submit Decision') }}</x-primary-button>
                        </form>
                    </div>
                </div>
            @endcan

            <a href="{{ Auth::user()->role === \App\Models\User::ROLE_ADMIN ? route('admin.disputes.index') : route('disputes.index') }}"
                class="text-sm text-gray-600 hover:text-gray-900">&larr; {{ __('Back to disputes') }}</a>
        </div>
    </div>
</x-app-layout>
