<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public bool $expanded = true;

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

    {{-- Toggle Button --}}
    <button wire:click="toggle" class="sidebar-item sidebar-toggle" type="button" title="{{ $expanded ? 'Close Sidebar' : 'Open Sidebar' }}">
        <i class="ph ph-sidebar sidebar-icon"></i>
        <span class="sidebar-label">{{ $expanded ? 'Close Sidebar' : 'Open Sidebar' }}</span>
    </button>

    <div class="sidebar-separator"></div>

    {{-- Top Group --}}
    <a href="{{ route('dashboard') }}" wire:navigate
       class="sidebar-item {{ request()->routeIs('dashboard') ? 'sidebar-item--active' : '' }}"
       title="Home">
        <i class="ph ph-house sidebar-icon"></i>
        <span class="sidebar-label">Home</span>
    </a>

    <a href="#" class="sidebar-item" title="Notifications">
        <i class="ph ph-bell sidebar-icon"></i>
        <span class="sidebar-label">Notifications</span>
    </a>

    {{-- Middle Group (visual separator) --}}
    <div class="sidebar-separator"></div>

    <a href="#" class="sidebar-item" title="Analytics">
        <i class="ph ph-chart-line-up sidebar-icon"></i>
        <span class="sidebar-label">Analytics</span>
    </a>

    <a href="{{ route('scholars.index') }}" wire:navigate
       class="sidebar-item {{ request()->routeIs('scholars.*') ? 'sidebar-item--active' : '' }}"
       title="Scholars List">
        <i class="ph ph-graduation-cap sidebar-icon"></i>
        <span class="sidebar-label">Scholars List</span>
    </a>

    <a href="{{ route('admin-records.index') }}" wire:navigate
       class="sidebar-item {{ request()->routeIs('admin-records.*') ? 'sidebar-item--active' : '' }}"
       title="Administrative Files">
        <i class="ph ph-file-text sidebar-icon"></i>
        <span class="sidebar-label">Administrative Files</span>
    </a>

    {{-- Actions Group (visual separator) --}}
    <div class="sidebar-separator"></div>

    <a href="#" class="sidebar-item" title="Add File">
        <i class="ph ph-file-plus sidebar-icon"></i>
        <span class="sidebar-label">Add File</span>
    </a>

    <a href="#" class="sidebar-item" title="Edit File Requirements">
        <i class="ph ph-note-pencil sidebar-icon"></i>
        <span class="sidebar-label">Edit File Requirements</span>
    </a>

    {{-- Bottom Spacer + Profile --}}
    <div class="mt-auto"></div>

    <a href="{{ route('profile') }}" wire:navigate
       class="sidebar-item {{ request()->routeIs('profile') ? 'sidebar-item--active' : '' }}"
       title="Admin Profile">
        <i class="ph ph-user sidebar-icon"></i>
        <span class="sidebar-label">Admin Profile</span>
    </a>
</nav>
