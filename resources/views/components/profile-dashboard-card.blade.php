@props(['user'])

@php
    $profile = $user->profile;
    $isComplete = $user->isProfileComplete();
@endphp

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900">
        @if (! $isComplete)
            <div class="mb-4 rounded-md bg-yellow-50 border border-yellow-200 p-4">
                <p class="text-sm text-yellow-800 font-medium">{{ __('Complete your profile') }}</p>
                <p class="mt-1 text-sm text-yellow-700">
                    {{ __('Add your profile details so clients and freelancers can learn more about you.') }}
                </p>
                <a href="{{ route('profile.edit') }}"
                    class="mt-3 inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                    {{ __('Complete profile now') }} &rarr;
                </a>
            </div>
        @else
            <div class="flex items-start gap-4">
                @if ($profile?->photoUrl())
                    <img src="{{ $profile->photoUrl() }}" alt="{{ $user->name }}"
                        class="h-16 w-16 rounded-full object-cover shrink-0">
                @else
                    <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center shrink-0">
                        <span class="text-lg font-semibold text-gray-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                @endif

                <div class="flex-1">
                    <h3 class="text-lg font-semibold">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500 capitalize">{{ $user->role }}</p>

                    @if ($profile?->bio)
                        <p class="mt-2 text-sm text-gray-700">{{ $profile->bio }}</p>
                    @endif

                    <dl class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                        @if ($profile?->phone)
                            <div>
                                <dt class="font-medium text-gray-500">{{ __('Phone') }}</dt>
                                <dd>{{ $profile->phone }}</dd>
                            </div>
                        @endif

                        @if ($user->role === \App\Models\User::ROLE_CLIENT && $profile?->company_name)
                            <div>
                                <dt class="font-medium text-gray-500">{{ __('Company') }}</dt>
                                <dd>{{ $profile->company_name }}</dd>
                            </div>
                        @endif

                        @if ($user->role === \App\Models\User::ROLE_FREELANCER && $profile?->skills)
                            <div class="sm:col-span-2">
                                <dt class="font-medium text-gray-500">{{ __('Skills') }}</dt>
                                <dd>{{ $profile->skills }}</dd>
                            </div>
                        @endif

                        @if ($user->role === \App\Models\User::ROLE_FREELANCER && $profile?->hourly_rate)
                            <div>
                                <dt class="font-medium text-gray-500">{{ __('Hourly Rate') }}</dt>
                                <dd>${{ number_format($profile->hourly_rate, 2) }}</dd>
                            </div>
                        @endif
                    </dl>

                    <a href="{{ route('profile.edit') }}" class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-800">
                        {{ __('Edit profile') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
