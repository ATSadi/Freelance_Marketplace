<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Platform administration</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Admin Dashboard</h2>
            <p class="mt-1 text-sm text-slate-500">Monitor users, projects, escrow flow, and dispute mediation.</p>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            <div class="stat-card animate-fade-up">
                <span class="eyebrow">Users</span>
                <p class="mt-2 text-3xl font-display font-bold text-slate-900">{{ $userCount }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $clientCount }} clients · {{ $freelancerCount }} freelancers</p>
            </div>
            <div class="stat-card animate-fade-up animate-delay-100">
                <span class="eyebrow">Projects</span>
                <p class="mt-2 text-3xl font-display font-bold text-slate-900">{{ $projectCount }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $activeProjects }} active</p>
            </div>
            <div class="stat-card animate-fade-up animate-delay-200">
                <span class="eyebrow">Open disputes</span>
                <p class="mt-2 text-3xl font-display font-bold text-rose-600">{{ $openDisputes }}</p>
                <p class="text-xs text-slate-500 mt-1">Awaiting review</p>
            </div>
            <div class="stat-card animate-fade-up animate-delay-300">
                <span class="eyebrow">Escrow released</span>
                <p class="mt-2 text-3xl font-display font-bold text-emerald-600">${{ number_format($totalEscrowReleased, 2) }}</p>
                <p class="text-xs text-slate-500 mt-1">Paid to freelancers</p>
            </div>
            <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}" class="stat-card animate-fade-up transition hover:-translate-y-1 hover:border-amber-200">
                <span class="eyebrow">Withdrawals</span>
                <p class="mt-2 text-3xl font-display font-bold text-amber-600">{{ $pendingWithdrawals }}</p>
                <p class="text-xs text-slate-500 mt-1">${{ number_format($pendingWithdrawalTotal, 2) }} awaiting review</p>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="wv-card p-6 animate-fade-up">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-display font-bold text-slate-900">Recent disputes</h3>
                    <a href="{{ route('admin.disputes.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-800">View all</a>
                </div>
                @if ($recentDisputes->isEmpty())
                    <p class="mt-4 text-sm text-slate-500">No disputes yet.</p>
                @else
                    <ul class="mt-4 divide-y divide-slate-100">
                        @foreach ($recentDisputes as $dispute)
                            <li class="py-3 flex justify-between items-center gap-2">
                                <div class="min-w-0">
                                    <a href="{{ route('disputes.show', $dispute) }}" class="font-semibold text-slate-900 hover:text-brand-700 truncate block">{{ $dispute->reason }}</a>
                                    <p class="text-xs text-slate-500 truncate">{{ $dispute->project->title }}</p>
                                </div>
                                <x-dispute-status-badge :status="$dispute->status" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="wv-card p-6 animate-fade-up animate-delay-100">
                <h3 class="text-lg font-display font-bold text-slate-900">Recent projects</h3>
                @if ($recentProjects->isEmpty())
                    <p class="mt-4 text-sm text-slate-500">No projects yet.</p>
                @else
                    <ul class="mt-4 divide-y divide-slate-100">
                        @foreach ($recentProjects as $project)
                            <li class="py-3 flex justify-between items-center gap-2">
                                <div class="min-w-0">
                                    <a href="{{ route('projects.show', $project) }}" class="font-semibold text-slate-900 hover:text-brand-700 truncate block">{{ $project->title }}</a>
                                    <p class="text-xs text-slate-500 truncate">{{ $project->client->name }}</p>
                                </div>
                                <x-project-status-badge :status="$project->status" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="wv-card p-6 animate-fade-up">
            <h3 class="text-lg font-display font-bold text-slate-900">Project pipeline</h3>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-xl bg-emerald-50 p-4">
                    <dt class="text-sm text-emerald-700 font-medium">Open</dt>
                    <dd class="mt-1 text-2xl font-display font-bold text-emerald-800">{{ $openProjects }}</dd>
                </div>
                <div class="rounded-xl bg-blue-50 p-4">
                    <dt class="text-sm text-blue-700 font-medium">In progress</dt>
                    <dd class="mt-1 text-2xl font-display font-bold text-blue-800">{{ $activeProjects }}</dd>
                </div>
                <div class="rounded-xl bg-brand-50 p-4">
                    <dt class="text-sm text-brand-700 font-medium">Completed</dt>
                    <dd class="mt-1 text-2xl font-display font-bold text-brand-800">{{ $completedProjects }}</dd>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
