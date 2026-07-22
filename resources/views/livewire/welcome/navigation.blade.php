<nav class="d-flex align-items-center gap-2 ms-auto">
    @auth
        <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-outline-primary">
            Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">
            Log in
        </a>

        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="btn btn-sm btn-primary">
                Register
            </a>
        @endif
    @endauth
</nav>
