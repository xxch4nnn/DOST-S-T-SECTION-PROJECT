<?php

use App\Models\Scholar;
use Livewire\Volt\Component;

new class extends Component
{
    public string $query = '';

    public function selectScholar(int|string $scholarId): void
    {
        $this->dispatch('open-scholar-drawer', scholarId: $scholarId);
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

<div class="file-search">
    <div class="file-search__wrapper {{ !empty($query) ? 'file-search__wrapper--expanded' : '' }}">
        {{-- Search Input Bar --}}
        <div class="file-search__input-group">
            <i class="ph ph-magnifying-glass file-search__icon"></i>
            <input wire:model.live.debounce.250ms="query"
                   type="text"
                   class="file-search__input"
                   placeholder="Search Scholar file or Admin File"
                   id="dashboard-search"
                   autocomplete="off" />
        </div>

        {{-- Expanded Search Results List --}}
        @if(!empty($query))
            <div class="search-results-list">
                @forelse($searchResults as $item)
                    <div wire:click="selectScholar({{ $item['id'] }})"
                         class="search-result-item"
                         role="button">
                        {{-- Arrow Top-Right Icon --}}
                        <div class="search-result-item__arrow">
                            <i class="ph ph-arrow-up-right"></i>
                        </div>

                        {{-- Name --}}
                        <div class="search-result-item__name">
                            <span class="fw-bold">{{ $item['last_name'] }},</span>
                            <span class="text-secondary ms-1">{{ $item['first_name'] }}</span>
                        </div>

                        {{-- SPAS ID --}}
                        <div class="search-result-item__spas">
                            <span class="fw-bold text-dark me-1">SPAS ID:</span>
                            <span class="text-secondary">{{ $item['spas_no'] }}</span>
                        </div>

                        {{-- Program & Level --}}
                        <div class="search-result-item__program">
                            <span class="fw-bold text-dark me-1">{{ $item['program_type'] }}</span>
                            <span class="text-secondary">{{ $item['program_level'] }}</span>
                        </div>

                        {{-- Status Badge --}}
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
        @endif
    </div>
</div>
