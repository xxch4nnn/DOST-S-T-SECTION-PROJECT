<div class="scholars-page">
    {{-- Page Header --}}
    <div class="scholars-page__header">
        <h1>{{ __('Scholars File List') }}</h1>
    </div>

    {{-- Search & Action Controls --}}
    <div class="scholars-search-row">
        <div class="scholars-search-input-group">
            <i class="ph ph-magnifying-glass scholars-search-icon"></i>
            <input 
                wire:model.live.debounce.250ms="search" 
                type="text" 
                class="scholars-search-input" 
                placeholder="Search Scholar file"
            >
        </div>

        @php
            $allSelected = count($allYears) > 0 && count($selectedYears) === count($allYears);
        @endphp

        <button 
            type="button" 
            class="scholars-select-all-btn {{ $allSelected ? 'scholars-select-all-btn--checked' : '' }}" 
            title="{{ $allSelected ? 'Deselect All Years' : 'Select All Years' }}"
            wire:click="{{ $allSelected ? 'deselectAllYears' : 'selectAllYears('.json_encode($allYears).')' }}"
        >
            @if($allSelected)
                <i class="ph-fill ph-check-square"></i>
            @else
                <i class="ph ph-square"></i>
            @endif
        </button>
    </div>


    {{-- Batch Selection Toolbar (Screenshot 2) --}}
    @if(count($selectedYears) > 0)
        <div class="scholars-batch-bar">
            <button type="button" class="batch-deselect-btn" wire:click="deselectAllYears" title="Deselect All">
                <i class="ph ph-x"></i>
            </button>

            <span class="batch-count-label">{{ count($selectedYears) }} Selected</span>

            <div class="batch-action-icons">
                <button type="button" class="batch-icon-btn" title="Print Selected">
                    <i class="ph ph-printer"></i>
                </button>
                <button type="button" class="batch-icon-btn" title="Download Selected">
                    <i class="ph ph-download-simple"></i>
                </button>
                <button type="button" class="batch-icon-btn" title="Export Selected">
                    <i class="ph ph-file-arrow-up"></i>
                </button>
                <button type="button" class="batch-icon-btn batch-icon-btn--danger" title="Delete Selected">
                    <i class="ph ph-trash"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Year Folders Stack --}}
    <div class="year-folders-stack">
        @foreach($groupedScholars as $year => $scholarsInYear)
            @php
                $isExpanded = in_array((string)$year, $expandedYears, true);
                $isSelected = in_array((string)$year, $selectedYears, true);
            @endphp

            <div class="year-folder-item">
                {{-- Folder Header Card --}}
                <div 
                    class="year-folder-header {{ $isSelected ? 'year-folder-header--selected' : '' }} {{ $isExpanded ? 'year-folder-header--expanded' : '' }}"
                    wire:click="toggleYear('{{ $year }}')"
                >
                    <div class="year-folder-tab">
                        <span class="year-folder-title">{{ $year }}</span>
                    </div>

                    <input 
                        type="checkbox" 
                        class="year-folder-checkbox" 
                        wire:click.stop="toggleSelectYear('{{ $year }}')"
                        @checked($isSelected)
                    >
                </div>

                {{-- Folder Body Content (Expanded) --}}
                @if($isExpanded)
                    <div class="year-folder-body">
                        @if($scholarsInYear->isEmpty())
                            <div class="text-center text-muted py-4">
                                <i class="ph ph-folder-open display-6 mb-2 text-secondary"></i>
                                <p class="mb-0 fs-6">No scholars found for year {{ $year }}.</p>
                            </div>
                        @else
                            <div class="scholars-grid">
                                @foreach($scholarsInYear as $scholar)
                                    @php
                                        $statusName = $scholar->clearanceStatus?->name ?? 'Not Cleared';
                                        $isCleared = in_array($statusName, ['Clear', 'Cleared']);
                                    @endphp

                                    <div 
                                        class="scholar-grid-card" 
                                        wire:click="openScholar({{ $scholar->id }})"
                                    >
                                        <div class="scholar-grid-card__header">
                                            <h3 class="scholar-name">
                                                {{ $scholar->last_name }}, {{ $scholar->first_name }} {{ $scholar->middle_name ? substr($scholar->middle_name, 0, 1) . '.' : '' }}
                                            </h3>
                                            <span class="badge {{ $isCleared ? 'badge-status-cleared' : 'badge-status-not-cleared' }}">
                                                {{ $statusName }}
                                            </span>
                                        </div>

                                        <div class="scholar-grid-card__spas">
                                            SPAS ID: {{ $scholar->spas_no }}
                                        </div>

                                        <div class="scholar-grid-card__meta-grid">
                                            <div class="scholar-meta-sub">
                                                <label>Scholarship Program</label>
                                                <span>{{ $scholar->scholarship?->name ?? 'RA 10612' }}</span>
                                            </div>

                                            <div class="scholar-meta-sub">
                                                <label>Scholarship Program Type</label>
                                                <span>{{ $scholar->scholarshipType?->name ?? 'DOST - SEI Undergraduate Scholarship' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Slide-over Drawer & Lightbox Viewer --}}
    <livewire:dashboard.scholar-drawer />
    <livewire:dashboard.document-viewer />
</div>
