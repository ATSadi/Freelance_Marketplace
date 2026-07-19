@props(['project', 'canManage' => false])

@php
    $milestones = $project->milestones;
    $total = $milestones->count();
    $completed = $milestones->filter(fn ($m) => $m->isCompleted())->count();
    $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
    $user = Auth::user();
    $isFreelancer = $user && $user->id === $project->freelancer_id;
    $isClient = $user && $user->id === $project->client_id;
@endphp

<div>
    <div class="flex flex-wrap justify-between items-center gap-2">
        <h3 class="text-lg font-display font-bold text-slate-900">{{ __('Milestones') }}</h3>
        @if ($canManage)
            <a href="{{ route('client.projects.milestones.create', $project) }}" class="btn-primary text-xs px-3 py-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                {{ __('Add Milestone') }}
            </a>
        @endif
    </div>

    @if ($total === 0)
        <p class="mt-3 text-sm text-slate-500">{{ __('No milestones created yet.') }}</p>
    @else
        {{-- Progress indicator --}}
        <div class="mt-4">
            <div class="flex justify-between text-sm text-slate-500">
                <span>{{ $completed }} {{ __('of') }} {{ $total }} {{ __('milestones completed') }}</span>
                <span class="font-semibold text-slate-700">{{ $percent }}%</span>
            </div>
            <div class="mt-2 w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div class="h-2.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%; background-image: linear-gradient(90deg, #6366f1, #7c3aed);"></div>
            </div>
        </div>

        <div class="mt-5 space-y-3">
            @foreach ($milestones as $milestone)
                <div class="rounded-xl border border-slate-200 p-4 hover:border-brand-200 transition">
                    <div class="flex flex-wrap justify-between items-start gap-2">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $milestone->order_index }}. {{ $milestone->title }}</p>
                            <p class="mt-1 text-sm text-slate-500 whitespace-pre-line">{{ $milestone->description }}</p>
                        </div>
                        <x-milestone-status-badge :status="$milestone->status" />
                    </div>

                    <div class="mt-3 flex flex-wrap gap-4 text-sm text-slate-500">
                        <span><span class="font-medium text-slate-700">{{ __('Amount:') }}</span> ${{ number_format($milestone->amount, 2) }}</span>
                        <span><span class="font-medium text-slate-700">{{ __('Due:') }}</span> {{ $milestone->due_date->format('M j, Y') }}</span>
                    </div>

                    @if ($isClient && $milestone->status !== \App\Models\Milestone::STATUS_PAID)
                        @php $stripeFunded = $milestone->stripePayments()->where('status', \App\Models\StripePayment::STATUS_PAID)->exists(); @endphp
                        <div class="mt-3">
                            @if ($stripeFunded)
                                <span class="wv-badge bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20">Stripe funded</span>
                            @else
                                <form method="POST" action="{{ route('stripe.checkout', $milestone) }}">
                                    @csrf
                                    <button type="submit" class="btn-secondary text-xs">Fund securely with Stripe</button>
                                </form>
                                <x-input-error :messages="$errors->get('stripe')" class="mt-2" />
                            @endif
                        </div>
                    @endif

                    @if ($milestone->submission_notes)
                        <div class="mt-3 rounded-xl bg-slate-50 border border-slate-200 p-3">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Delivery notes') }}</p>
                            <p class="mt-1 text-sm text-slate-700 whitespace-pre-line">{{ $milestone->submission_notes }}</p>
                            @if ($milestone->submitted_at)
                                <p class="mt-1 text-xs text-slate-400">{{ __('Submitted') }} {{ $milestone->submitted_at->format('M j, Y g:i A') }}</p>
                            @endif
                        </div>
                    @endif

                    @if ($milestone->client_feedback)
                        <div class="mt-3 rounded-md bg-amber-50 border border-amber-200 p-3">
                            <p class="text-xs font-medium text-amber-700 uppercase tracking-wide">{{ __('Client feedback') }}</p>
                            <p class="mt-1 text-sm text-amber-900 whitespace-pre-line">{{ $milestone->client_feedback }}</p>
                        </div>
                    @endif

                    @if ($milestone->status === \App\Models\Milestone::STATUS_PAID)
                        <div class="mt-3">
                            <a href="{{ route('invoices.show', $milestone) }}"
                                class="inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:text-brand-800">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                {{ __('View invoice') }}</a>
                        </div>
                    @endif

                    @if ($canManage && $milestone->status === \App\Models\Milestone::STATUS_PENDING)
                        <div class="mt-3 flex items-center gap-4">
                            <a href="{{ route('client.projects.milestones.edit', [$project, $milestone]) }}"
                                class="text-sm font-medium text-brand-600 hover:text-brand-800">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('client.projects.milestones.destroy', [$project, $milestone]) }}"
                                onsubmit="return confirm('{{ __('Delete this milestone?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-800">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    @endif

                    {{-- Freelancer: start work --}}
                    @if ($isFreelancer && $milestone->canBeStarted())
                        <form method="POST" action="{{ route('freelancer.milestones.start', [$project, $milestone]) }}" class="mt-3">
                            @csrf
                            <x-primary-button>{{ __('Start Work') }}</x-primary-button>
                        </form>
                    @endif

                    {{-- Freelancer: submit delivery --}}
                    @if ($isFreelancer && $milestone->canBeSubmitted())
                        <form method="POST" action="{{ route('freelancer.milestones.submit', [$project, $milestone]) }}" class="mt-3 space-y-3">
                            @csrf
                            <div>
                                <x-input-label for="submission_notes_{{ $milestone->id }}" :value="__('Delivery notes')" />
                                <textarea id="submission_notes_{{ $milestone->id }}" name="submission_notes" rows="3"
                                    class="wv-input"
                                    placeholder="{{ __('Describe what you delivered…') }}" required>{{ old('submission_notes') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('submission_notes')" />
                            </div>
                            <x-primary-button>{{ __('Submit for Review') }}</x-primary-button>
                        </form>
                    @endif

                    {{-- Client: approve or request changes --}}
                    @if ($isClient && $milestone->canBeReviewed())
                        <div class="mt-3 space-y-3 border-t border-slate-100 pt-3">
                            <div>
                                <x-input-label for="client_feedback_{{ $milestone->id }}" :value="__('Feedback (required for changes)')" />
                                <textarea id="client_feedback_{{ $milestone->id }}" name="client_feedback" form="review-approve-{{ $milestone->id }}" rows="2"
                                    class="wv-input"
                                    placeholder="{{ __('Optional praise, or notes when requesting changes…') }}">{{ old('client_feedback') }}</textarea>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <form id="review-approve-{{ $milestone->id }}" method="POST"
                                    action="{{ route('client.milestones.approve', [$project, $milestone]) }}">
                                    @csrf
                                    <x-primary-button>{{ __('Approve') }}</x-primary-button>
                                </form>
                                <form method="POST" action="{{ route('client.milestones.request-changes', [$project, $milestone]) }}"
                                    onsubmit="
                                        const feedback = document.getElementById('client_feedback_{{ $milestone->id }}').value;
                                        if (!feedback.trim()) { alert('{{ __('Please add feedback when requesting changes.') }}'); return false; }
                                        this.querySelector('[name=client_feedback]').value = feedback;
                                    ">
                                    @csrf
                                    <input type="hidden" name="client_feedback" value="">
                                    <x-secondary-button>{{ __('Request Changes') }}</x-secondary-button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-5 rounded-xl bg-brand-50/60 border border-brand-100 p-4 text-sm text-slate-700">
            <span class="font-semibold text-slate-900">{{ __('Total allocated:') }}</span>
            ${{ number_format($project->milestonesTotal(), 2) }}
            @if ($project->agreedAmount() > 0)
                <span class="text-slate-400">/ ${{ number_format($project->agreedAmount(), 2) }} agreed</span>
            @endif
        </div>
    @endif
</div>
