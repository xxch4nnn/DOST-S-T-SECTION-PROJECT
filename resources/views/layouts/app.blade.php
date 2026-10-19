<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'DOSTorage') }}</title>

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-white">

    <div class="d-flex min-vh-100">
        {{-- Collapsible Sidebar --}}
        <livewire:layout.sidebar />

        {{-- Main Content Area with Background Layering --}}
        <div class="main-canvas flex-grow-1 d-flex flex-column position-relative">
            {{-- Main Content Slot (Z-Index 10 so dropdowns float over background) --}}
            <main class="flex-grow-1 overflow-auto position-relative z-10">
                {{ $slot }}
            </main>

            {{-- Folder Footer Background Graphic (Positioned Absolutely as Background) --}}
            @if(request()->routeIs('dashboard'))
                <div class="folder-bg-wrapper">
                    <x-folder-background />
                </div>
            @endif
        </div>
    </div>

    @livewireScripts

    <!---------------------------------------------------->
    <!-- PLACE THE SWAL SESSION LISTENER HERE            -->
    <!---------------------------------------------------->
    @if (session()->has('swal_success'))
        <script>
        document.addEventListener('DOMContentLoaded', triggerSessionSwal);
        document.addEventListener('livewire:navigated', triggerSessionSwal);

        function triggerSessionSwal() {
            @if (session()->has('swal_success'))
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('swal_success') }}",
                    icon: 'success',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    confirmButtonText: "Close"
                });
            @endif
        }
        </script>
    @endif

    @stack('scripts')

    {{-- Global Right-Corner Alert / Toast Notification System --}}
    <x-notification-toast />

</body>
</html>
