<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="text-body bg-light">
    <div class="min-vh-100 d-flex flex-column">
        <livewire:layout.navigation />

        @if (isset($header))
            <header class="bg-white border-bottom border-primary border-opacity-25 shadow-sm">
                <div class="container py-3">
                    <div class="text-dost-dark-blue">
                        {{ $header }}
                    </div>
                </div>
            </header>
        @endif

        <main class="flex-grow-1 py-4">
            {{ $slot }}
        </main>

        <footer class="border-top bg-white py-3 mt-auto">
            <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span class="small text-muted">{{ config('app.name', 'DOSTorage') }}</span>
                <span class="small text-dost-dark-blue fw-semibold">DOST-SEI Davao Region</span>
            </div>
        </footer>
    </div>
</body>
</html>
