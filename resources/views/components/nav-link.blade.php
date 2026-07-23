@props(['active'])

@php
$classes = ($active ?? false)
    ? 'nav-link active fw-semibold text-white border-bottom border-warning border-2'
    : 'nav-link text-white-50';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
