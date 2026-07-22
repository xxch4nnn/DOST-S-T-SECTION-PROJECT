@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
])

@php
$maxWidthClass = [
    'sm' => 'modal-sm',
    'md' => '',
    'lg' => 'modal-lg',
    'xl' => 'modal-xl',
    '2xl' => 'modal-xl',
][$maxWidth] ?? '';
@endphp

<div
    x-data="{ show: @js($show) }"
    x-init="$watch('show', value => {
        document.body.classList.toggle('modal-open', !!value);
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    x-cloak
    class="modal fade show d-block"
    tabindex="-1"
    role="dialog"
    aria-modal="true"
    style="background: rgba(0,0,0,.5);"
>
    <div class="modal-dialog {{ $maxWidthClass }} modal-dialog-centered" @click.stop>
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>
