<div class="admin-files-page">
    @if(!$selectedCategory)
        {{-- Screen 1: Administrative File List (Category Folder Overview Stack) --}}
        <div class="admin-files-page__header">
            <h1>{{ __('Administrative File List') }}</h1>
        </div>

        <div class="admin-category-stack">
            @foreach($categories as $category)
                <div class="admin-category-card" wire:click="selectCategory('{{ $category }}')">
                    <h2 class="admin-category-card__title">{{ $category }}</h2>
                    <span class="admin-category-card__count">Category Folder</span>
                </div>
            @endforeach
        </div>
    @else
        {{-- Screen 2-5: Category Detail View with Breadcrumbs --}}
        <div class="admin-breadcrumb-bar">
            <button type="button" class="admin-back-btn" wire:click="clearCategory" title="Back to Category List">
                <i class="ph ph-caret-left"></i>
            </button>
            <span class="admin-breadcrumb-title">Administrative Files</span>
            <span class="admin-breadcrumb-sep">/</span>
            <span class="admin-breadcrumb-active">{{ $selectedCategory }}</span>
        </div>

        {{-- Search & Select-All Controls --}}
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
                wire:click="{{ $allSelected ? 'deselectAll' : 'selectAllYears('.json_encode($allYears).')' }}"
            >
                @if($allSelected)
                    <i class="ph-fill ph-check-square"></i>
                @else
                    <i class="ph ph-square"></i>
                @endif
            </button>
        </div>

        {{-- Batch Selection Toolbar (Screenshots 2 & 4) --}}
        @php
            $totalSelected = count($selectedYears) + count($selectedCards);
        @endphp

        @if($totalSelected > 0)
            <div class="scholars-batch-bar">
                <button type="button" class="batch-deselect-btn" wire:click="deselectAll" title="Deselect All">
                    <i class="ph ph-x"></i>
                </button>

                <span class="batch-count-label">{{ $totalSelected }} Selected</span>

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

        {{-- Year Folders Accordion Stack --}}
        <div class="year-folders-stack">
            @foreach($groupedRecords as $year => $recordsInYear)
                @php
                    $isExpanded = in_array((string)$year, $expandedYears, true);
                    $isSelected = in_array((string)$year, $selectedYears, true);
                @endphp

                <div class="year-folder-item">
                    {{-- Year Folder Header Card --}}
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

                    {{-- Folder Body Content (Expanded Grid - Screenshots 3 & 4) --}}
                    @if($isExpanded)
                        <div class="year-folder-body">
                            <div class="scholars-grid">
                                {{-- Render Records or Sample Grid Cards --}}
                                @php
                                    $cards = $recordsInYear->isEmpty() ? collect([
                                        (object)[
                                            'id' => 1,
                                            'title' => 'Maclang, Wakin Cean C.',
                                            'series_number' => '2023-00855-2235',
                                            'program' => 'RA 10612',
                                            'program_type' => 'DOST - SEI Undergraduate Scholarship',
                                            'status' => 'Not Cleared'
                                        ],
                                        (object)[
                                            'id' => 2,
                                            'title' => 'Maclang, Wakin Cean C.',
                                            'series_number' => '2023-00855-2235',
                                            'program' => 'RA 10612',
                                            'program_type' => 'DOST - SEI Undergraduate Scholarship',
                                            'status' => 'Not Cleared'
                                        ],
                                        (object)[
                                            'id' => 3,
                                            'title' => 'Maclang, Wakin Cean C.',
                                            'series_number' => '2023-00855-2235',
                                            'program' => 'RA 10612',
                                            'program_type' => 'DOST - SEI Undergraduate Scholarship',
                                            'status' => 'Not Cleared'
                                        ],
                                        (object)[
                                            'id' => 4,
                                            'title' => 'Maclang, Wakin Cean C.',
                                            'series_number' => '2023-00855-2235',
                                            'program' => 'RA 10612',
                                            'program_type' => 'DOST - SEI Undergraduate Scholarship',
                                            'status' => 'Not Cleared'
                                        ],
                                        (object)[
                                            'id' => 5,
                                            'title' => 'Maclang, Wakin Cean C.',
                                            'series_number' => '2023-00855-2235',
                                            'program' => 'RA 10612',
                                            'program_type' => 'DOST - SEI Undergraduate Scholarship',
                                            'status' => 'Not Cleared'
                                        ],
                                        (object)[
                                            'id' => 6,
                                            'title' => 'Maclang, Wakin Cean C.',
                                            'series_number' => '2023-00855-2235',
                                            'program' => 'RA 10612',
                                            'program_type' => 'DOST - SEI Undergraduate Scholarship',
                                            'status' => 'Not Cleared'
                                        ],
                                    ]) : $recordsInYear;
                                @endphp

                                @foreach($cards as $card)
                                    @php
                                        $cardId = $card->id;
                                        $isCardSelected = in_array($cardId, $selectedCards, true);
                                    @endphp

                                    <div 
                                        class="admin-file-card {{ $isCardSelected ? 'admin-file-card--selected' : '' }}" 
                                        wire:click="openRecord({{ $cardId }})"
                                    >
                                        <div class="admin-file-card__header">
                                            <h3 class="admin-file-name">{{ $card->title }}</h3>
                                            <span class="badge badge-status-not-cleared">
                                                {{ $card->status ?? 'Not Cleared' }}
                                            </span>
                                        </div>

                                        <div class="admin-file-card__series">
                                            SPAS ID: {{ $card->series_number ?? $card->spas_no }}
                                        </div>

                                        <div class="admin-file-card__meta-grid">
                                            <div class="admin-meta-sub">
                                                <label>Scholarship Program</label>
                                                <span>{{ $card->program ?? 'RA 10612' }}</span>
                                            </div>

                                            <div class="admin-meta-sub">
                                                <label>Scholarship Program Type</label>
                                                <span>{{ $card->program_type ?? 'DOST - SEI Undergraduate Scholarship' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Administrative File Slide-Over Drawer & Lightbox Viewer --}}
    <livewire:admin-records.file-drawer />
    <livewire:dashboard.document-viewer />
</div>
