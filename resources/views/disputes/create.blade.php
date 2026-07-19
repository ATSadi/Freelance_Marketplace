<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Support</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Open a Dispute</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $project->title }}</p>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="wv-card">
            <div class="p-6 sm:p-8">
                <p class="text-sm text-slate-500 mb-6">
                    {{ __('Disputes are reviewed by platform admins. Provide clear details so we can help resolve the issue.') }}
                </p>

                    <form method="POST" action="{{ route('disputes.store', $project) }}">
                        @csrf

                        <div>
                            <x-input-label for="reason" :value="__('Reason')" />
                            <select id="reason" name="reason" class="wv-input" required>
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
                            <select id="milestone_id" name="milestone_id" class="wv-input">
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
                                class="wv-input"
                                required>{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <x-primary-button>Submit Dispute</x-primary-button>
                            <a href="{{ route('projects.show', $project) }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</a>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</x-app-layout>
