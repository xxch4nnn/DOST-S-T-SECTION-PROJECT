<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="text-body">
    <div class="min-vh-100 d-flex flex-column align-items-center justify-content-center bg-light py-4">
        <div class="mb-3">
            <a href="/" wire:navigate>
                <x-application-logo style="width: 5rem; height: 5rem;" class="text-secondary" />
            </a>
        </div>

        <div class="w-100 mx-auto p-4 bg-white shadow-sm rounded" style="max-width: 28rem;">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
