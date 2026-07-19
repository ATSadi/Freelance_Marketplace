<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Client workspace</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Welcome back, {{ explode(' ', $user->name)[0] }}</h2>
                <p class="mt-1 text-sm text-slate-500">Post projects, review proposals, and manage milestone payments.</p>
            </div>
            <a href="{{ route('client.projects.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Post New Project
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <x-profile-dashboard-card :user="$user" />

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card animate-fade-up">
                <div class="flex items-center justify-between">
                    <span class="eyebrow">Total Projects</span>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-4xl font-display font-bold text-slate-900">{{ $projectCount }}</p>
                <p class="mt-1 text-sm text-slate-500">Projects posted</p>
            </div>
            <div class="stat-card animate-fade-up animate-delay-100">
                <div class="flex items-center justify-between">
                    <span class="eyebrow">Active</span>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-4xl font-display font-bold text-slate-900">{{ $activeProjects->count() }}</p>
                <p class="mt-1 text-sm text-slate-500">In progress now</p>
            </div>
            <div class="stat-card animate-fade-up animate-delay-200">
                <div class="flex items-center justify-between">
                    <span class="eyebrow">Quick links</span>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <a href="{{ route('orders.index') }}" class="link-chip bg-slate-50">My Orders</a>
                    <a href="{{ route('client.projects.index') }}" class="link-chip bg-slate-50">Projects</a>
                    <a href="{{ route('transactions.index') }}" class="link-chip bg-slate-50">Transactions</a>
                </div>
            </div>
        </div>

        <div class="wv-card p-6 animate-fade-up">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-display font-bold text-slate-900">Active Projects</h3>
                <a href="{{ route('client.projects.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-800">View all</a>
            </div>

            @if ($activeProjects->isEmpty())
                <div class="mt-6 text-center py-10 rounded-xl border-2 border-dashed border-slate-200">
                    <p class="text-sm text-slate-500">No active projects right now.</p>
                    <a href="{{ route('client.projects.create') }}" class="mt-3 btn-primary">Post your first project</a>
                </div>
            @else
                <ul class="mt-4 divide-y divide-slate-100">
                    @foreach ($activeProjects as $project)
                        <li class="py-4 flex flex-wrap justify-between items-center gap-3">
                            <div>
                                <a href="{{ route('orders.show', $project) }}" class="font-semibold text-slate-900 hover:text-brand-700">{{ $project->title }}</a>
                                @if ($project->freelancer)
                                    <p class="text-xs text-slate-500 mt-0.5">Freelancer: {{ $project->freelancer->name }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('client.projects.milestones.index', $project) }}" class="text-sm font-medium text-slate-500 hover:text-brand-700">Milestones</a>
                                <x-project-status-badge :status="$project->status" />
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>
