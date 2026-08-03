<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'DOSTorage') }}</title>

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body style="margin: 0; padding: 0; min-height: 100vh; background-color: #ffffff; font-family: 'Zalando Sans', system-ui, -apple-system, sans-serif; overflow: hidden;">

    <div class="d-flex min-vh-100" style="margin: 0; padding: 0;">
        {{-- Collapsible Sidebar --}}
        <livewire:layout.sidebar />

        {{-- Main Content Area with Background Layering --}}
        <div class="main-canvas flex-grow-1 d-flex flex-column position-relative" style="min-width: 0; height: 100vh; overflow: hidden; background-color: #f4f6fa; border-top-left-radius: 36px; border-bottom-left-radius: 36px; border-top-right-radius: 0; border-bottom-right-radius: 0;">
            {{-- Main Content Slot (Z-Index 10 so dropdowns float over background) --}}
            <main class="flex-grow-1 overflow-auto position-relative" style="z-index: 10;">
                {{ $slot }}
            </main>

            {{-- Folder Footer Background Graphic (Positioned Absolutely as Background) --}}
            @if(request()->routeIs('dashboard'))
                <div style="position: absolute; bottom: 0; left: 0; width: 100%; z-index: 1; pointer-events: none; line-height: 0;">
                    <x-folder-background />
                </div>
            @endif
        </div>
    </div>

</body>
</html>
