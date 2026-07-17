@props(['project', 'canManage' => false])

@php
    $milestones = $project->milestones;
    $total = $milestones->count();
    $completed = $milestones->filter(fn ($m) => $m->isCompleted())->count();
    $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
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
