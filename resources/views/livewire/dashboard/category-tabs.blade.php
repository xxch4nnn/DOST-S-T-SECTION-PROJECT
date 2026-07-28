<?php

use Livewire\Volt\Component;

new class extends Component
{
    public string $activeCategory = 'scholarship';

    /**
     * Set the active category tab.
     */
    public function setCategory(string $category): void
    {
        $this->activeCategory = $category;
        $this->dispatch('category-changed', category: $category);
    }
}; ?>

<div class="category-tabs">
    <button wire:click="setCategory('scholarship')"
            class="category-tab {{ $activeCategory === 'scholarship' ? 'category-tab--active' : '' }}"
            type="button">
        Scholarship Files
    </button>
    <button wire:click="setCategory('administrative')"
            class="category-tab {{ $activeCategory === 'administrative' ? 'category-tab--active' : '' }}"
            type="button">
        Administrative Files
    </button>
</div>
