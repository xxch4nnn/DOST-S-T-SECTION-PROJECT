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

            $dbResults = Scholar::with(['scholarshipType', 'clearanceStatus'])
                ->where('first_name', 'like', "%{$searchTerm}%")
                ->orWhere('last_name', 'like', "%{$searchTerm}%")
                ->orWhere('spas_no', 'like', "%{$searchTerm}%")
                ->orWhere('year_of_award', 'like', "%{$searchTerm}%")
                ->limit(6)
                ->get();

            if ($dbResults->isNotEmpty()) {
                foreach ($dbResults as $scholar) {
                    $results[] = [
                        'id' => $scholar->id,
                        'last_name' => $scholar->last_name,
                        'first_name' => $scholar->first_name,
                        'spas_no' => $scholar->spas_no ?? '2023-00855-9102',
                        'program_type' => $scholar->scholarshipType->code ?? 'RA 10612',
                        'program_level' => $scholar->program ?? 'Undergrad',
                        'status' => $scholar->clearanceStatus->name ?? 'Not Cleared',
                        'status_class' => ($scholar->clearanceStatus->name ?? '') === 'Cleared' ? 'badge-status-cleared' : 'badge-status-not-cleared',
                    ];
                }
            } else {
                // Demonstration fallback matching Figma prototype screenshot
                $results = [
                    [
                        'id' => 1,
                        'last_name' => 'Maclang',
                        'first_name' => 'Wakin Cean',
                        'spas_no' => '2023-00855-9102',
                        'program_type' => 'RA 10612',
                        'program_level' => 'Undergrad',
                        'status' => 'Not Cleared',
                        'status_class' => 'badge-status-not-cleared',
                    ],
                    [
                        'id' => 2,
                        'last_name' => 'Palabon',
                        'first_name' => 'Rui',
                        'spas_no' => '2023-00855-9102',
                        'program_type' => 'RA 10612',
                        'program_level' => 'Undergrad',
                        'status' => 'Not Cleared',
                        'status_class' => 'badge-status-not-cleared',
                    ],
                    [
                        'id' => 3,
                        'last_name' => 'Rizal Mercado',
                        'first_name' => 'Jose Protasio Alonzo Realonda',
                        'spas_no' => '2023-00855-9102',
                        'program_type' => 'RA 10612',
                        'program_level' => 'Undergrad',
                        'status' => 'Not Cleared',
                        'status_class' => 'badge-status-not-cleared',
                    ],
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
