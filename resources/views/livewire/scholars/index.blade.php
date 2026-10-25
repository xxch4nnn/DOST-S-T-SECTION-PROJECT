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
                onclick="{{ $allSelected }} ? 'deselectAll()' : 'selectAll()' "
            >
                @if($allSelected)
                    <i class="ph-fill ph-check-square"></i>
                @else
                    <i class="ph ph-square"></i>
                @endif
            </button>
        </div>

        {{-- Batch Selection Toolbar --}}
        <div class="scholars-batch-bar" hidden>
            <button type="button" class="batch-deselect-btn" onclick="deselectAll()" title="Deselect All">
                <i class="ph ph-x"></i>
            </button>
            <span class="batch-count-label"></span>
            <div class="batch-action-icons">
                {{-- <button type="button" class="batch-icon-btn" title="Print Scholar(s)"><i class="ph ph-printer"></i></button> --}}
                <button type="button" class="batch-icon-btn" title="Download Scholar(s)" wire:click="downloadScholars"><i class="ph ph-download-simple"></i></button>
                <button type="button" class="batch-icon-btn" title="Move Scholar(s)"><i class="ph ph-file-arrow-up"></i></button>
                <button type="button" class="batch-icon-btn" title="Delete Scholar(s)"><i class="ph ph-trash"></i></button>
            </div>
        </div>

        {{-- Year Folders Stack --}}
        <div class="year-folders-stack">
            @foreach($groupedScholars as $year => $scholarsInYear)
                @php
                    $isExpanded = in_array((string)$year, $expandedYears, true);
                    $isSelected = in_array((string)$year, $selectedYears, true);
                @endphp

                <div class="year-folder-item">
                    {{-- <div class="year-folder-header {{ $isSelected ? 'year-folder-header--selected' : '' }} {{ $isExpanded ? 'year-folder-header--expanded' : '' }}" wire:click="toggleYear('{{ $year }}')">
                        <div class="year-folder-tab">
                            <span class="year-folder-title">{{ $year }}</span>
                        </div>
                        <input type="checkbox" class="year-folder-checkbox" onclick="appendScholarYear('{{ $year }}', true)" @checked($isSelected)>
                    </div>  --}}
                
                    <div class="year-folder-header {{ $isExpanded ? 'year-folder-header--expanded' : '' }}" wire:click="toggleYear('{{ $year }}')" data-year-header="{{ $year }}">
                        <div class="year-folder-tab">
                            <span class="year-folder-title">{{ $year }}</span>
                        </div>

                        <!-- Added a matching data attribute to the checkbox -->
                        <input type="checkbox" 
                            class="year-folder-checkbox" 
                            data-year-checkbox="{{ $year }}"
                            onchange="appendScholarYear('{{ $year }}', this.checked)">
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
    let groupedScholars = @js($groupedScholars);
    let selectedAllScholars = false;

    let totalScholarsLoaded = 0;
    let totalScholarsSelected = 0;

    console.log(groupedScholars);
    console.log("Hello world");

    // Values that represent variables that are selected.
    let selectedScholars = {};

    // HELPER FUNCTIONS TO HANDLE THE SELECTION LOGIC
    // ==============================================
    function appendScholarId(year, scholarId, willCache = false) {
        if (!selectedScholars[year].includes(scholarId)){
            selectedScholars[year].push(scholarId);
            if (willCache === false) 
                return;
            localStorage.setItem('selectedScholars', JSON.stringify(selectedScholars));
            if (groupedScholars[year].length === selectedScholars[year].length)
                helpYearSelectedState(year, true);
            updateScholarBatchBar();
            return true;
        } else
            return false;
    }

    function removeScholarId(year, scholarId) {
        if (selectedScholars[year].includes(scholarId)){
            selectedScholars[year].splice(selectedScholars[year].indexOf(scholarId), 1);
            localStorage.setItem('selectedScholars', JSON.stringify(selectedScholars));
            helpYearSelectedState(year, false);
            updateScholarBatchBar();
            return true;
        } else
            return false;
    }

    /**
     * Appends all the scholars in the current scholar year.
     * Calls the appendScholarYear() helper function for each scholar year.
     */
    function appendScholarYear(scholarYear, willCache) {
        if (selectedScholars[scholarYear]){
            let scholarsInYear = groupedScholars[scholarYear];
            
            for (const scholar in scholarsInYear)
                appendScholarId(scholarYear, scholar.id, false);

            helpYearSelectedState(scholarYear, true);
            updateScholarRelatedHTMLElements(willCache);
            return true;
        } else
            return false;
    }

    /**
     * Removes all the scholars in the current scholar year.
     * Calls the removeScholarYear() helper function for each scholar year.
     */
    function removeScholarYear(scholarYear, willCache) {
        if (selectedScholars[scholarYear]){
            selectedScholars[scholarYear] = [];

            if (willCache)
                localStorage.setItem('selectedScholars', JSON.stringify(selectedScholars));

            updateScholarBatchBar();
            return true;
        } else
            return false;
    }

    /**
     * Selects all the scholars in the current scholar year.
     * Calls the appendScholarYear() helper function for each scholar year.
     */
    function selectAll() {
        for (const [key, value] of Object.entries[groupedScholars])
            appendScholarYear(key, false);
        localStorage.setItem('selectedScholars', JSON.stringify(selectedScholars));
        updateScholarBatchBar();
    }

    function deselectAll() {
        for (const [key, value] of Object.entries[groupedScholars])
            removeScholarYear(key, false);
        localStorage.setItem('selectedScholars', JSON.stringify(selectedScholars));
        updateScholarBatchBar();
    }

    function helpYearSelectedState(year, isChecked){
        let folderHeader = document.querySelector(`.year-folder-header[data-year-header="${year}"]`);
        console.log(folderHeader)
        let checkboxElement = folderHeader.querySelector(`.year-folder-checkbox[data-year-checkbox="${year}"]`);
        console.log(checkboxElement)

        if (!folderHeader || !checkboxElement){
            console.error('Unable to find the folder element or checkbox.', folderHeader, checkboxElement);
            return;
        }

        if (isChecked === true) {
            folderHeader.classList.add("year-folder-header--selected");
            checkboxElement.checked = true;
        } else {
            folderHeader.classList.remove("year-folder-header--selected");
            checkboxElement.checked = false;
        }
    }

    function updateScholarBatchBar(){
        const batchBar = document.querySelector('.scholars-batch-bar');
        const batchCountLabel = batchBar.querySelector('.batch-count-label'); 
        
        if (!batchBar || !batchCountLabel){
            console.error('Unable to find scholar batch bar or scholar batch label.', batchBar, batchCountLabel);
            return;
        }

        let scholarCount = 0;
        for (const [year, scholarIds] of Object.entries(selectedScholars)){
            scholarCount += scholarIds.length
        }

        if (scholarCount > 1){
            batchBar.hidden = false;
            batchCountLabel.textContent = `${scholarCount} scholars selected.`;
        } else if (scholarCount === 1){
            batchBar.hidden = false;
            batchCountLabel.textContent = '1 scholar selected.';
        } else if (scholarCount === 0){
            batchBar.hidden = true;
            batchCountLabel.textContent = 'No scholars selected.';
        }
    }

    /**
     * Function that will (FINISH THIS)
     */
    function updateScholarRelatedHTMLElements(willCache){
        if (willCache)
            localStorage.setItem('selectedScholars', JSON.stringify(selectedScholars));

        updateScholarBatchBar();

        for (const [year, scholarIds] of Object.entries(selectedScholars)){
            totalScholarsSelected += selectedScholars[year].length;
            totalScholarsLoaded += groupedScholars[year].length;
        }

        selectedAllScholars = (totalScholarsSelected === totalScholarsLoaded) ? true : false;
    }

    // END OF HELPER FUNCTIONS TO HANDLE THE SELECTION LOGIC
    // ==============================================
    
    let debounceTimer = null;

    if (!$wire.hasEventListener){
        await document.addEventListener('contextmenu', async function (event) {
            const scholar = event.target.closest('.scholar-grid-card');
            const yearFolder = event.target.closest('.year-folder-item');
            if (!scholar) return; // If it's not a scholar card, ignore

            event.preventDefault(); // Stop the native browser right-click menu

            // Gets the information linked to the scholar card selected.
            const scholarId = scholar.getAttribute('data-scholar_id');
            const scholarYear = String(yearFolder.querySelector('.year-folder-title').textContent);
            // console.log(scholarId, scholarYear);

            if (scholar.getAttribute('data-scholar_selected') === 'true') {
                result = removeScholarId(scholarYear, scholarId);
                if (result === false){
                    console.error(`Unable to remove scholar ID ${scholarId} from the list of ${scholarYear} scholars.`);
                    return;
                }

                scholar.setAttribute('data-scholar_selected', 'false');
                scholar.classList.remove('border-2', 'border-accent');
            } else {
                result = appendScholarId(scholarYear, scholarId, true)
                if (result === false){
                    console.error(`Error in appending the scholar ID ${scholarId} to the list of ${scholarYear} scholars.`);
                    return;
                }

                scholar.setAttribute('data-scholar_selected', 'true');
                scholar.classList.add('border-2', 'border-accent');
                console.log(selectedScholars);
            }
        });

        $wire.hasEventListener = true;
    } else {
        console.log('Document event listener for the scholar selection already exists.');
    }

    function loadSelectedScholars(){
        const cachedSelectedScholars = JSON.parse(localStorage.getItem('selectedScholars'));
        if (cachedSelectedScholars && Object.keys(cachedSelectedScholars).length == Object.keys(groupedScholars).length) {
            selectedScholars = cachedSelectedScholars;
        } else {
            for (const [key, value] of Object.entries(groupedScholars)){
                selectedScholars[key] = [];
            }
            localStorage.setItem('selectedScholars', JSON.stringify(selectedScholars));
        }

        console.log('Loaded cache of the scholars successfully!');
        console.log("Cached Scholars: \n", localStorage.getItem('selectedScholars'));
    }

    function updateUIOfSelectedScholarCards(){
        for (const [year, scholarIds] of Object.entries(selectedScholars)){
            if (scholarIds.length === 0)
                continue;

            for (const scholarId of scholarIds){
                const scholarCard = document.querySelector(`.scholar-grid-card[data-scholar_id="${scholarId}"]`);
                if (!scholarCard){
                    console.warn(`Unable to find the scholar card for scholar ID ${scholarId}.`);
                    continue;
                }

                scholarCard.setAttribute('data-scholar_selected', 'true');
                scholarCard.classList.add('border-2', 'border-accent');
            }
        }
    }

    function instantiateElementsAndVariables(){
        loadSelectedScholars();
        updateUIOfSelectedScholarCards();
        updateScholarRelatedHTMLElements(false);
    }

    document.addEventListener('livewire:init', instantiateElementsAndVariables);
    document.addEventListener('livewire:navigated', instantiateElementsAndVariables);
</script>
@endscript