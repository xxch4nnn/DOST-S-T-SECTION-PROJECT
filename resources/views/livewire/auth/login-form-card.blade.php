<div>
    <!-- Logo & Title Header -->
    <div class="text-center mb-4">
        <img src="{{ asset('DostSEILogo.svg') }}" alt="DOST SEI Logo" class="img-fluid mb-2" style="height: 3.8rem; width: auto;">
        <h1 class="fw-extrabold text-dark tracking-tight mb-0" style="font-size: 2.6rem; font-family: 'Zalando Sans', system-ui, sans-serif;">DOSTorage</h1>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form wire:submit="login">
        <!-- Employee ID / Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold text-dark mb-1" style="font-size: 0.925rem;">Employee ID</label>
            <input wire:model="form.email" id="email" type="email" name="email" class="form-control form-control-lg bg-white border-secondary-subtle fs-6 rounded-3" placeholder="DOSTSEI-12345" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
        </div>

        <!-- Password with Eye Toggle -->
        <div class="mb-4" x-data="{ showPassword: false }">
            <label for="password" class="form-label fw-semibold text-dark mb-1" style="font-size: 0.925rem;">Password</label>
            <div class="position-relative">
                <input wire:model="form.password" id="password"
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        class="form-control form-control-lg bg-white border-secondary-subtle fs-6 rounded-3 pe-5"
                        placeholder="••••••••••••••••"
                        required autocomplete="current-password" />
                <button type="button"
                        @click="showPassword = !showPassword"
                        class="btn btn-link text-muted position-absolute end-0 top-50 translate-middle-y pe-3 text-decoration-none border-0 bg-transparent shadow-none"
                        tabindex="-1">
                    <!-- Eye Open Icon -->
                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                    </svg>
                    <!-- Eye Slash Icon -->
                    <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16" style="display: none;">
                        <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755a11 11 0 0 1-1.64 1.348zm-2.162-2.162a3.5 3.5 0 0 1-4.882-4.882l5.882 5.882zM2.51 3.864l1.41 1.41C2.695 6.44 1.83 7.55 1.173 8a13 13 0 0 0 1.66 2.043C4.12 11.332 5.88 12.5 8 12.5c1.135 0 2.2-.33 3.141-.893l1.442 1.442.707-.707L3.217 3.157zm4.33 4.33L8.68 9.385A2.5 2.5 0 0 1 7 7.5c0-.397.094-.77.26-1.101z"/>
                        <path d="M13.646 14.354l-12-12 .708-.708 12 12z"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
        </div>

        <!-- Log In Button -->
        <button type="submit" class="btn btn-info w-100 py-2.5 text-white fw-bold fs-6 rounded-3 shadow-sm" style="background-color: #0099ff; border: none;">
            {{ __('Log in') }}
        </button>
    </form>
</div>
