<section>
    <header>
        <h3 class="text-lg font-display font-bold text-slate-900">{{ __('Submit a Proposal') }}</h3>
        <p class="mt-1 text-sm text-slate-500">{{ __('Tell the client why you are a good fit for this project.') }}</p>
    </header>

    <form method="POST" action="{{ route('freelancer.proposals.store', $project) }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <x-input-label for="cover_letter" :value="__('Cover Letter')" />
            <textarea id="cover_letter" name="cover_letter" rows="5"
                class="wv-input"
                required>{{ old('cover_letter') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('cover_letter')" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="proposed_amount" :value="__('Proposed Amount ($)')" />
                <x-text-input id="proposed_amount" name="proposed_amount" type="number" step="0.01" min="0.01"
                    class="mt-1 block w-full" :value="old('proposed_amount')" required />
                <x-input-error class="mt-2" :messages="$errors->get('proposed_amount')" />
            </div>

            <div>
                <x-input-label for="proposed_duration_days" :value="__('Duration (days)')" />
                <x-text-input id="proposed_duration_days" name="proposed_duration_days" type="number" min="1"
                    class="mt-1 block w-full" :value="old('proposed_duration_days')" required />
                <x-input-error class="mt-2" :messages="$errors->get('proposed_duration_days')" />
            </div>
        </div>

        <x-input-error class="mt-2" :messages="$errors->get('project_id')" />

        <x-primary-button>{{ __('Submit Proposal') }}</x-primary-button>
    </form>
</section>
