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
    <div class="min-vh-100 d-flex flex-column align-items-center justify-content-center py-5" style="background: linear-gradient(135deg, var(--blue-50) 0%, #ffffff 100%);">
        <div class="mb-4 text-center">
            <a href="/" wire:navigate class="text-decoration-none">
                <x-application-logo style="width: 4.5rem; height: 4.5rem;" class="text-primary mb-2" />
                <h4 class="fw-bold text-dost-dark-blue mb-0">DOSTorage V1</h4>
                <small class="text-muted">Scholarship & Administrative Management System</small>
            </a>
        </div>

        <div class="w-100 mx-auto p-4 bg-white shadow-sm rounded-3 border-top border-4 border-primary" style="max-width: 26rem;">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
