<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h5 class="fw-bold text-dost-dark-blue mb-3">{{ __('Sign in') }}</h5>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="mb-3 form-check">
            <input wire:model="form.remember" id="remember" type="checkbox" class="form-check-input" name="remember">
            <label for="remember" class="form-check-label">{{ __('Remember me') }}</label>
        </div>

        <div class="d-flex align-items-center justify-content-end gap-2">
            @if (Route::has('password.request'))
                <a class="small text-decoration-underline text-muted" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div>
