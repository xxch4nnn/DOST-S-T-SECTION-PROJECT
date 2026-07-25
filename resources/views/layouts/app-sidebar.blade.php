<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'DOSTorage') }}</title>

    {{-- Zalando Sans --}}
    <link rel="stylesheet" href="https://fonts.cdnfonts.com/css/zalando-sans">

    {{-- Self-hosted fallback @font-face --}}
    <style>
        @font-face {
            font-family: 'Zalando Sans';
            font-style: normal;
            font-weight: 100 900;
            font-display: swap;
            src: url('/fonts/zalando-sans/ZalandoSans-Variable.woff2') format('woff2');
        }
    </style>

    {{-- Phosphor Icons --}}
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body style="margin: 0; padding: 0; min-height: 100vh; background-color: #ffffff; font-family: 'Zalando Sans', system-ui, -apple-system, sans-serif; overflow: hidden;">

    <div class="d-flex min-vh-100" style="margin: 0; padding: 0;">
        {{-- Collapsible Sidebar --}}
        <livewire:layout.sidebar />

        {{-- Main Content Area with ONLY top-left and bottom-left rounded corners --}}
        <div class="main-canvas flex-grow-1 d-flex flex-column" style="min-width: 0; height: 100vh; overflow: hidden; background-color: #f8f9fa; border-top-left-radius: 36px; border-bottom-left-radius: 36px; border-top-right-radius: 0; border-bottom-right-radius: 0;">
            <main class="flex-grow-1 overflow-auto">
                {{ $slot }}
            </main>

            {{-- Folder Footer Background --}}
            <x-folder-background />
        </div>
    </div>

</body>
</html>
