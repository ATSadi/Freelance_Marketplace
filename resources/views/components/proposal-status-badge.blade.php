@props(['status'])

@php
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'accepted' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium '.($statusColors[$status] ?? 'bg-gray-100 text-gray-800')]) }}>
    {{ ucfirst($status) }}
</span>
