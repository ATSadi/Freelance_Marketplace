@props(['user'])

@php
    $profile = $user->profile;
    $isComplete = $user->isProfileComplete();
@endphp

@if (! $isComplete)
    <div class="wv-card p-5 border-amber-200 bg-gradient-to-r from-amber-50 to-white">
        <div class="flex items-start gap-4">
            <div class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-600 shrink-0">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-amber-900">{{ __('Complete your profile') }}</p>
                <p class="mt-1 text-sm text-amber-700">{{ __('Add your profile details so clients and freelancers can learn more about you.') }}</p>
                <a href="{{ route('profile.edit') }}" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-800">
                    {{ __('Complete profile now') }} &rarr;
                </a>
            </div>
        </div>
    </div>
@else
    <div class="wv-card p-6">
        <div class="flex items-start gap-4">
            @if ($profile?->photoUrl())
                <img src="{{ $profile->photoUrl() }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-2xl object-cover shrink-0 ring-2 ring-brand-100">
            @else
                <div class="h-16 w-16 rounded-2xl bg-brand-gradient flex items-center justify-center shrink-0 shadow-glow">
                    <span class="text-xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
            @endif

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="text-lg font-display font-bold text-slate-900">{{ $user->name }}</h3>
                    <span class="wv-badge bg-brand-50 text-brand-700 ring-1 ring-brand-600/20 capitalize">{{ $user->role }}</span>
                </div>

                @if ($profile?->bio)
                    <p class="mt-2 text-sm text-slate-600">{{ $profile->bio }}</p>
                @endif

                <dl class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                    @if ($profile?->phone)
                        <div><dt class="font-medium text-slate-400">{{ __('Phone') }}</dt><dd class="text-slate-700">{{ $profile->phone }}</dd></div>
                    @endif
                    @if ($user->role === \App\Models\User::ROLE_CLIENT && $profile?->company_name)
                        <div><dt class="font-medium text-slate-400">{{ __('Company') }}</dt><dd class="text-slate-700">{{ $profile->company_name }}</dd></div>
                    @endif
                    @if ($user->role === \App\Models\User::ROLE_FREELANCER && $profile?->skills)
                        <div class="sm:col-span-2"><dt class="font-medium text-slate-400">{{ __('Skills') }}</dt><dd class="text-slate-700">{{ $profile->skills }}</dd></div>
                    @endif
                    @if ($user->role === \App\Models\User::ROLE_FREELANCER && $profile?->hourly_rate)
                        <div><dt class="font-medium text-slate-400">{{ __('Hourly Rate') }}</dt><dd class="text-slate-700">${{ number_format($profile->hourly_rate, 2) }}</dd></div>
                    @endif
                </dl>

                <a href="{{ route('profile.edit') }}" class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:text-brand-800">
                    {{ __('Edit profile') }}
                </a>
            </div>
        </div>
    </div>
@endif
