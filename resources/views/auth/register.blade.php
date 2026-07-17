<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('Create your WorkVault account') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ __('Pick a role to unlock the right dashboard and permissions.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <fieldset class="mt-4">
            <legend class="text-sm font-medium text-gray-700">{{ __('I am joining as') }}</legend>
            @php $selectedRole = old('role', request('role')); @endphp
            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label class="relative flex cursor-pointer rounded-lg border p-4 focus-within:ring-2 focus-within:ring-indigo-500 {{ $selectedRole === 'client' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                    <input type="radio" name="role" value="client" class="sr-only" {{ $selectedRole === 'client' ? 'checked' : '' }} required>
                    <span>
                        <span class="block text-sm font-semibold text-gray-900">{{ __('Client') }}</span>
                        <span class="mt-1 block text-xs text-gray-600">{{ __('Post projects, manage milestones, and release escrow.') }}</span>
                    </span>
                </label>
                <label class="relative flex cursor-pointer rounded-lg border p-4 focus-within:ring-2 focus-within:ring-indigo-500 {{ $selectedRole === 'freelancer' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                    <input type="radio" name="role" value="freelancer" class="sr-only" {{ $selectedRole === 'freelancer' ? 'checked' : '' }} required>
                    <span>
                        <span class="block text-sm font-semibold text-gray-900">{{ __('Freelancer') }}</span>
                        <span class="mt-1 block text-xs text-gray-600">{{ __('Browse jobs, submit proposals, and deliver milestones.') }}</span>
                    </span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </fieldset>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6 gap-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button>
                {{ __('Create account') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.querySelectorAll('input[name="role"]').forEach((input) => {
            input.addEventListener('change', () => {
                document.querySelectorAll('input[name="role"]').forEach((el) => {
                    el.closest('label').classList.toggle('border-indigo-500', el.checked);
                    el.closest('label').classList.toggle('bg-indigo-50', el.checked);
                    el.closest('label').classList.toggle('border-gray-200', !el.checked);
                });
            });
        });
    </script>
</x-guest-layout>
