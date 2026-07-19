@php
    $role = Auth::user()->role;
    $unread = Auth::user()->unreadNotifications->count();
    $navPhotoUrl = Auth::user()->profile?->photoUrl();
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-40 glass border-b border-white/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::user()->dashboardRoute() }}" class="flex items-center">
                        <x-application-logo :with-text="true" />
                    </a>
                </div>

                <div class="hidden space-x-5 xl:-my-px xl:ms-8 xl:flex">
                    <a href="{{ Auth::user()->dashboardRoute() }}"
                        class="wv-nav-link {{ request()->routeIs('*.dashboard') ? 'wv-nav-link-active' : '' }}">
                        {{ __('Dashboard') }}
                    </a>

                    @if ($role === \App\Models\User::ROLE_CLIENT)
                        <a href="{{ route('client.projects.index') }}"
                            class="wv-nav-link {{ request()->routeIs('client.projects.*') ? 'wv-nav-link-active' : '' }}">
                            {{ __('My Projects') }}
                        </a>
                        <a href="{{ route('freelancers.index') }}"
                            class="wv-nav-link {{ request()->routeIs('freelancers.*') ? 'wv-nav-link-active' : '' }}">
                            {{ __('Talent') }}
                        </a>
                    @endif

                    @if ($role === \App\Models\User::ROLE_FREELANCER)
                        <a href="{{ route('freelancer.projects.browse') }}"
                            class="wv-nav-link {{ request()->routeIs('freelancer.projects.browse') ? 'wv-nav-link-active' : '' }}">
                            {{ __('Browse Projects') }}
                        </a>
                        <a href="{{ route('freelancer.proposals.index') }}"
                            class="wv-nav-link {{ request()->routeIs('freelancer.proposals.*') ? 'wv-nav-link-active' : '' }}">
                            {{ __('My Proposals') }}
                        </a>
                        <a href="{{ route('saved-projects.index') }}"
                            class="wv-nav-link {{ request()->routeIs('saved-projects.*') ? 'wv-nav-link-active' : '' }}">
                            {{ __('Saved') }}
                        </a>
                    @endif

                    @if (in_array($role, [\App\Models\User::ROLE_CLIENT, \App\Models\User::ROLE_FREELANCER], true))
                        <a href="{{ route('orders.index') }}"
                            class="wv-nav-link {{ request()->routeIs('orders.*') ? 'wv-nav-link-active' : '' }}">
                            {{ __('Orders') }}
                        </a>
                        @if ($role === \App\Models\User::ROLE_CLIENT)
                        <a href="{{ route('transactions.index') }}"
                            class="wv-nav-link {{ request()->routeIs('transactions.*') ? 'wv-nav-link-active' : '' }}">
                            {{ __('Billing') }}
                        </a>
                        @else
                        <a href="{{ route('wallet.index') }}"
                            class="wv-nav-link {{ request()->routeIs('wallet.*') ? 'wv-nav-link-active' : '' }}">
                            {{ __('Wallet') }}
                        </a>
                        @endif
                        <a href="{{ route('messages.index') }}"
                            class="wv-nav-link {{ request()->routeIs('messages.*') ? 'wv-nav-link-active' : '' }}">
                            {{ __('Messages') }}
                        </a>
                        <a href="{{ route('disputes.index') }}"
                            class="wv-nav-link {{ request()->routeIs('disputes.*') ? 'wv-nav-link-active' : '' }}">
                            {{ __('Disputes') }}
                        </a>
                    @endif

                    @if ($role === \App\Models\User::ROLE_ADMIN)
                        <a href="{{ route('admin.users.index') }}"
                            class="wv-nav-link {{ request()->routeIs('admin.users.*') ? 'wv-nav-link-active' : '' }}">
                            {{ __('Users') }}
                        </a>
                        <a href="{{ route('admin.projects.index') }}"
                            class="wv-nav-link {{ request()->routeIs('admin.projects.*') ? 'wv-nav-link-active' : '' }}">
                            {{ __('Projects') }}
                        </a>
                        <a href="{{ route('admin.payments.index') }}"
                            class="wv-nav-link {{ request()->routeIs('admin.payments.*') ? 'wv-nav-link-active' : '' }}">
                            {{ __('Payments') }}
                        </a>
                        <a href="{{ route('admin.withdrawals.index') }}"
                            class="wv-nav-link {{ request()->routeIs('admin.withdrawals.*') ? 'wv-nav-link-active' : '' }}">
                            {{ __('Withdrawals') }}
                        </a>
                        <a href="{{ route('admin.disputes.index') }}"
                            class="wv-nav-link {{ request()->routeIs('admin.disputes.*') || request()->routeIs('disputes.show') ? 'wv-nav-link-active' : '' }}">
                            {{ __('Disputes') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden xl:flex xl:items-center xl:ms-5 gap-2">
                @if (in_array($role, [\App\Models\User::ROLE_CLIENT, \App\Models\User::ROLE_FREELANCER, \App\Models\User::ROLE_ADMIN], true))
                    <a href="{{ route('notifications.index') }}"
                        class="relative inline-flex items-center justify-center h-10 w-10 rounded-xl text-slate-500 hover:text-brand-700 hover:bg-brand-50 transition"
                        title="{{ __('Notifications') }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        @if ($unread > 0)
                            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center min-w-[1.15rem] h-[1.15rem] px-1 rounded-full bg-gradient-to-br from-rose-500 to-red-600 text-[10px] font-bold text-white shadow">{{ $unread > 9 ? '9+' : $unread }}</span>
                        @endif
                    </a>
                @endif

                <div x-data="{ menu: false }" class="relative">
                    <button @click="menu = !menu" @click.outside="menu = false"
                        class="flex items-center gap-2 rounded-xl py-1.5 pl-1.5 pr-3 hover:bg-slate-100 transition">
                        @if ($navPhotoUrl)
                            <img src="{{ $navPhotoUrl }}" alt="{{ Auth::user()->name }}" class="h-9 w-9 rounded-lg object-cover shadow-soft ring-1 ring-brand-100">
                        @else
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-brand-gradient text-sm font-bold text-white shadow-soft">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        @endif
                        <span class="text-left leading-tight">
                            <span class="block text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</span>
                            <span class="block text-[11px] font-medium capitalize text-brand-600">{{ $role }}</span>
                        </span>
                        <svg class="h-4 w-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>

                    <div x-show="menu" x-transition x-cloak
                        class="absolute right-0 mt-2 w-52 origin-top-right rounded-2xl bg-white p-2 shadow-card border border-slate-100">
                        <a href="{{ route('profile.edit') }}" class="link-chip w-full">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 19.5a7.5 7.5 0 0 1 15 0"/></svg>
                            {{ __('Profile') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="link-chip w-full text-left hover:bg-rose-50 hover:text-rose-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="-me-2 flex items-center xl:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden xl:hidden bg-white/95 backdrop-blur border-t border-slate-100">
        <div class="pt-2 pb-3 space-y-1 px-3">
            <x-responsive-nav-link :href="Auth::user()->dashboardRoute()" :active="request()->routeIs('*.dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if ($role === \App\Models\User::ROLE_CLIENT)
                <x-responsive-nav-link :href="route('client.projects.index')" :active="request()->routeIs('client.projects.*')">
                    {{ __('My Projects') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('freelancers.index')" :active="request()->routeIs('freelancers.*')">
                    {{ __('Talent') }}
                </x-responsive-nav-link>
            @endif
            @if ($role === \App\Models\User::ROLE_FREELANCER)
                <x-responsive-nav-link :href="route('freelancer.projects.browse')" :active="request()->routeIs('freelancer.projects.browse')">
                    {{ __('Browse Projects') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('freelancer.proposals.index')" :active="request()->routeIs('freelancer.proposals.*')">
                    {{ __('My Proposals') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('saved-projects.index')" :active="request()->routeIs('saved-projects.*')">
                    {{ __('Saved Projects') }}
                </x-responsive-nav-link>
            @endif
            @if (in_array($role, [\App\Models\User::ROLE_CLIENT, \App\Models\User::ROLE_FREELANCER], true))
                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                    {{ __('Orders') }}
                </x-responsive-nav-link>
                @if ($role === \App\Models\User::ROLE_CLIENT)
                <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                    {{ __('Billing & Payments') }}
                </x-responsive-nav-link>
                @else
                <x-responsive-nav-link :href="route('wallet.index')" :active="request()->routeIs('wallet.*')">
                    {{ __('Wallet') }}
                </x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                    {{ __('Messages') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('disputes.index')" :active="request()->routeIs('disputes.*')">
                    {{ __('Disputes') }}
                </x-responsive-nav-link>
            @endif
            @if ($role === \App\Models\User::ROLE_ADMIN)
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    {{ __('Users') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.projects.index')" :active="request()->routeIs('admin.projects.*')">
                    {{ __('Projects') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.*')">
                    {{ __('Payments') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.withdrawals.index')" :active="request()->routeIs('admin.withdrawals.*')">
                    {{ __('Withdrawals') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.disputes.index')" :active="request()->routeIs('admin.disputes.*') || request()->routeIs('disputes.show')">
                    {{ __('Disputes') }}
                </x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                {{ __('Notifications') }}@if ($unread > 0) <span class="ml-1 text-brand-600 font-semibold">({{ $unread }})</span>@endif
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-2 border-t border-slate-100 px-3">
            <div class="flex items-center gap-3 px-1">
                @if ($navPhotoUrl)
                    <img src="{{ $navPhotoUrl }}" alt="{{ Auth::user()->name }}" class="h-10 w-10 rounded-lg object-cover ring-1 ring-brand-100">
                @else
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-brand-gradient text-sm font-bold text-white">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                @endif
                <div>
                    <div class="font-semibold text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-slate-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
