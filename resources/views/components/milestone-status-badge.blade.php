@props(['status'])

@php
    $map = [
        'pending' => ['bg-slate-100 text-slate-700 ring-1 ring-slate-500/20', 'bg-slate-400'],
        'in_progress' => ['bg-blue-50 text-blue-700 ring-1 ring-blue-600/20', 'bg-blue-500'],
        'submitted' => ['bg-violet-50 text-violet-700 ring-1 ring-violet-600/20', 'bg-violet-500'],
        'approved' => ['bg-teal-50 text-teal-700 ring-1 ring-teal-600/20', 'bg-teal-500'],
        'paid' => ['bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20', 'bg-emerald-500'],
    ];
    [$classes, $dot] = $map[$status] ?? ['bg-slate-100 text-slate-700 ring-1 ring-slate-500/20', 'bg-slate-400'];
@endphp

<span {{ $attributes->merge(['class' => 'wv-badge '.$classes]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
    {{ str_replace('_', ' ', ucfirst($status)) }}
</span>
