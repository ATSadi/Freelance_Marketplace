@props(['status'])

@php
    $statusColors = [
        'open' => 'bg-green-100 text-green-800',
        'in_progress' => 'bg-blue-100 text-blue-800',
        'completed' => 'bg-gray-100 text-gray-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium '.($statusColors[$status] ?? 'bg-gray-100 text-gray-800')]) }}>
    {{ str_replace('_', ' ', ucfirst($status)) }}
</span>
