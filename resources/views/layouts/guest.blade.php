<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body style="margin: 0; padding: 0; min-height: 100vh; background-color: #f8f9fa; font-family: 'Zalando Sans', system-ui, -apple-system, sans-serif; overflow-x: hidden;">

    <div class="d-flex flex-column min-vh-100">
        {{-- Row 1: Login Form (takes remaining space, centers content) --}}
        <div class="flex-grow-1 d-flex align-items-center justify-content-center">
            <div style="width: 100%; max-width: 26rem; padding: 2rem 1rem;">
                {{ $slot }}
            </div>
        </div>

        {{-- Row 2: Folder Footer Background Component --}}
        <x-folder-background />
    </div>

</body>
</html>

