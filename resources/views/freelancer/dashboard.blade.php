<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Freelancer workspace</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">Welcome back, {{ explode(' ', $user->name)[0] }}</h2>
                <p class="mt-1 text-sm text-slate-500">Find work, submit proposals, and deliver milestones.</p>
            </div>
            <a href="{{ route('freelancer.projects.browse') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                Browse Projects
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <x-profile-dashboard-card :user="$user" />

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card animate-fade-up">
                <div class="flex items-center justify-between">
                    <span class="eyebrow">Proposals</span>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-4xl font-display font-bold text-slate-900">{{ $proposalCount }}</p>
                <p class="mt-1 text-sm text-slate-500">Submitted total</p>
            </div>
            <div class="stat-card animate-fade-up animate-delay-100">
                <div class="flex items-center justify-between">
                    <span class="eyebrow">Active</span>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-4xl font-display font-bold text-slate-900">{{ $activeProjects->count() }}</p>
                <p class="mt-1 text-sm text-slate-500">Projects underway</p>
            </div>
            <div class="stat-card animate-fade-up animate-delay-200">
                <span class="eyebrow">Quick links</span>
                <div class="mt-3 flex flex-wrap gap-2">
                    <a href="{{ route('orders.index') }}" class="link-chip bg-slate-50">My Orders</a>
                    <a href="{{ route('wallet.index') }}" class="link-chip bg-slate-50">Wallet</a>
                    <a href="{{ route('freelancer.proposals.index') }}" class="link-chip bg-slate-50">Proposals</a>
                </div>
            </div>
        </div>

        <div class="wv-card p-6 animate-fade-up">
            <h3 class="text-lg font-display font-bold text-slate-900">Active Projects</h3>

            @if ($activeProjects->isEmpty())
                <div class="mt-6 text-center py-10 rounded-xl border-2 border-dashed border-slate-200">
                    <p class="text-sm text-slate-500">No active projects yet. Win a proposal to get started.</p>
                    <a href="{{ route('freelancer.projects.browse') }}" class="mt-3 btn-primary">Find work</a>
                </div>
            @else
                <ul class="mt-4 divide-y divide-slate-100">
                    @foreach ($activeProjects as $project)
                        <li class="py-4 flex flex-wrap justify-between items-center gap-3">
                            <div>
                                <a href="{{ route('orders.show', $project) }}" class="font-semibold text-slate-900 hover:text-brand-700">{{ $project->title }}</a>
                                <p class="text-xs text-slate-500 mt-0.5">Client: {{ $project->client->name }}</p>
                            </div>
                            <x-project-status-badge :status="$project->status" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>
