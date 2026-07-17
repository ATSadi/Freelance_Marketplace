<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $user->role === \App\Models\User::ROLE_ADMIN ? __('Dispute Moderation') : __('My Disputes') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('status') === 'dispute-opened')
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                    {{ __('Dispute opened. An admin will review it shortly.') }}
                </div>
            @endif
            @if (session('status') === 'dispute-resolved')
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                    {{ __('Dispute updated successfully.') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($disputes->isEmpty())
                        <p class="text-sm text-gray-600">{{ __('No disputes found.') }}</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($disputes as $dispute)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex flex-wrap justify-between items-start gap-2">
                                        <div>
                                            <a href="{{ route('disputes.show', $dispute) }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                                                {{ $dispute->reason }}
                                            </a>
                                            <p class="mt-1 text-sm text-gray-600">
                                                {{ $dispute->project->title }}
                                                &middot;
                                                {{ __('Opened by') }} {{ $dispute->opener->name }}
                                            </p>
                                        </div>
                                        <x-dispute-status-badge :status="$dispute->status" />
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">{{ $dispute->created_at->format('M j, Y g:i A') }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $disputes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
