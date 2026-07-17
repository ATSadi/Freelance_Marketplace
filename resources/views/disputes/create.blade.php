<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Open a Dispute') }} &mdash; {{ $project->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-sm text-gray-600 mb-6">
                        {{ __('Disputes are reviewed by platform admins. Provide clear details so we can help resolve the issue.') }}
                    </p>

                    <form method="POST" action="{{ route('disputes.store', $project) }}">
                        @csrf

                        <div>
                            <x-input-label for="reason" :value="__('Reason')" />
                            <select id="reason" name="reason" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled {{ old('reason') ? '' : 'selected' }}>{{ __('Select a reason') }}</option>
                                @foreach ([
                                    'Work quality concerns',
                                    'Missed deadline',
                                    'Payment / escrow issue',
                                    'Communication breakdown',
                                    'Scope disagreement',
                                    'Other',
                                ] as $reason)
                                    <option value="{{ $reason }}" @selected(old('reason') === $reason)>{{ $reason }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="milestone_id" :value="__('Related milestone (optional)')" />
                            <select id="milestone_id" name="milestone_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('None') }}</option>
                                @foreach ($project->milestones as $milestone)
                                    <option value="{{ $milestone->id }}" @selected((string) old('milestone_id') === (string) $milestone->id)>
                                        {{ $milestone->title }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('milestone_id')" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Describe the issue')" />
                            <textarea id="description" name="description" rows="5"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required>{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <x-primary-button>{{ __('Submit Dispute') }}</x-primary-button>
                            <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
