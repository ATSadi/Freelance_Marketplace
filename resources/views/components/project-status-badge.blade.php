@props(['status'])

@php
    $map = [
        'open' => ['bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20', 'bg-emerald-500'],
        'in_progress' => ['bg-blue-50 text-blue-700 ring-1 ring-blue-600/20', 'bg-blue-500'],
        'completed' => ['bg-brand-50 text-brand-700 ring-1 ring-brand-600/20', 'bg-brand-500'],
        'cancelled' => ['bg-rose-50 text-rose-700 ring-1 ring-rose-600/20', 'bg-rose-500'],
    ];
    [$classes, $dot] = $map[$status] ?? ['bg-slate-100 text-slate-700 ring-1 ring-slate-500/20', 'bg-slate-500'];
@endphp

<span {{ $attributes->merge(['class' => 'wv-badge '.$classes]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
    {{ str_replace('_', ' ', ucfirst($status)) }}
</span>
