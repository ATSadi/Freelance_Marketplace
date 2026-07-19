<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-slate-900">Reset your password</h1>
        <p class="mt-2 text-sm text-slate-500">
            Enter your account email and we will send a secure reset link.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('local_reset_url'))
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            <p class="font-semibold">Local showcase inbox</p>
            <p class="mt-1">Email delivery is logged locally. Use this secure one-time link to complete the reset:</p>
            <a href="{{ session('local_reset_url') }}" class="mt-2 inline-flex font-semibold text-brand-700 underline">Open password reset</a>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-5">
            <x-primary-button class="w-full justify-center">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
