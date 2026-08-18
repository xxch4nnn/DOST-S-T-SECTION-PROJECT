<?php

use App\Models\Scholar;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Component;

new class extends Component
{
    public string $query = '';

    /** Empty until backend persistence (#85); no production-visible mock identity. */
    public array $recentSearches = [];

    public function selectScholar(int|string $scholarId): void
    {
        $this->dispatch('open-scholar-drawer', scholarId: $scholarId);
    }

    public function clearRecentSearch(int $id): void
    {
        $this->recentSearches = array_values(array_filter(
            $this->recentSearches,
            fn ($item) => (int) $item['id'] !== $id
        ));
    }

    public function clearAllRecentSearches(): void
    {
        $this->recentSearches = [];
    }

    public function with(): array
    {
        $results = [];

        if (trim($this->query) !== '') {
            $searchTerm = trim($this->query);
            $like = '%'.$searchTerm.'%';

            // Scholar fields + document_versions filename/type (Q09=A / UUID shape).
            $dbResults = Scholar::query()
                ->with(['scholarship', 'scholarshipType', 'clearanceStatus'])
                ->where(function ($q) use ($like) {
                    $q->where('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('middle_name', 'like', $like)
                        ->orWhere('spas_no', 'like', $like)
                        ->orWhere('year_of_award', 'like', $like)
                        ->orWhere('email_address', 'like', $like)
                        ->orWhere('contact_number', 'like', $like)
                        ->orWhereHas('documents', function ($dq) use ($like) {
                            $dq->where('status', 'active')
                                ->whereHas('versions', function ($vq) use ($like) {
                                    $vq->where('original_filename', 'like', $like)
                                        ->orWhere('stored_filename', 'like', $like)
                                        ->orWhereHas('fileType', function ($ft) use ($like) {
                                            $ft->where('name', 'like', $like);
                                        });
                                });
                        });
                })
                ->limit(15)
                ->get();

            foreach ($dbResults as $scholar) {
                $results[] = [
                    'id' => $scholar->id,
                    'last_name' => $scholar->last_name,
                    'first_name' => $scholar->first_name,
                    'spas_no' => $scholar->spas_no ?? '—',
                    'program_type' => $scholar->scholarship->name ?? '—',
                    'program_level' => $scholar->scholarshipType->name ?? ($scholar->program ?? '—'),
                    'status' => $scholar->clearanceStatus->name ?? 'Not Cleared',
                    'status_class' => ($scholar->clearanceStatus->name ?? '') === 'Cleared' ? 'badge-status-cleared' : 'badge-status-not-cleared',
                ];
            }
        }

        return [
            'searchResults' => $results,
        ];
    }
}; ?>

@php
    $hasQuery = trim($query) !== '';
    $hasRecent = count($recentSearches) > 0;
@endphp

<div class="file-search" x-data="{ focused: false }" @click.away="focused = false">
    <div class="file-search__wrapper"
         :class="{ 'file-search__wrapper--has-dropdown': @js($hasQuery) || (focused && @js($hasRecent)) }">

        {{-- Search Input Bar --}}
        <div class="file-search__input-group">
            <i class="ph ph-magnifying-glass file-search__icon"
               :class="{ 'file-search__icon--focused': focused && !@js($hasQuery) }"></i>
            <input wire:model.live.debounce.250ms="query"
                   @focus="focused = true"
                   type="text"
                   class="file-search__input"
                   placeholder="Search Scholar file or Admin File"
                   id="dashboard-search"
                   autocomplete="off" />

            @if(! $hasQuery && $hasRecent)
                <button x-show="focused"
                        wire:click="clearAllRecentSearches"
                        class="file-search__clear-btn"
                        type="button"
                        aria-label="Clear recent search history"
                        x-transition.opacity.duration.200ms
                        style="display: none;">
                    clear history
                </button>
            @endif
        </div>

        {{-- Expanded Search Results List --}}
        <div class="file-search__dropdown"
             x-show="@js($hasQuery) || (focused && @js($hasRecent))"
             x-transition.opacity.duration.200ms
             style="display: none;">

            @if($hasQuery)
                <div class="search-results-list">
                    @forelse($searchResults as $item)
                        <div wire:click="selectScholar({{ $item['id'] }})"
                             class="search-result-item"
                             role="button"
                             tabindex="0">
                            <div class="search-result-item__arrow">
                                <i class="ph ph-arrow-up-right"></i>
                            </div>

                            <div class="search-result-item__name">
                                <span class="fw-bold">{{ $item['last_name'] }},</span>
                                <span class="text-secondary ms-1">{{ $item['first_name'] $item['middle_name']  }}</span>
                            </div>

                            <div class="search-result-item__spas">
                                <span class="fw-bold text-dark me-1">SPAS ID:</span>
                                <span class="text-secondary">{{ $item['spas_no'] }}</span>
                            </div>

                            <div class="search-result-item__program">
                                <span class="fw-bold text-dark me-1">{{ $item['program_type'] }}</span>
                                <span class="text-secondary">{{ $item['program_level'] }}</span>
                            </div>

                            <div class="search-result-item__status">
                                <span class="badge {{ $item['status_class'] }} rounded-pill px-3 py-1.5 fs-7">
                                    {{ $item['status'] }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-3 text-center text-muted small">
                            No scholar or administrative records found.
                        </div>
                    @endforelse
                </div>
            @elseif($hasRecent)
                <div class="search-results-list">
                    @foreach($recentSearches as $item)
                        <div wire:click="selectScholar({{ $item['id'] }})"
                             class="search-result-item search-result-item--recent"
                             role="button"
                             tabindex="0">
                            <div class="search-result-item__arrow search-result-item__arrow--history">
                                <i class="ph ph-clock-counter-clockwise"></i>
                            </div>

                            <div class="search-result-item__name">
                                <span class="fw-bold">{{ $item['last_name'] }},</span>
                                <span class="text-secondary ms-1">{{ $item['first_name'] }}</span>
                            </div>

                            <div class="search-result-item__spas">
                                <span class="fw-bold text-dark me-1">SPAS ID:</span>
                                <span class="text-secondary">{{ $item['spas_no'] }}</span>
                            </div>

                            <div class="search-result-item__program">
                                <span class="fw-bold text-dark me-1">{{ $item['program_type'] }}</span>
                                <span class="text-secondary">{{ $item['program_level'] }}</span>
                            </div>

                            <div class="search-result-item__status">
                                <span class="badge badge-status-history rounded-pill px-3 py-1.5 fs-7">
                                    {{ $item['status'] }}
                                </span>
                            </div>

                            <button wire:click.stop="clearRecentSearch({{ $item['id'] }})"
                                    class="search-result-item__close"
                                    type="button"
                                    aria-label="Remove from recent searches">
                                <i class="ph ph-x"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
