<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'DOSTorage') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex flex-column">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container">
                <a class="navbar-brand fw-semibold" href="/">{{ config('app.name', 'DOSTorage') }}</a>
                @if (Route::has('login'))
                    <livewire:welcome.navigation />
                @endif
            </div>
        </nav>

        <main class="flex-grow-1 d-flex align-items-center">
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-body p-4 p-md-5 text-center">
                                <h1 class="display-6 fw-bold mb-3">DOSTorage</h1>
                                <p class="lead text-muted mb-4">
                                    Digitized records portal for the DOST Region XI Scholarship Section.
                                    Manage scholar 201 files and administrative documents securely.
                                </p>
                                @if (Route::has('login'))
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        @auth
                                            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
                                            @if (Route::has('register'))
                                                <a href="{{ route('register') }}" class="btn btn-outline-secondary">Register</a>
                                            @endif
                                        @endauth
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-3 text-center text-muted small">
            {{ config('app.name', 'DOSTorage') }}
        </footer>
    </div>
</body>
</html>
