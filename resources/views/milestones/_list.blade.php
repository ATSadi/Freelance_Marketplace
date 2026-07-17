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
        <h3 class="text-lg font-medium">{{ __('Milestones') }}</h3>
        @if ($canManage)
            <a href="{{ route('client.projects.milestones.create', $project) }}"
                class="inline-flex items-center px-3 py-1.5 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('Add Milestone') }}
            </a>
        @endif
    </div>

    @if ($total === 0)
        <p class="mt-3 text-sm text-gray-600">{{ __('No milestones created yet.') }}</p>
    @else
        {{-- Progress indicator --}}
        <div class="mt-4">
            <div class="flex justify-between text-sm text-gray-600">
                <span>{{ $completed }} {{ __('of') }} {{ $total }} {{ __('milestones completed') }}</span>
                <span>{{ $percent }}%</span>
            </div>
            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $percent }}%"></div>
            </div>
        </div>

        <div class="mt-4 space-y-3">
            @foreach ($milestones as $milestone)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex flex-wrap justify-between items-start gap-2">
                        <div>
                            <p class="font-medium">{{ $milestone->order_index }}. {{ $milestone->title }}</p>
                            <p class="mt-1 text-sm text-gray-600 whitespace-pre-line">{{ $milestone->description }}</p>
                        </div>
                        <x-milestone-status-badge :status="$milestone->status" />
                    </div>

                    <div class="mt-3 flex flex-wrap gap-4 text-sm text-gray-600">
                        <span><span class="font-medium">{{ __('Amount:') }}</span> ${{ number_format($milestone->amount, 2) }}</span>
                        <span><span class="font-medium">{{ __('Due:') }}</span> {{ $milestone->due_date->format('M j, Y') }}</span>
                    </div>

                    @if ($milestone->submission_notes)
                        <div class="mt-3 rounded-md bg-gray-50 border border-gray-200 p-3">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Delivery notes') }}</p>
                            <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $milestone->submission_notes }}</p>
                            @if ($milestone->submitted_at)
                                <p class="mt-1 text-xs text-gray-500">{{ __('Submitted') }} {{ $milestone->submitted_at->format('M j, Y g:i A') }}</p>
                            @endif
                        </div>
                    @endif

                    @if ($milestone->client_feedback)
                        <div class="mt-3 rounded-md bg-amber-50 border border-amber-200 p-3">
                            <p class="text-xs font-medium text-amber-700 uppercase tracking-wide">{{ __('Client feedback') }}</p>
                            <p class="mt-1 text-sm text-amber-900 whitespace-pre-line">{{ $milestone->client_feedback }}</p>
                        </div>
                    @endif

                    @if ($canManage && $milestone->status === \App\Models\Milestone::STATUS_PENDING)
                        <div class="mt-3 flex items-center gap-3">
                            <a href="{{ route('client.projects.milestones.edit', [$project, $milestone]) }}"
                                class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('client.projects.milestones.destroy', [$project, $milestone]) }}"
                                onsubmit="return confirm('{{ __('Delete this milestone?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800">{{ __('Delete') }}</button>
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
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    placeholder="{{ __('Describe what you delivered…') }}" required>{{ old('submission_notes') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('submission_notes')" />
                            </div>
                            <x-primary-button>{{ __('Submit for Review') }}</x-primary-button>
                        </form>
                    @endif

                    {{-- Client: approve or request changes --}}
                    @if ($isClient && $milestone->canBeReviewed())
                        <div class="mt-3 space-y-3 border-t border-gray-100 pt-3">
                            <div>
                                <x-input-label for="client_feedback_{{ $milestone->id }}" :value="__('Feedback (required for changes)')" />
                                <textarea id="client_feedback_{{ $milestone->id }}" name="client_feedback" form="review-approve-{{ $milestone->id }}" rows="2"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
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

        <div class="mt-4 text-sm text-gray-600">
            <span class="font-medium">{{ __('Total allocated:') }}</span>
            ${{ number_format($project->milestonesTotal(), 2) }}
            @if ($project->agreedAmount() > 0)
                / ${{ number_format($project->agreedAmount(), 2) }}
            @endif
        </div>
    @endif
</div>
