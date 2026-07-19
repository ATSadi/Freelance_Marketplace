<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Administration</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Users</h2>
            <p class="mt-1 text-sm text-slate-500">Search accounts and control platform access.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
        <form class="wv-card grid gap-3 p-4 sm:grid-cols-[1fr_12rem_auto]" method="GET">
            <input class="wv-input" name="q" value="{{ request('q') }}" placeholder="Search name or email">
            <select class="wv-input" name="role">
                <option value="">All roles</option>
                @foreach (['client', 'freelancer', 'admin'] as $role)
                    <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
            <x-primary-button>Filter</x-primary-button>
        </form>

        <div class="wv-card overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="p-4">User</th><th class="p-4">Role</th><th class="p-4">Activity</th><th class="p-4">Status</th><th class="p-4 text-right">Action</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr class="hover:bg-slate-50/70">
                            <td class="p-4"><p class="font-semibold text-slate-900">{{ $user->name }}</p><p class="text-xs text-slate-500">{{ $user->email }}</p></td>
                            <td class="p-4 capitalize">{{ $user->role }}</td>
                            <td class="p-4 text-slate-500">{{ $user->projects_count }} projects · {{ $user->proposals_count }} proposals · {{ $user->received_reviews_count }} reviews</td>
                            <td class="p-4"><span class="wv-badge {{ $user->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ $user->is_active ? 'Active' : 'Suspended' }}</span></td>
                            <td class="p-4 text-right">
                                @unless ($user->is(Auth::user()))
                                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                        @csrf @method('PATCH')
                                        <button class="font-semibold {{ $user->is_active ? 'text-rose-600' : 'text-emerald-600' }}">{{ $user->is_active ? 'Suspend' : 'Activate' }}</button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
</x-app-layout>
