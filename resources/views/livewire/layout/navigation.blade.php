<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom" x-data="{ open: false }">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}" wire:navigate>
            <x-application-logo style="height: 2rem; width: auto;" class="text-dark" />
        </a>

        <button class="navbar-toggler" type="button" @click="open = ! open" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" :class="{ 'show': open }">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </li>
                <li class="nav-item">
                    <x-nav-link :href="route('scholars.index')" :active="request()->routeIs('scholars.*')" wire:navigate>
                        {{ __('Scholars') }}
                    </x-nav-link>
                </li>
                <li class="nav-item">
                    <x-nav-link :href="route('admin-records.index')" :active="request()->routeIs('admin-records.*')" wire:navigate>
                        {{ __('Admin Records') }}
                    </x-nav-link>
                </li>
                @can('viewAuditLogs')
                    <li class="nav-item">
                        <x-nav-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')" wire:navigate>
                            {{ __('Audit Log') }}
                        </x-nav-link>
                    </li>
                @endcan
            </ul>

            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item dropdown">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button type="button" class="btn btn-link nav-link dropdown-toggle text-decoration-none">
                                <span x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></span>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile')" wire:navigate>
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <button wire:click="logout" class="dropdown-item text-start border-0 bg-transparent w-100" type="button">
                                {{ __('Log Out') }}
                            </button>
                        </x-slot>
                    </x-dropdown>
                </li>
            </ul>
        </div>
    </div>
</nav>
