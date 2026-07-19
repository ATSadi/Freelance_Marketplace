<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Project chat</p>
                <h2 class="mt-2 font-display text-2xl font-bold text-slate-900">{{ $project->title }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $project->client->name }} ↔ {{ $project->freelancer->name }}</p>
            </div>
            <a href="{{ route('messages.index') }}" class="btn-secondary">All conversations</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="wv-card overflow-hidden">
            <div id="messages" class="flex max-h-[58vh] min-h-[22rem] flex-col gap-3 overflow-y-auto bg-slate-50/70 p-5 sm:p-6">
                @forelse ($project->messages as $message)
                    @php $mine = $message->sender_id === Auth::id(); @endphp
                    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[82%] rounded-2xl px-4 py-3 shadow-soft {{ $mine ? 'bg-brand-gradient text-white rounded-br-md' : 'bg-white text-slate-700 rounded-bl-md' }}">
                            <p class="text-xs font-semibold {{ $mine ? 'text-white/70' : 'text-brand-600' }}">{{ $message->sender->name }}</p>
                            <p class="mt-1 whitespace-pre-wrap text-sm">{{ $message->body }}</p>
                            <p class="mt-1.5 text-[10px] {{ $mine ? 'text-white/60' : 'text-slate-400' }}">{{ $message->created_at->format('M j, g:i A') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="m-auto text-center">
                        <p class="font-medium text-slate-700">Start the conversation</p>
                        <p class="mt-1 text-sm text-slate-500">Use this chat for project updates and milestone questions.</p>
                    </div>
                @endforelse
            </div>

            @if (Auth::user()->role !== \App\Models\User::ROLE_ADMIN)
                <form method="POST" action="{{ route('messages.store', $project) }}" class="border-t border-slate-100 bg-white p-4">
                    @csrf
                    <div class="flex items-end gap-3">
                        <div class="flex-1">
                            <textarea name="body" rows="2" class="wv-input resize-none" maxlength="5000"
                                placeholder="Write a project message…" required>{{ old('body') }}</textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-1" />
                        </div>
                        <x-primary-button class="h-11 px-5">Send</x-primary-button>
                    </div>
                </form>
            @else
                <p class="border-t border-slate-100 bg-white p-4 text-center text-xs text-slate-500">Admin read-only moderation view</p>
            @endif
        </div>
    </div>

    <script>
        const messages = document.getElementById('messages');
        messages.scrollTop = messages.scrollHeight;
    </script>
</x-app-layout>
