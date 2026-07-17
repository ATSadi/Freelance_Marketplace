@props(['status'])

@php
    $statusColors = [
        'open' => 'bg-red-100 text-red-800',
        'under_review' => 'bg-amber-100 text-amber-800',
        'resolved' => 'bg-green-100 text-green-800',
        'dismissed' => 'bg-gray-100 text-gray-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium '.($statusColors[$status] ?? 'bg-gray-100 text-gray-800')]) }}>
    {{ str_replace('_', ' ', ucfirst($status)) }}
</span>
