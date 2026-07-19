<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Activity</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Notifications</h2>
            </div>
            @if (Auth::user()->unreadNotifications->isNotEmpty())
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <x-secondary-button>Mark all as read</x-secondary-button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="wv-card">
            <div class="p-6">
                @if ($notifications->isEmpty())
                    <div class="text-center py-10">
                        <div class="mx-auto h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                        </div>
                        <p class="mt-4 text-sm text-slate-500">You have no notifications yet.</p>
                    </div>
                @else
                    <ul class="space-y-2">
                        @foreach ($notifications as $notification)
                            <li>
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex gap-3 rounded-xl p-3 transition hover:bg-slate-50 {{ $notification->read_at ? '' : 'bg-brand-50/60' }}">
                                        <span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-lg shrink-0 {{ $notification->read_at ? 'bg-slate-100 text-slate-400' : 'bg-brand-gradient text-white' }}">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                                        </span>
                                        <span class="flex-1 min-w-0">
                                            <span class="block font-semibold {{ $notification->read_at ? 'text-slate-700' : 'text-slate-900' }}">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                                @unless ($notification->read_at)<span class="ml-2 inline-block h-2 w-2 rounded-full bg-brand-500 align-middle"></span>@endunless
                                            </span>
                                            <span class="mt-0.5 block text-sm text-slate-500">{{ $notification->data['message'] ?? '' }}</span>
                                            <span class="mt-1 block text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                                        </span>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4">{{ $notifications->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
