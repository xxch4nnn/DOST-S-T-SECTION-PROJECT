<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 overflow-x-hidden">

    <div class="d-flex flex-column min-vh-100">
        {{-- Row 1: Login Form (takes remaining space, centers content) --}}
        <div class="flex-grow-1 d-flex align-items-center justify-content-center">
            <div class="guest-form-container">
                {{ $slot }}
            </div>
        </div>

        {{-- Row 2: Folder Footer Background Component --}}
        <x-folder-background />
    </div>

</body>
</html>

