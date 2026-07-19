<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-slate-900">{{ __('Welcome back') }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ __('Sign in to your client, freelancer, or admin dashboard.') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-brand-600 shadow-sm focus:ring-brand-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-brand-600 hover:text-brand-800" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center text-base py-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50/70 p-4">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Demo accounts — click to fill</p>
        <div class="mt-3 grid grid-cols-1 gap-2">
            @php
                $demos = [
                    ['Client', 'client@workvault.test', 'bg-blue-500'],
                    ['Freelancer', 'freelancer@workvault.test', 'bg-emerald-500'],
                    ['Admin', 'admin@workvault.test', 'bg-violet-500'],
                ];
            @endphp
            @foreach ($demos as $d)
                <button type="button"
                    onclick="document.getElementById('email').value='{{ $d[1] }}';document.getElementById('password').value='password';"
                    class="flex items-center justify-between rounded-lg bg-white border border-slate-200 px-3 py-2 text-left text-sm hover:border-brand-300 hover:shadow-soft transition">
                    <span class="flex items-center gap-2 font-medium text-slate-700">
                        <span class="h-2 w-2 rounded-full {{ $d[2] }}"></span> {{ $d[0] }}
                    </span>
                    <span class="text-xs text-slate-400">{{ $d[1] }}</span>
                </button>
            @endforeach
        </div>
        <p class="mt-2 text-[11px] text-slate-400">Password for all demo accounts: <span class="font-mono font-semibold">password</span></p>
    </div>

    <p class="mt-6 text-center text-sm text-slate-600">
        {{ __('New here?') }}
        <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:text-brand-800">{{ __('Create an account') }}</a>
    </p>
</x-guest-layout>
