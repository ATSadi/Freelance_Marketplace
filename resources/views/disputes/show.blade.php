<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Dispute</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">{{ $dispute->reason }}</h2>
            </div>
            <x-dispute-status-badge :status="$dispute->status" />
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <x-flash-status />

        <div class="wv-card">
            <div class="p-6 text-slate-800 space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Project') }}</p>
                    <a href="{{ route('projects.show', $dispute->project) }}" class="font-semibold text-brand-600 hover:text-brand-800">
                        {{ $dispute->project->title }}
                    </a>
                </div>

                @if ($dispute->milestone)
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Milestone') }}</p>
                        <p class="font-semibold text-slate-900">{{ $dispute->milestone->title }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-400">{{ __('Opened by') }}</p>
                        <p class="mt-1 font-semibold text-slate-900">{{ $dispute->opener->name }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-400">{{ __('Against') }}</p>
                        <p class="mt-1 font-semibold text-slate-900">{{ $dispute->againstUser->name }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('Description') }}</p>
                    <p class="mt-1 whitespace-pre-line text-slate-700">{{ $dispute->description }}</p>
                </div>

                @if ($dispute->admin_notes)
                    <div class="rounded-xl bg-brand-50/60 border border-brand-100 p-4">
                        <p class="text-sm font-semibold text-slate-900">{{ __('Admin decision') }}</p>
                        <p class="mt-1 text-sm whitespace-pre-line text-slate-700">{{ $dispute->admin_notes }}</p>
                        @if ($dispute->resolver)
                            <p class="mt-2 text-xs text-slate-400">
                                {{ $dispute->resolver->name }} &middot; {{ $dispute->resolved_at?->format('M j, Y g:i A') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @can('moderate', $dispute)
            <div class="wv-card">
                <div class="p-6 text-slate-800 space-y-4">
                    <h3 class="text-lg font-display font-bold text-slate-900">{{ __('Admin actions') }}</h3>

                    @if ($dispute->status === \App\Models\Dispute::STATUS_OPEN)
                        <form method="POST" action="{{ route('admin.disputes.review', $dispute) }}">
                            @csrf
                            <x-secondary-button>{{ __('Mark Under Review') }}</x-secondary-button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.disputes.resolve', $dispute) }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="status" :value="__('Resolution')" />
                            <select id="status" name="status" class="wv-input" required>
                                <option value="resolved">{{ __('Resolve in favor / close') }}</option>
                                <option value="dismissed">{{ __('Dismiss dispute') }}</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="financial_action" :value="__('Financial action')" />
                            <select id="financial_action" name="financial_action" class="wv-input" required>
                                <option value="none">{{ __('No money movement') }}</option>
                                @if ($dispute->milestone)
                                    <option value="release">{{ __('Release escrow for disputed milestone') }}</option>
                                    <option value="refund">{{ __('Refund escrow for disputed milestone') }}</option>
                                @else
                                    <option value="refund">{{ __('Refund all open escrow holds') }}</option>
                                @endif
                                <option value="cancel_project">{{ __('Refund open holds and cancel project') }}</option>
                            </select>
                            <p class="mt-1 text-xs text-slate-500">Choose how escrow should settle when this dispute closes.</p>
                        </div>
                        <div>
                            <x-input-label for="admin_notes" :value="__('Admin notes')" />
                            <textarea id="admin_notes" name="admin_notes" rows="4"
                                class="wv-input"
                                required>{{ old('admin_notes') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('admin_notes')" />
                        </div>
                        <x-primary-button>{{ __('Submit Decision') }}</x-primary-button>
                    </form>
                </div>
            </div>
        @endcan

        <a href="{{ Auth::user()->role === \App\Models\User::ROLE_ADMIN ? route('admin.disputes.index') : route('disputes.index') }}"
            class="inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-brand-700">&larr; {{ __('Back to disputes') }}</a>
    </div>
</x-app-layout>
