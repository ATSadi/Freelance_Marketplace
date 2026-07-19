<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Collaboration</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Messages</h2>
            <p class="mt-1 text-sm text-slate-500">Keep project conversations organized and private.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="wv-card overflow-hidden">
            @forelse ($projects as $project)
                <a href="{{ route('messages.show', $project) }}"
                    class="flex items-center gap-4 border-b border-slate-100 p-5 transition last:border-0 hover:bg-brand-50/40">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-gradient font-bold text-white">
                        {{ strtoupper(substr($project->title, 0, 1)) }}
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate font-semibold text-slate-900">{{ $project->title }}</span>
                        <span class="mt-0.5 block truncate text-sm text-slate-500">
                            {{ $project->client->name }} ↔ {{ $project->freelancer->name }}
                        </span>
                    </span>
                    @if ($project->unread_messages_count)
                        <span class="rounded-full bg-brand-600 px-2.5 py-1 text-xs font-bold text-white">
                            {{ $project->unread_messages_count }}
                        </span>
                    @endif
                    <svg class="h-5 w-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 18 6-6-6-6"/>
                    </svg>
                </a>
            @empty
                <div class="p-12 text-center">
                    <p class="font-medium text-slate-700">No conversations yet</p>
                    <p class="mt-1 text-sm text-slate-500">A chat opens when a freelancer is assigned to a project.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
