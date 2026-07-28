<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public bool $expanded = false;
    public string $activeItem = '';
    public bool $isHome = false;

    public function mount(): void
    {
        $this->isHome = request()->routeIs('dashboard');
        $this->setActiveFromRoute();
    }

    public function setActive(string $item): void
    {
        $this->activeItem = $item;
    }

    private function setActiveFromRoute(): void
    {
        if (request()->routeIs('dashboard')) {
            $this->activeItem = 'home';
        } elseif (request()->routeIs('scholars.*')) {
            $this->activeItem = 'scholars';
        } elseif (request()->routeIs('admin-records.*')) {
            $this->activeItem = 'admin-records';
        } elseif (request()->routeIs('add-file.*')) {
            $this->activeItem = 'add-file';
        } elseif (request()->routeIs('profile')) {
            $this->activeItem = 'profile';
        } else {
            $this->activeItem = 'home';
        }
    }

    /**
     * Toggle sidebar expanded/collapsed state.
     */
    public function toggle(): void
    {
        $this->expanded = ! $this->expanded;
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav class="sidebar {{ $expanded ? '' : 'sidebar--collapsed' }}">
    
    <div class="sidebar-separator"></div>

    {{-- Toggle Button / Logo Slot --}}
    @if (! $expanded && ! $isHome)
        {{-- Collapsed state on non-Home pages: Show DOST Logo by default, hover to reveal Open Sidebar toggle icon --}}
        <button wire:click="toggle" class="sidebar-item sidebar-toggle sidebar-logo-toggle" type="button" title="Open Sidebar">
            <img src="{{ asset('DostSEILogo.svg') }}" alt="DOST SEI Logo" class="sidebar-collapsed-logo" />
            <i class="ph ph-sidebar sidebar-icon sidebar-hover-icon"></i>
            <span class="sidebar-label">Open Sidebar</span>
            <span class="sidebar-tooltip">Open Sidebar</span>
        </button>
    @else
        {{-- Expanded state OR Collapsed state on Home page --}}
        <button wire:click="toggle" class="sidebar-item sidebar-toggle" type="button" title="{{ $expanded ? 'Close Sidebar' : 'Open Sidebar' }}">
            <i class="ph ph-sidebar sidebar-icon"></i>
            <span class="sidebar-label">{{ $expanded ? 'Close Sidebar' : 'Open Sidebar' }}</span>
            <span class="sidebar-tooltip">{{ $expanded ? 'Close Sidebar' : 'Open Sidebar' }}</span>
        </button>
    @endif

    <div class="sidebar-separator"></div>

    {{-- Top Group --}}
    <a href="{{ route('dashboard') }}" wire:navigate
       wire:click="setActive('home')"
       class="sidebar-item {{ $activeItem === 'home' || request()->routeIs('dashboard') ? 'sidebar-item--active' : '' }}"
       title="Home">
        <i class="ph ph-house sidebar-icon"></i>
        <span class="sidebar-label">Home</span>
        <span class="sidebar-tooltip">Home</span>
    </a>

    <a href="#" wire:click.prevent="setActive('notifications')"
       class="sidebar-item {{ $activeItem === 'notifications' ? 'sidebar-item--active' : '' }}"
       title="Notifications">
        <i class="ph ph-bell sidebar-icon"></i>
        <span class="sidebar-label">Notifications</span>
        <span class="sidebar-tooltip">Notifications</span>
    </a>

    {{-- Middle Group (visual separator) --}}
    <div class="sidebar-separator"></div>

    <a href="#" wire:click.prevent="setActive('analytics')"
       class="sidebar-item {{ $activeItem === 'analytics' ? 'sidebar-item--active' : '' }}"
       title="Analytics">
        <i class="ph ph-chart-line-up sidebar-icon"></i>
        <span class="sidebar-label">Analytics</span>
        <span class="sidebar-tooltip">Analytics</span>
    </a>

    <a href="{{ route('scholars.index') }}" wire:navigate
       wire:click="setActive('scholars')"
       class="sidebar-item {{ $activeItem === 'scholars' || request()->routeIs('scholars.*') ? 'sidebar-item--active' : '' }}"
       title="Scholars List">
        <i class="ph ph-graduation-cap sidebar-icon"></i>
        <span class="sidebar-label">Scholars List</span>
        <span class="sidebar-tooltip">Scholars List</span>
    </a>

    <a href="{{ route('admin-records.index') }}" wire:navigate
       wire:click="setActive('admin-records')"
       class="sidebar-item {{ $activeItem === 'admin-records' || request()->routeIs('admin-records.*') ? 'sidebar-item--active' : '' }}"
       title="Administrative Files">
        <i class="ph ph-file-text sidebar-icon"></i>
        <span class="sidebar-label">Administrative Files</span>
        <span class="sidebar-tooltip">Administrative Files</span>
    </a>

    {{-- Actions Group (visual separator) --}}
    <div class="sidebar-separator"></div>

    <a href="{{ route('add-file.index') }}" wire:navigate
       wire:click="setActive('add-file')"
       class="sidebar-item {{ $activeItem === 'add-file' || request()->routeIs('add-file.*') ? 'sidebar-item--active' : '' }}"
       title="Add File">
        <i class="ph ph-file-plus sidebar-icon"></i>
        <span class="sidebar-label">Add File</span>
        <span class="sidebar-tooltip">Add File</span>
    </a>

    <a href="#" wire:click.prevent="setActive('edit-requirements')"
       class="sidebar-item {{ $activeItem === 'edit-requirements' ? 'sidebar-item--active' : '' }}"
       title="Edit File Requirements">
        <i class="ph ph-note-pencil sidebar-icon"></i>
        <span class="sidebar-label">Edit File Requirements</span>
        <span class="sidebar-tooltip">Edit File Requirements</span>
    </a>

    {{-- Bottom Spacer + Profile --}}
    <div class="mt-auto"></div>

    <a href="{{ route('profile') }}" wire:navigate
       wire:click="setActive('profile')"
       class="sidebar-item {{ $activeItem === 'profile' || request()->routeIs('profile') ? 'sidebar-item--active' : '' }}"
       title="Admin Profile">
        <i class="ph ph-user sidebar-icon"></i>
        <span class="sidebar-label">Admin Profile</span>
        <span class="sidebar-tooltip">Admin Profile</span>
    </a>
</nav>

