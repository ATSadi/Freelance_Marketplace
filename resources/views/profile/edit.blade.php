<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Account</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Profile settings</h2>
            <p class="mt-1 text-sm text-slate-500">Manage your public profile, account details, and password.</p>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="wv-card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-work-profile-form')
            </div>
        </div>

        <div class="wv-card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="wv-card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="wv-card p-6 sm:p-8 border-rose-100">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
