@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-lg ps-3 pe-4 py-2 text-start text-base font-semibold text-brand-700 bg-brand-50 transition'
            : 'block w-full rounded-lg ps-3 pe-4 py-2 text-start text-base font-medium text-slate-600 hover:text-brand-700 hover:bg-brand-50 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
