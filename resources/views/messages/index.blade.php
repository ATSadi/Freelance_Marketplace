<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Collaboration</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Messages</h2>
            <p class="mt-1 text-sm text-slate-500">Keep project conversations organized and private.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="wv-card overflow-hidden divide-y divide-slate-100">
            @forelse ($projects as $project)
                @php
                    $me = auth()->user();
                    $partner = $me->id === $project->client_id ? $project->freelancer : $project->client;
                    $partnerPhoto = $partner?->profile?->photoUrl();
                    $last = $project->latestMessage;
                    $hasUnread = $project->unread_messages_count > 0;
                @endphp

                <a href="{{ route('messages.show', $project) }}"
                    class="flex items-center gap-4 p-4 sm:p-5 transition hover:bg-brand-50/40 {{ $hasUnread ? 'bg-brand-50/25' : '' }}">

                    {{-- Partner avatar --}}
                    <span class="relative shrink-0">
                        @if ($partnerPhoto)
                            <img src="{{ $partnerPhoto }}" alt="{{ $partner->name }}"
                                class="h-12 w-12 rounded-full object-cover ring-2 ring-white shadow-soft">
                        @else
                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-gradient text-lg font-bold text-white">
                                {{ strtoupper(substr($partner?->name ?? '?', 0, 1)) }}
                            </span>
                        @endif
                        @if ($hasUnread)
                            <span class="absolute -top-0.5 -right-0.5 h-3 w-3 rounded-full bg-brand-600 ring-2 ring-white"></span>
                        @endif
                    </span>

                    {{-- Conversation summary --}}
                    <span class="min-w-0 flex-1">
                        <span class="flex items-baseline justify-between gap-3">
                            <span class="truncate font-semibold {{ $hasUnread ? 'text-slate-900' : 'text-slate-800' }}">
                                @if ($me->role === \App\Models\User::ROLE_ADMIN && ! in_array($me->id, [$project->client_id, $project->freelancer_id], true))
                                    {{ $project->client->name }} &harr; {{ $project->freelancer->name }}
                                @else
                                    {{ $partner?->name ?? __('Unassigned') }}
                                @endif
                            </span>
                            @if ($last)
                                <span class="shrink-0 text-xs {{ $hasUnread ? 'font-semibold text-brand-600' : 'text-slate-400' }}"
                                    title="{{ $last->created_at->format('M j, Y g:i A') }}">
                                    {{ $last->created_at->diffForHumans(short: true) }}
                                </span>
                            @endif
                        </span>

                        <span class="mt-0.5 block truncate text-xs font-medium text-brand-600/80">
                            {{ $project->title }}
                        </span>

                        <span class="mt-1 flex items-center justify-between gap-3">
                            <span class="truncate text-sm {{ $hasUnread ? 'font-semibold text-slate-700' : 'text-slate-500' }}">
                                @if ($last)
                                    @if ($last->sender_id === $me->id)
                                        <span class="text-slate-400">{{ __('You:') }}</span>
                                    @elseif ($me->role === \App\Models\User::ROLE_ADMIN)
                                        <span class="text-slate-400">{{ $last->sender?->name }}:</span>
                                    @endif
                                    {{ \Illuminate\Support\Str::limit($last->body, 90) }}
                                @else
                                    <span class="italic text-slate-400">{{ __('No messages yet — say hello!') }}</span>
                                @endif
                            </span>
                            @if ($hasUnread)
                                <span class="shrink-0 inline-flex min-w-[1.4rem] items-center justify-center rounded-full bg-brand-600 px-2 py-0.5 text-xs font-bold text-white">
                                    {{ $project->unread_messages_count > 9 ? '9+' : $project->unread_messages_count }}
                                </span>
                            @endif
                        </span>
                    </span>
                </a>
            @empty
                <div class="p-12 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-500">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm3.75 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm3.75 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM21 12c0 4.556-4.03 8.25-9 8.25a9.76 9.76 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/>
                        </svg>
                    </div>
                    <p class="mt-4 font-medium text-slate-700">{{ __('No conversations yet') }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ __('A chat opens when a freelancer is assigned to a project.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
