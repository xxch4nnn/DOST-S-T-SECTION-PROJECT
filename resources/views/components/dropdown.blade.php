@props(['align' => 'right', 'width' => '48', 'contentClasses' => ''])

@php
    $menuAlign = match ($align) {
        'left' => '',
        default => 'dropdown-menu-end',
    };
@endphp

<div class="dropdown" {{ $attributes }}>
    <div data-bs-toggle="dropdown" aria-expanded="false" role="button">
        {{ $trigger }}
    </div>

    <div class="dropdown-menu {{ $menuAlign }} {{ $contentClasses }}">
        {{ $content }}
    </div>
</div>
