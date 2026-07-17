<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Notifications') }}
            </h2>
            @if (Auth::user()->unreadNotifications->isNotEmpty())
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <x-secondary-button>{{ __('Mark all as read') }}</x-secondary-button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($notifications->isEmpty())
                        <p class="text-sm text-gray-600">{{ __('You have no notifications yet.') }}</p>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach ($notifications as $notification)
                                <li class="py-4 {{ $notification->read_at ? '' : 'bg-indigo-50/50 -mx-2 px-2 rounded-md' }}">
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left">
                                            <p class="font-medium {{ $notification->read_at ? 'text-gray-800' : 'text-indigo-900' }}">
                                                {{ $notification->data['title'] ?? __('Notification') }}
                                            </p>
                                            <p class="mt-1 text-sm text-gray-600">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
