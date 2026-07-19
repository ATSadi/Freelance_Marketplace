@props(['type'])

@php
    $map = [
        'escrow_hold' => ['bg-amber-50 text-amber-700 ring-1 ring-amber-600/20', 'bg-amber-500', 'Escrow Hold'],
        'escrow_release' => ['bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20', 'bg-emerald-500', 'Payment Released'],
        'refund' => ['bg-blue-50 text-blue-700 ring-1 ring-blue-600/20', 'bg-blue-500', 'Refund'],
    ];
    [$classes, $dot, $label] = $map[$type] ?? ['bg-slate-100 text-slate-700 ring-1 ring-slate-500/20', 'bg-slate-400', ucfirst(str_replace('_', ' ', $type))];
@endphp

<span {{ $attributes->merge(['class' => 'wv-badge '.$classes]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
    {{ $label }}
</span>
