@props(['type'])

@php
    $colors = [
        'escrow_hold' => 'bg-amber-100 text-amber-800',
        'escrow_release' => 'bg-green-100 text-green-800',
        'refund' => 'bg-blue-100 text-blue-800',
    ];

    $labels = [
        'escrow_hold' => 'Escrow Hold',
        'escrow_release' => 'Payment Released',
        'refund' => 'Refund',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium '.($colors[$type] ?? 'bg-gray-100 text-gray-800')]) }}>
    {{ $labels[$type] ?? ucfirst(str_replace('_', ' ', $type)) }}
</span>
