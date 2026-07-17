@props(['status'])

@php
    $statusColors = [
        'pending' => 'bg-gray-100 text-gray-800',
        'in_progress' => 'bg-blue-100 text-blue-800',
        'submitted' => 'bg-purple-100 text-purple-800',
        'approved' => 'bg-teal-100 text-teal-800',
        'paid' => 'bg-green-100 text-green-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium '.($statusColors[$status] ?? 'bg-gray-100 text-gray-800')]) }}>
    {{ str_replace('_', ' ', ucfirst($status)) }}
</span>
