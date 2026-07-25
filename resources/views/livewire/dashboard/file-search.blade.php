<?php

use Livewire\Volt\Component;

new class extends Component
{
    public string $query = '';

    /**
     * Called when the search query changes (debounced).
     */
    public function updatedQuery(): void
    {
        $this->dispatch('search-updated', query: $this->query);
    }
}; ?>

<div class="file-search">
    <div class="file-search__wrapper">
        <i class="ph ph-magnifying-glass file-search__icon"></i>
        <input wire:model.live.debounce.300ms="query"
               type="text"
               class="file-search__input"
               placeholder="Search Scholar file or Admin File"
               id="dashboard-search" />
    </div>
</div>
