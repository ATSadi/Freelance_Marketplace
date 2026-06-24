<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Proposals') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status') === 'proposal-submitted')
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                    {{ __('Proposal submitted successfully.') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($proposals->isEmpty())
                        <p class="text-gray-600">{{ __('You have not submitted any proposals yet.') }}</p>
                        <a href="{{ route('freelancer.projects.browse') }}"
                            class="mt-4 inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            {{ __('Browse open projects') }} &rarr;
                        </a>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Project') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Amount') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Duration') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Submitted') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($proposals as $proposal)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <a href="{{ route('projects.show', $proposal->project) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 font-medium">
                                                    {{ $proposal->project->title }}
                                                </a>
                                                <p class="text-xs text-gray-500">{{ $proposal->project->client->name }}</p>
                                            </td>
                                            <td class="px-4 py-3 text-sm">${{ number_format($proposal->proposed_amount, 2) }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $proposal->proposed_duration_days }} {{ __('days') }}</td>
                                            <td class="px-4 py-3">
                                                <x-proposal-status-badge :status="$proposal->status" />
                                            </td>
                                            <td class="px-4 py-3 text-sm">{{ $proposal->created_at->format('M j, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
