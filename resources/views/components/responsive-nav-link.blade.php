@props(['active'])

@php
$classes = ($active ?? false)
    ? 'nav-link active bg-light rounded'
    : 'nav-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
