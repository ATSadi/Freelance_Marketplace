@props(['active'])

@php
$classes = ($active ?? false)
            ? 'wv-nav-link wv-nav-link-active'
            : 'wv-nav-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
