@props(['status'])

@php
    $map = [
        'pending' => ['bg-amber-50 text-amber-700 ring-1 ring-amber-600/20', 'bg-amber-500'],
        'accepted' => ['bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20', 'bg-emerald-500'],
        'rejected' => ['bg-rose-50 text-rose-700 ring-1 ring-rose-600/20', 'bg-rose-500'],
    ];
    [$classes, $dot] = $map[$status] ?? ['bg-slate-100 text-slate-700 ring-1 ring-slate-500/20', 'bg-slate-500'];
@endphp

<span {{ $attributes->merge(['class' => 'wv-badge '.$classes]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
    {{ ucfirst($status) }}
</span>
