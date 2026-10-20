<x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">
            {{ __('Scholars Directory') }}
        </h2>
        <a href="{{ route('scholars.create') }}" class="btn btn-info text-white px-4 py-2 rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center">
            <i class="ph ph-user-plus me-2 fs-5"></i> Add Scholar
        </a>
    </div>
</x-slot>

<div class="container py-4">
    <div class="scholars-page">
        {{-- Page Header --}}
        <div class="scholars-page__header d-flex justify-content-between align-items-center mb-4">
            <h1>{{ __('Scholars File List') }}</h1>
            <a href="{{ route('scholars.create') }}" class="btn btn-info text-white px-4 py-2 rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center">
                <i class="ph ph-user-plus me-2 fs-5"></i> Add Scholar
            </a>
        </div>

        {{-- Search & Action Controls --}}
        <div class="scholars-search-row">
            <div class="scholars-search-input-group">
                <i class="ph ph-magnifying-glass scholars-search-icon"></i>
                <input 
                    wire:model.live.debounce.250ms="search" 
                    type="text" 
                    class="scholars-search-input" 
                    placeholder="Search Scholar"
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

        {{-- Batch Selection Toolbar --}}
        @if(count($selectedYears) > 0 || count($selectedScholarIds) > 0)
            <div class="scholars-batch-bar">
                <button type="button" class="batch-deselect-btn" wire:click="deselectAll" title="Deselect All">
                    <i class="ph ph-x"></i>
                </button>
                <span class="batch-count-label">{{ count($selectedScholarIds) }} 
                    @if(count($selectedScholarIds) === 1)
                        Scholar selected
                    @else
                        Scholars selected
                    @endif
                </span>
                <div class="batch-action-icons">
                    {{-- <button type="button" class="batch-icon-btn" title="Print Scholar(s)"><i class="ph ph-printer"></i></button> --}}
                    <button type="button" class="batch-icon-btn" title="Download Scholar(s)" wire:click="downloadScholars"><i class="ph ph-download-simple"></i></button>
                    <button type="button" class="batch-icon-btn" title="Move Scholar(s)"><i class="ph ph-file-arrow-up"></i></button>
                    <button type="button" class="batch-icon-btn" title="Delete Scholar(s)"><i class="ph ph-trash"></i></button>
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
                    <div class="year-folder-header {{ $isSelected ? 'year-folder-header--selected' : '' }} {{ $isExpanded ? 'year-folder-header--expanded' : '' }}" wire:click="toggleYear('{{ $year }}')">
                        <div class="year-folder-tab">
                            <span class="year-folder-title">{{ $year }}</span>
                        </div>
                        <input type="checkbox" class="year-folder-checkbox" wire:click.stop="toggleSelectYear('{{ $year }}', {{ $scholarsInYear }})" @checked($isSelected)>
                    </div>

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

                                        {{-- 
                                        data-scholar_selected: A boolean to determine if the scholar was selected (similar to how GDrives folders are selected by single clicking or right clicking on it). When it is true, it will display the batch action toolbar at the top of the page.

                                        openScholar: Calls on to the scholar-drawer.blade file's openDrawer() function to display the side panel of the scholar's information.
                                        --}}
                                        <div class="scholar-grid-card 
                                            @if(in_array($scholar->id, $selectedScholarIds)) 
                                                border-2 border-accent 
                                            @endif"
                                            data-scholar_id="{{ $scholar->id }}"
                                            data-scholar_selected="{{ in_array($scholar->id, $selectedScholarIds) ? 'true' : 'false' }}"
                                            wire:click="openScholar({{ $scholar->id }})">
                                            <div class="scholar-grid-card__header">
                                                <h3 class="scholar-name">
                                                    {{ $scholar->last_name }}, {{ $scholar->first_name }} {{ $scholar->middle_name ? substr($scholar->middle_name, 0, 1) . '.' : '' }} {{ $scholar->generational_suffix }}
                                                </h3>
                                                <span class="badge {{ $isCleared ? 'badge-status-cleared' : 'badge-status-not-cleared' }}">{{ $statusName }}</span>
                                            </div>

                                            <div class="scholar-grid-card__spas">
                                                SPAS ID: {{ $scholar->spas_no ?? $scholar->spas_no }}
                                            </div>

                                            <div class="scholar-grid-card__meta-grid">
                                                <div class="scholar-meta-sub">
                                                    <label>Scholarship Program</label>
                                                    <span>{{ $scholar->scholarshipProgram?->name ?? 'RA 10612' }}</span>
                                                </div>
                                                <div class="scholar-meta-sub">
                                                    <label>Scholarship Program Type</label>
                                                    <span>{{ $scholar->scholarshipProgramType?->name ?? 'DOST - SEI Undergraduate Scholarship' }}</span>
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
</div>

@script
<script>
    let debounceTimer = null;

    await document.addEventListener('contextmenu', async function (event) {
        const scholar = event.target.closest('.scholar-grid-card');
        if (!scholar) return; // If it's not a scholar card, ignore

        event.preventDefault(); // Stop the native browser right-click menu

        const scholarId = scholar.getAttribute('data-scholar_id');
        
        // Fetch current array from Livewire
        let selectedIds = await $wire.get('selectedScholarIds') || [];

        if (scholar.getAttribute('data-scholar_selected') === 'true') {
            // Deselect
            scholar.setAttribute('data-scholar_selected', 'false');
            scholar.classList.remove('border-2', 'border-accent');
            
            // Reassign a filtered array (Safe for Livewire proxy tracking)
            selectedIds = selectedIds.filter(id => id !== scholarId);
        } else {
            // Select
            scholar.setAttribute('data-scholar_selected', 'true');
            scholar.classList.add('border-2', 'border-accent');
            
            // Push and reassign
            if (!selectedIds.includes(scholarId)) {
                selectedIds.push(scholarId);
            }
        }

        if (debounceTimer != null)
            clearTimeout(debounceTimer);

        // Sync the brand new array back to Livewire securely
        debounceTimer = setTimeout(async () => {
            await $wire.set('selectedScholarIds', selectedIds);
            console.log('Synced final set of IDs: ' + selectedIds);
        }, 300)
    });
</script>
@endscript