<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="text-body bg-light overflow-hidden-x">
    <div class="min-vh-100 d-flex flex-column align-items-center justify-content-center py-5 position-relative" style="background-color: #f8f9fa; z-index: 1;">
        
        <!-- Slot Container for Auth Page View -->
        <div class="w-100 mx-auto px-3" style="max-width: 26rem; z-index: 2;">
            {{ $slot }}
        </div>

    </div>

    <!-- Inline SVG Folder Bottom Shape -->
    <div class="position-fixed bottom-0 start-0 w-100 overflow-hidden" style="z-index: 0; pointer-events: none;">
        <svg viewBox="0 0 1440 360" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-100 d-block" style="height: auto; max-height: 38vh; min-height: 180px;">
            <!-- Light Main Blue Folder Curve -->
            <path d="M0 260C450 260 650 180 1440 180V360H0V260Z" fill="#54bbff"/>
            <!-- Dark Blue Left Folder Tab -->
            <path d="M0 90C420 90 600 170 780 360H0V90Z" fill="#0066b2"/>
        </svg>
    </div>
</body>
</html>
