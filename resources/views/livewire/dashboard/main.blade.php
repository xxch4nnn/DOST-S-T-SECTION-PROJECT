<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $activeCategory = 'scholarship';
    public string $searchQuery = '';

    /**
     * Handle category tab change.
     */
    #[On('category-changed')]
    public function onCategoryChanged(string $category): void
    {
        $this->activeCategory = $category;
    }

    /**
     * Handle search query update.
     */
    #[On('search-updated')]
    public function onSearchUpdated(string $query): void
    {
        $this->searchQuery = $query;
    }
}; ?>

<div class="dashboard-main">
    {{-- Logo & Brand --}}
    <div class="dashboard-main__header">
        <img src="{{ asset('DostSEILogo.svg') }}" alt="DOST SEI Logo" class="dashboard-main__logo">
        <h1 class="dashboard-main__title">DOSTorage</h1>
    </div>

    {{-- Category Toggle Tabs --}}
    <livewire:dashboard.category-tabs />

    {{-- Search Bar --}}
    <livewire:dashboard.file-search />

    {{-- Content Area (placeholder for future file grid) --}}
    <div class="dashboard-main__content">
        {{-- Future: file results grid will render here based on $activeCategory and $searchQuery --}}
    </div>
</div>
