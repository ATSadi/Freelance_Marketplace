<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Freelancer workspace</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-slate-900">My Proposals</h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-flash-status />

        <div class="wv-card overflow-hidden">
            @if ($proposals->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-slate-500">You have not submitted any proposals yet.</p>
                    <a href="{{ route('freelancer.projects.browse') }}" class="mt-4 btn-primary">Browse open projects</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Project</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Amount</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Duration</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Submitted</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($proposals as $proposal)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-5 py-4">
                                        <a href="{{ route('projects.show', $proposal->project) }}" class="font-semibold text-slate-900 hover:text-brand-700">{{ $proposal->project->title }}</a>
                                        <p class="text-xs text-slate-500">{{ $proposal->project->client->name }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-slate-700">${{ number_format($proposal->proposed_amount, 2) }}</td>
                                    <td class="px-5 py-4 text-sm text-slate-700">{{ $proposal->proposed_duration_days }} days</td>
                                    <td class="px-5 py-4"><x-proposal-status-badge :status="$proposal->status" /></td>
                                    <td class="px-5 py-4 text-sm text-slate-500">{{ $proposal->created_at->format('M j, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
