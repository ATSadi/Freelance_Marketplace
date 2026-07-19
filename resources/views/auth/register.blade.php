<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-slate-900">{{ __('Create your WorkVault account') }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ __('Pick a role to unlock the right dashboard and permissions.') }}</p>
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
            <legend class="text-sm font-medium text-slate-700 mb-2">{{ __('I am joining as') }}</legend>
            @php $selectedRole = old('role', request('role')); @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label class="role-option relative flex cursor-pointer rounded-xl border-2 p-4 transition {{ $selectedRole === 'client' ? 'border-brand-500 bg-brand-50' : 'border-slate-200 hover:border-slate-300' }}">
                    <input type="radio" name="role" value="client" class="sr-only" {{ $selectedRole === 'client' ? 'checked' : '' }} required>
                    <span>
                        <span class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                            <svg class="h-4 w-4 text-brand-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            {{ __('Client') }}
                        </span>
                        <span class="mt-1 block text-xs text-slate-500">{{ __('Post projects, manage milestones, and release escrow.') }}</span>
                    </span>
                </label>
                <label class="role-option relative flex cursor-pointer rounded-xl border-2 p-4 transition {{ $selectedRole === 'freelancer' ? 'border-brand-500 bg-brand-50' : 'border-slate-200 hover:border-slate-300' }}">
                    <input type="radio" name="role" value="freelancer" class="sr-only" {{ $selectedRole === 'freelancer' ? 'checked' : '' }} required>
                    <span>
                        <span class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                            <svg class="h-4 w-4 text-brand-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"/></svg>
                            {{ __('Freelancer') }}
                        </span>
                        <span class="mt-1 block text-xs text-slate-500">{{ __('Browse jobs, submit proposals, and deliver milestones.') }}</span>
                    </span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </fieldset>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center text-base py-3">
                {{ __('Create account') }}
            </x-primary-button>
        </div>

        <p class="mt-4 text-center text-sm text-slate-600">
            {{ __('Already registered?') }}
            <a class="font-semibold text-brand-600 hover:text-brand-800" href="{{ route('login') }}">{{ __('Log in') }}</a>
        </p>
    </form>

    <script>
        document.querySelectorAll('input[name="role"]').forEach((input) => {
            input.addEventListener('change', () => {
                document.querySelectorAll('input[name="role"]').forEach((el) => {
                    const label = el.closest('label');
                    label.classList.toggle('border-brand-500', el.checked);
                    label.classList.toggle('bg-brand-50', el.checked);
                    label.classList.toggle('border-slate-200', !el.checked);
                });
            });
        });
    </script>
</x-guest-layout>
