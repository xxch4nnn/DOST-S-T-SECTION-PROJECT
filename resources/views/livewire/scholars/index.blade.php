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

            <button 
                type="button" 
                class="scholars-select-all-btn">
               <i id="selectAllIcon" class="ph ph-square"></i>
            </button>
        </div>

        {{-- Batch Selection Toolbar --}}
        <div class="scholars-batch-bar" hidden>
            <button type="button" class="batch-deselect-btn" title="Deselect All">
                <i class="ph ph-x"></i>
            </button>
            <span class="batch-count-label"></span>
            <div class="batch-action-icons">
                <button type="button" class="batch-icon-btn" title="Print Scholar(s)" hidden><i class="ph ph-printer"></i></button>
                <button type="button" class="batch-icon-btn" title="Download Scholar(s)"><i class="ph ph-download-simple"></i></button>
                <button type="button" class="batch-icon-btn" title="Move Scholar(s)"><i class="ph ph-file-arrow-up"></i></button>
                <button type="button" class="batch-icon-btn" title="Delete Scholar(s)"><i class="ph ph-trash"></i></button>
            </div>
        </div>

        {{-- Year Folders Stack --}}
        <div class="year-folders-stack">
            @foreach($groupedScholars as $year => $scholarsInYear)
                @php
                    $isExpanded = in_array((string)$year, $expandedYears, true);
                @endphp

                <div class="year-folder-item">
                    <div class="year-folder-header {{ $isExpanded ? 'year-folder-header--expanded' : '' }}" wire:click="toggleYear('{{ $year }}')" data-year-header="{{ $year }}">
                        <div class="year-folder-tab">
                            <span class="year-folder-title">{{ $year }}</span>
                        </div>

                        <!-- Added a matching data attribute to the checkbox -->
                        <input type="checkbox" 
                            class="year-folder-checkbox" 
                            data-year-checkbox="{{ $year }}">
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
                                            data-scholar_selected="false"
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
    let scholarCardsRendered = document.querySelectorAll('.scholar-grid-card').length;

    console.log(groupedScholars);
    console.log("Hello world");

    // Values that represent variables that are selected.
    let selectedScholars = {};

    // HELPER FUNCTIONS TO HANDLE THE SELECTION LOGIC
    // ==============================================
    function appendScholarId(year, scholarId, willCache = false, updateHTMLElements = true) {
        if (!selectedScholars[year].includes(scholarId)){
            selectedScholars[year].push(scholarId);
            if (groupedScholars[year].length === selectedScholars[year].length) 
                helpYearSelectedState(year, true);
            if (updateHTMLElements === true && willCache === true)
                updateScholarRelatedHTMLElements(willCache);
            return true;
        } else
            return false;
    }

    function removeScholarId(year, scholarId) {
        if (selectedScholars[year].includes(scholarId)){
            selectedScholars[year].splice(selectedScholars[year].indexOf(scholarId), 1);
            helpYearSelectedState(year, false);
            updateScholarRelatedHTMLElements(true);
            return true;
        } else
            return false;
    }

    function handleFolderCheckbox(scholarYear){
        const checkboxElement = document.querySelector(`.year-folder-checkbox[data-year-checkbox="${scholarYear}"]`);
        
        if (!checkboxElement){
            console.error("Unable to find the checkbox element. Please refresh the page.", checkboxElement)
            return;
        }

        if (!checkboxElement.checked)
            removeScholarYear(scholarYear);
        else
            appendScholarYear(scholarYear);
    }

    /**
     * Appends all the scholars in the current scholar year.
     */
    function appendScholarYear(scholarYear) {
        if (selectedScholars[scholarYear]){
            // console.log(groupedScholars[scholarYear]);
            for (const [year, scholar] of Object.entries(groupedScholars[scholarYear])){
                // console.log('Appended another scholar:', scholar.id);
                appendScholarId(scholarYear, scholar.id, false);
            }

            // console.log(groupedScholars, `Scholars under the year ${scholarYear}: `, groupedScholars[scholarYear], groupedScholars[scholarYear][0].id);

            helpYearSelectedState(scholarYear, true);
            updateScholarRelatedHTMLElements(true);
            return true;
        } else
            return false;
    }

    /**
     * Removes all the scholars in the current scholar year.
     * Calls the removeScholarYear() helper function for each scholar year.
     */
    function removeScholarYear(scholarYear) {
        if (selectedScholars[scholarYear]){
            selectedScholars[scholarYear] = [];

            helpYearSelectedState(scholarYear, false);
            updateScholarRelatedHTMLElements(true);
            return true;
        } else
            return false;
    }

    /**
     * Selects all the scholars in the current scholar year.
     * Calls the appendScholarYear() helper function for each scholar year.
     */
    function selectAll() {
        for (const [key, value] of Object.entries(groupedScholars))
            appendScholarYear(key, false);
        helpAllSelectedState(true);
        updateScholarRelatedHTMLElements(true);
    }

    function deselectAll () {
        for (const [key, value] of Object.entries(groupedScholars))
            removeScholarYear(key, false);
        helpAllSelectedState(false);
        updateScholarRelatedHTMLElements(true);
    }

    function handleSelectAllBtn(){
        if (selectedAllScholars)
            deselectAll();
        else
            selectAll();
    }

    // END OF HELPER FUNCTIONS TO HANDLE THE SELECTION LOGIC

    // ======================================
    //  FUNCTIONS TO UPDATE THE RELATED HTML ELEMENTS WITH SELECTING A SCHOLAR
    // ======================================

    /**
     * Helper function to set the folder to selected or not,
     * Called by the appendScholarId() and appendScholarYear() function to control the frontend look of a folder being selected.
     */
    function helpYearSelectedState(year, isChecked = null){
        let folderHeader = document.querySelector(`.year-folder-header[data-year-header="${year}"]`);
        // console.log(folderHeader)
        let checkboxElement = folderHeader.querySelector(`.year-folder-checkbox[data-year-checkbox="${year}"]`);
        // console.log(checkboxElement)

        if (!folderHeader || !checkboxElement){
            console.error('Unable to find the folder element or checkbox.', folderHeader, checkboxElement);
            return;
        }

        if (checkboxElement.checked === undefined || checkboxElement.checked === null)
            checkboxElement.checked = isChecked === true;
        else if (isChecked === null)
            isChecked = !checkboxElement.checked;

        if (isChecked === true) {
            folderHeader.classList.add("year-folder-header--selected");
            checkboxElement.checked = true;
        } else {
            folderHeader.classList.remove("year-folder-header--selected");
            checkboxElement.checked = false;
        }
    }

    function helpAllSelectedState(isChecked = null){
        const selectAllBtn = document.querySelector('.scholars-select-all-btn');
        const selectAllIcon = selectAllBtn.querySelector('#selectAllIcon');

        if (!selectAllBtn || !selectAllIcon){
            console.error('Unable to find select all button or icon.', selectAllBtn, selectAllIcon);
            return;
        }

        // console.log('The checkbox is currently ', selectAllBtn.checked);

        // Instantiates the value of the checked attribute of the selectAllBtn.
        if (selectAllBtn.checked === null || selectAllBtn.checked === undefined)
            selectAllBtn.checked = isChecked = true;
        else if (isChecked === null)
            isChecked = !selectAllBtn.checked;
        else 
            selectAllBtn.checked = isChecked;

        // console.log('Updated select all button\'s checked status to:', selectAllBtn.checked, 'and the value of isChecked is', isChecked);

        if (isChecked === true){
            selectAllBtn.classList.add('scholars-select-all-btn--checked');
            selectAllBtn.title = 'Deselect All Years';
            selectAllBtn.checked = true;
            selectAllIcon.classList.remove('ph-square');
            selectAllIcon.classList.add('ph-check-square');
        } else {
            selectAllBtn.classList.remove('scholars-select-all-btn--checked');
            selectAllBtn.title = 'Select All Years';
            selectAllBtn.checked = false;
            selectAllIcon.classList.remove('ph-check-square');
            selectAllIcon.classList.add('ph-square');
        }

        console.log('Function is now finished.');
    }

    function updateScholarBatchBar(){
        const batchBar = document.querySelector('.scholars-batch-bar');
        const batchCountLabel = batchBar.querySelector('.batch-count-label'); 
        
        if (!batchBar || !batchCountLabel){
            console.error('Unable to find scholar batch bar or scholar batch label.', batchBar, batchCountLabel);
            return;
        }

        console.log('Preparing the batch bar.', totalScholarsSelected);

        if (totalScholarsSelected > 1){
            batchBar.hidden = false;
            batchCountLabel.textContent = `${totalScholarsSelected} scholars selected.`;
        } else if (totalScholarsSelected === 1){
            batchBar.hidden = false;
            batchCountLabel.textContent = '1 scholar selected.';
        } else if (totalScholarsSelected === 0){
            batchBar.hidden = true;
            batchCountLabel.textContent = 'No scholars selected.';
        }
    }

    function updateSelectAllButton(){
        const selectAllBtn = document.querySelector('.scholars-select-all-btn');
        
        if (!selectAllBtn){
            console.error('Unable to find select all button.');
            return;
        }

        if (selectedAllScholars){
            selectAllBtn.classList.add('scholars-select-all-btn--checked');
            selectAllBtn.title = 'Deselect All Years';

        } else {
            selectAllBtn.classList.remove('scholars-select-all-btn--checked');
            selectAllBtn.title = 'Select All Years';
        }
    }

    /**
     * Function that will (FINISH THIS)
     */
    function updateScholarRelatedHTMLElements(willCache){
        if (willCache)
            localStorage.setItem('selectedScholars', JSON.stringify(selectedScholars));

        totalScholarsLoaded = totalScholarsSelected = 0;
        for (const [year, scholarIds] of Object.entries(selectedScholars)){
            totalScholarsSelected += selectedScholars[year].length;
            totalScholarsLoaded += groupedScholars[year].length;
        }

        updateScholarBatchBar();

        selectedAllScholars = (totalScholarsSelected === totalScholarsLoaded) ? true : false;

        updateUIOfSelectedScholarCards();
        updateSelectAllButton();
    }

    // ==============================================
    // FUNCTIONS TO HANDLE THE SCHOLAR CARD & YEAR FOLDER
    // ==============================================
    
    let debounceTimer = null;

    if (!$wire.hasEventListener){
        document.addEventListener('contextmenu', async function (event) {
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

        // document.addEventListener('onchange', function(event){
        //     console.log('Test');
        //     updateUIOfSelectedScholarCards();
        // });

        $wire.hasEventListener = true;
    } else {
        console.log('Document event listener for the scholar selection already exists.');
    }

    function loadSelectedScholars(){
        // Instantiates the selectedScholarsKey
        for (const [key, value] of Object.entries(groupedScholars)){
            selectedScholars[key] = [];
        }

        const cachedSelectedScholars = JSON.parse(localStorage.getItem('selectedScholars'));
        
        // Checks if there is an existing cache of selected scholars
        if (cachedSelectedScholars && Object.keys(cachedSelectedScholars).length == Object.keys(groupedScholars).length) {
            for (const [year, scholarIds] of Object.entries(cachedSelectedScholars)){
                for (const scholarId of scholarIds){
                    appendScholarId(year, scholarId, false, false);
                }
            }
        } else {
            localStorage.setItem('selectedScholars', JSON.stringify(selectedScholars));
        }

        console.log('Loaded cache of the scholars successfully!');
        console.log("Cached Scholars: \n", localStorage.getItem('selectedScholars'));
    }

    function updateUIOfSelectedScholarCards(){
        // Resets the UI of all loaded scholar grid cards.
        for (const scholarGridCard of document.querySelectorAll(`.scholar-grid-card`)){
            scholarGridCard.setAttribute('data-scholar_selected', 'false');
            scholarGridCard.classList.remove('border-2', 'border-accent');
        }

        for (const [year, scholarIds] of Object.entries(selectedScholars)){
            // console.log(`Scholar IDs for the year ${year}: `, scholarIds);

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

    /**
     * Initializes the HTML Document Object Model (DOM) event listeners for the scholar selection feature.
     */
    function initializeHTMLListeners(){
        const selectAllButton = document.querySelector('.scholars-select-all-btn');

        if (!selectAllButton)
            console.error('Unable to find the select all button.')
        else {
            selectAllButton.addEventListener('click', function(event){
                handleSelectAllBtn();
            })
        }

        // Handles the deselect all button found in the batch bar.
        const deselectAllButton = document.querySelector('.batch-deselect-btn')
        if (!deselectAllButton)
            console.error('Unable to find the deselect all button.')
        else {
            deselectAllButton.addEventListener('click', function(event){
                deselectAll();
            })
        }

        // Method to update the selected cards after opening a folder.
        for (const folderHeader of document.querySelectorAll('.year-folder-header')){
            folderHeader.addEventListener('click', function(event){
                let scholarYear = folderHeader.getAttribute('data-year-header')
                
                // How to add a checker or wait logic that checks if the HTML code was rendered?

                updateScholarRelatedHTMLElements();
                // updateUIOfSelectedScholarCards();
            });
        }

        // Initialize event listeners for all scholar year folders.
        for (const folderCheckbox of document.querySelectorAll('.year-folder-checkbox')){
            folderCheckbox.addEventListener('click', function(event){
                event.stopPropagation();
                console.log('Scholar Year Folder Checkbox pressed.')
                const scholarYear = this.getAttribute('data-year-checkbox');
                handleFolderCheckbox(scholarYear);
            })
        }

        const printScholarBtn = document.querySelector('.batch-icon-btn[title="Print Scholar(s)"]');
        if (!printScholarBtn)
            console.error('Unable to find the print button.', printScholarBtn);
        else {
            printScholarBtn.addEventListener('click', function(event){
                $wire.printScholars(getCompressedScholarsSelected());
            })
        }

        const downloadScholarBtn = document.querySelector('.batch-icon-btn[title="Download Scholar(s)"]');
        if (!downloadScholarBtn)
            console.error('Unable to find the download button.', downloadScholarBtn);
        else {
            downloadScholarBtn.addEventListener('click', function(event){
                $wire.downloadScholars(getCompressedScholarsSelected());
            })
        }

        const moveScholarBtn = document.querySelector('.batch-icon-btn[title="Move Scholar(s)"]');
        if (!moveScholarBtn)
            console.error('Unable to find the move button.', moveScholarBtn);
        else {
            moveScholarBtn.addEventListener('click', function(event){
                $wire.moveScholars(getCompressedScholarsSelected());
            })
        }

        const deleteScholarBtn = document.querySelector('.batch-icon-btn[title="Delete Scholar(s)"]');
        if (!deleteScholarBtn)
            console.error('Unable to find the delete button.', deleteScholarBtn);
        else {
            deleteScholarBtn.addEventListener('click', function(event){
                $wire.deleteScholars(getCompressedScholarsSelected());
            })
        }
    }

    function instantiateElementsAndVariables(){
        initializeHTMLListeners();
        loadSelectedScholars();
        updateScholarRelatedHTMLElements(false);
    }

    document.addEventListener('livewire:init', instantiateElementsAndVariables);
    document.addEventListener('livewire:navigated', instantiateElementsAndVariables);

    // ==============================================
    // FUNCTIONS TO MANIPULATE THE SELECTED SCHOLAR ID GLOBAL ARRAY.
    // ==============================================

    function getCompressedScholarsSelected(){
        var compressedScholarIds = [];
        for (const [scholarYear, scholarIds] of Object.entries(selectedScholars)){
            for (const scholarId of scholarIds)
                compressedScholarIds.push(scholarId);
        }

        return compressedScholarIds;
    }
</script>
@endscript