<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Work Profile') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Update your WorkVault profile details.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.work.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        @if ($profile->photoUrl())
            <div>
                <x-input-label :value="__('Current Photo')" />
                <img src="{{ $profile->photoUrl() }}" alt="{{ __('Profile photo') }}" class="mt-2 h-24 w-24 rounded-full object-cover">
            </div>
        @endif

        <div>
            <x-input-label for="profile_photo" :value="__('Profile Photo')" />
            <input id="profile_photo" name="profile_photo" type="file" accept="image/*"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
            <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
        </div>

        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea id="bio" name="bio" rows="4"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required>{{ old('bio', $profile->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div>
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                :value="old('phone', $profile->phone)" required />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        @if ($user->role === \App\Models\User::ROLE_CLIENT)
            <div>
                <x-input-label for="company_name" :value="__('Company Name')" />
                <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full"
                    :value="old('company_name', $profile->company_name)" required />
                <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
            </div>
        @endif

        @if ($user->role === \App\Models\User::ROLE_FREELANCER)
            <div>
                <x-input-label for="skills" :value="__('Skills')" />
                <textarea id="skills" name="skills" rows="3"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    placeholder="{{ __('e.g. Laravel, UI/UX, PostgreSQL') }}" required>{{ old('skills', $profile->skills) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('skills')" />
            </div>

            <div>
                <x-input-label for="hourly_rate" :value="__('Hourly Rate ($)')" />
                <x-text-input id="hourly_rate" name="hourly_rate" type="number" step="0.01" min="0" class="mt-1 block w-full"
                    :value="old('hourly_rate', $profile->hourly_rate)" required />
                <x-input-error class="mt-2" :messages="$errors->get('hourly_rate')" />
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Work Profile') }}</x-primary-button>

            @if (session('status') === 'work-profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
