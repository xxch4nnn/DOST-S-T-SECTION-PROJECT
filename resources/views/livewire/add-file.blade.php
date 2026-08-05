<div class="add-file-page">
    {{-- External Libraries for Instant Client-Side PDF Preview & Drag-Drop Reorder --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    @if(!$fileType)
        {{-- Screen 1: File Type Selection (Scholar vs Administrative) --}}
        <div class="add-file-page__header flex-column align-items-start mb-4">
            <h1 class="mb-1">Add New File</h1>
            <p class="text-muted m-0" style="font-size: 0.95rem;">Please select the type of file you want to upload</p>
        </div>

        <div class="file-type-grid" style="grid-template-columns: repeat(2, 1fr); max-width: 800px;">
            <div class="file-type-card" wire:click="selectFileType('scholar')">
                <i class="ph ph-student file-type-card__icon"></i>
                <h2 class="file-type-card__label">Scholar File</h2>
            </div>

            <div class="file-type-card" wire:click="selectFileType('admin')">
                <i class="ph ph-folder-notch-open file-type-card__icon"></i>
                <h2 class="file-type-card__label">Administrative File</h2>
            </div>
        </div>
    @elseif($fileType === 'scholar')
        {{-- Screen 2: Scholar File Full Upload Form --}}
        <div class="admin-breadcrumb-bar">
            <button type="button" class="admin-back-btn" wire:click="goBack" title="Back">
                <i class="ph ph-caret-left"></i>
            </button>
            <span class="admin-breadcrumb-title">Add New File</span>
            <span class="admin-breadcrumb-sep">/</span>
            <span class="admin-breadcrumb-active">Scholar File</span>
        </div>

        <form wire:submit="saveScholar" class="add-file-form-stack" id="scholarUploadForm">
            {{-- Section 1: Basic Information --}}
            <div class="add-file-form-card">
                <h3 class="add-file-form-card__title">Basic Information</h3>

                <div class="row g-3">
                    <div class="col-md-3 form-field-group">
                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                        <input wire:model="last_name" type="text" id="last_name" class="form-control-custom" placeholder="e.g. Maclang" required>
                        @error('last_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                        <input wire:model="first_name" type="text" id="first_name" class="form-control-custom" placeholder="e.g. Wakin Cean" required>
                        @error('first_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="middle_name">Middle Name</label>
                        <input wire:model="middle_name" type="text" id="middle_name" class="form-control-custom" placeholder="e.g. Castro">
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="generational_suffix">Suffix</label>
                        <input wire:model="generational_suffix" type="text" id="generational_suffix" class="form-control-custom" placeholder="e.g. Jr., III">
                    </div>
                </div>
            </div>

            {{-- Section 2: Scholarship Details --}}
            <div class="add-file-form-card">
                <h3 class="add-file-form-card__title">Scholarship Details</h3>

                <div class="row g-3">
                    {{-- Row 1: 3 equal columns --}}
                    <div class="col-md-4 form-field-group">
                        <label for="spas_no">SPAS ID No. <span class="text-danger">*</span></label>
                        <input wire:model="spas_no" type="text" id="spas_no" class="form-control-custom" placeholder="e.g. U-2023-00855-2235" required>
                        @error('spas_no') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4 form-field-group">
                        <label for="scholarship_id">Scholarship Program</label>
                        <select wire:model="scholarship_id" id="scholarship_id" class="form-select-custom">
                            <option value="" disabled>Dropdown</option>
                            <option value="">Select Scholarship</option>
                            @foreach($scholarships as $sch)
                                <option value="{{ $sch->id }}">{{ $sch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-field-group">
                        <label for="scholarship_type_id">Program Type</label>
                        <select wire:model="scholarship_type_id" id="scholarship_type_id" class="form-select-custom">
                            <option value="" disabled>Dropdown</option>
                            <option value="">Select Program Type</option>
                            @foreach($scholarshipTypes as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Row 2: 3 equal columns matching Row 1 precisely --}}
                    <div class="col-md-4 form-field-group">
                        <label for="year_of_award">Year of Award <span class="text-danger">*</span></label>
                        <select wire:model="year_of_award" id="year_of_award" class="form-select-custom">
                            @for($y = date('Y'); $y >= 2000; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-4 form-field-group">
                        <label for="school_id">University / School</label>
                        <select wire:model="school_id" id="school_id" class="form-select-custom">
                            <option value="" disabled>Dropdown</option>
                            <option value="">Select School</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }} {{ $school->abbreviation ? "({$school->abbreviation})" : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-field-group">
                        <label for="course_id">Course / Degree Program</label>
                        <select wire:model="course_id" id="course_id" class="form-select-custom">
                            <option value="" disabled>Dropdown</option>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Row 3: 2 equal columns --}}
                    <div class="col-md-6 form-field-group">
                        <label for="clearance_status_id">Clearance Status</label>
                        <select wire:model="clearance_status_id" id="clearance_status_id" class="form-select-custom">
                            <option value="" disabled>Dropdown</option>
                            @foreach($clearanceStatuses as $cs)
                                <option value="{{ $cs->id }}">{{ $cs->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 form-field-group">
                        <label for="clearance_date">Clearance Date</label>
                        <input wire:model="clearance_date" type="date" id="clearance_date" class="form-control-custom">
                    </div>
                </div>
            </div>

            {{-- Section 3: Demographic & Contact Information --}}
            <div class="add-file-form-card">
                <h3 class="add-file-form-card__title">Demographic & Contact Information</h3>

                <div class="row g-3">
                    {{-- Row 1: 4 equal columns --}}
                    <div class="col-md-3 form-field-group">
                        <label for="barangay">Address / Barangay</label>
                        <input wire:model="barangay" type="text" id="barangay" class="form-control-custom" placeholder="e.g. Brgy. 34-D">
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="municipality">Municipality / City</label>
                        <input wire:model="municipality" type="text" id="municipality" class="form-control-custom" placeholder="e.g. Davao City">
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="province">Province</label>
                        <input wire:model="province" type="text" id="province" class="form-control-custom" placeholder="e.g. Davao del Sur">
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="region_id">Region</label>
                        <select wire:model="region_id" id="region_id" class="form-select-custom">
                            <option value="" disabled>Dropdown</option>
                            <option value="">Select Region</option>
                            @foreach($regions as $reg)
                                <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Row 2: 2 equal columns --}}
                    <div class="col-md-6 form-field-group">
                        <label for="birthdate">Birthdate</label>
                        <input wire:model="birthdate" type="date" id="birthdate" class="form-control-custom">
                    </div>

                    <div class="col-md-6 form-field-group">
                        <label for="sex">Sex</label>
                        <select wire:model="sex" id="sex" class="form-select-custom">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Section 4: Upload Scanned Files Extension (Client-Side Instant Previews & Categorized Folders) --}}
            <div class="add-file-form-card">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="add-file-form-card__title mb-1">Upload Scanned Files</h3>
                        <p class="text-muted small mb-0">Organize and upload scholarship documents by folder category with instant thumbnail preview and drag-to-reorder.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">
                        {{ count($scannedCategories) }} {{ count($scannedCategories) === 1 ? 'Category' : 'Categories' }}
                    </span>
                </div>

                <div class="upload-scanned-stack d-flex flex-column gap-4">
                    @foreach($scannedCategories as $catIndex => $cat)
                        <div class="scanned-file-box" wire:key="scanned-category-{{ $cat['id'] }}">
                            {{-- Category Header with Category Dropdown & Custom Rename Field --}}
                            <div class="scanned-file-box__header d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="ph ph-folder-notch-open text-primary fs-5"></i>
                                        <label class="fw-bold text-dark small mb-0 me-1">Folder Category:</label>
                                    </div>

                                    {{-- Predefined Category Dropdown --}}
                                    <select class="scanned-file-box__category-select" 
                                            wire:change="setCategoryType({{ $catIndex }}, $event.target.value)"
                                            value="{{ $cat['selected_type'] ?? 'custom' }}">
                                        @foreach($availableFileTypes as $ft)
                                            <option value="{{ $ft->name }}" @selected(($cat['selected_type'] ?? '') === $ft->name)>
                                                 {{ $ft->name }}
                                            </option>
                                        @endforeach
                                        <option value="custom" @selected(($cat['selected_type'] ?? '') === 'custom')>+ Custom Name / Rename...</option>
                                    </select>

                                    {{-- Editable Name Input --}}
                                    <div class="d-flex align-items-center position-relative">
                                        <input type="text" 
                                               wire:model.live.debounce.300ms="scannedCategories.{{ $catIndex }}.name" 
                                               class="scanned-file-box__editable-label" 
                                               placeholder="Enter folder name..."
                                               title="Rename this category">
                                    </div>
                                </div>

                                {{-- Category Actions (Delete) --}}
                                @if(count($scannedCategories) > 1)
                                    <button type="button" 
                                            wire:click="removeScannedCategory({{ $catIndex }})" 
                                            class="btn btn-outline-danger btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1"
                                            title="Delete Folder Category">
                                        <i class="ph ph-trash"></i> Delete Category
                                    </button>
                                @endif
                            </div>

                            {{-- Hidden File Input for this Category --}}
                            <input type="file" 
                                   id="file_input_{{ $cat['id'] }}" 
                                   data-cat-id="{{ $cat['id'] }}" 
                                   multiple 
                                   accept=".pdf, .jpg, .jpeg, .png, .webp" 
                                   style="display: none;">

                            {{-- Dropzone Area Matching Edit Scholar File Reference --}}
                            <div class="file-upload-dropzone position-relative" 
                                 id="dropzone_{{ $cat['id'] }}"
                                 onclick="document.getElementById('file_input_{{ $cat['id'] }}').click()"
                                 ondragover="event.preventDefault(); this.style.borderColor='var(--dost-main-blue)';"
                                 ondragleave="this.style.borderColor='';"
                                 ondrop="event.preventDefault(); this.style.borderColor=''; const fInput = document.getElementById('file_input_{{ $cat['id'] }}'); if (fInput) { fInput.files = event.dataTransfer.files; fInput.dispatchEvent(new Event('change', { bubbles: true })); }">
                                <i class="ph ph-cloud-arrow-up upload-icon"></i>
                                <div class="upload-text">Click or drag files to add more</div>
                                <div class="upload-hint">Supports PDF, JPG, PNG</div>
                            </div>

                            {{-- Source Files List Container (Client-Side Rendered) --}}
                            <div class="added-files-container mb-3" id="added_files_container_{{ $cat['id'] }}" style="display: none;" wire:ignore>
                                <label style="font-weight: 600; font-size: 0.85rem; color: #495057; margin-bottom: 0.5rem; display: block;">
                                    Source Files (<span id="source_files_count_{{ $cat['id'] }}">0</span>)
                                </label>
                                <div class="added-files-list" id="added_files_list_{{ $cat['id'] }}">
                                    {{-- Staged items rendered dynamically --}}
                                </div>
                            </div>

                            {{-- Document Files & Sort Order Preview Grid (Client-Side Rendered) --}}
                            <div class="preview-grid-wrapper" id="preview_grid_wrapper_{{ $cat['id'] }}" style="display: none;" wire:ignore>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="preview-grid-title m-0">DOCUMENT FILES &amp; SORT ORDER</div>
                                    <small class="text-muted"><i class="ph ph-info me-1"></i>Drag to reorder files</small>
                                </div>
                                <div id="preview_container_{{ $cat['id'] }}" class="preview-container" data-cat-id="{{ $cat['id'] }}">
                                    {{-- Staged preview cards rendered dynamically --}}
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Centered + Add Scanned File Category Button --}}
                    <div class="text-center mt-3">
                        <button type="button" wire:click="addScannedCategory" class="btn-add-category-custom shadow-sm">
                            <i class="ph ph-folder-plus text-primary fs-5"></i> Add Scanned File Category
                        </button>
                    </div>
                </div>
            </div>

            {{-- Form Bottom Action Bar (Discard + Upload File Green Button) --}}
            <div class="form-actions-bar d-flex align-items-center justify-content-end gap-3 mt-4">
                <button type="button" wire:click="discard" class="btn-discard-custom">Discard</button>
                <button type="submit" class="btn-success-custom" id="btnSubmitScholar">
                    <span id="btnSubmitScholarText">Upload File</span>
                </button>
            </div>
        </form>
    @elseif($fileType === 'admin' && !$adminCategory)
        {{-- Screen 3: Administrative Category Selection --}}
        <div class="admin-breadcrumb-bar">
            <button type="button" class="admin-back-btn" wire:click="goBack" title="Back">
                <i class="ph ph-caret-left"></i>
            </button>
            <span class="admin-breadcrumb-title">Add New File</span>
            <span class="admin-breadcrumb-sep">/</span>
            <span class="admin-breadcrumb-active">Administrative File</span>
        </div>

        <div class="file-type-grid">
            @foreach($adminCategories as $cat)
                <div class="file-type-card" wire:click="selectAdminCategory('{{ $cat }}')">
                    <i class="ph ph-folder file-type-card__icon"></i>
                    <h2 class="file-type-card__label">{{ $cat }}</h2>
                </div>
            @endforeach

            <div class="file-type-card file-type-card--dashed">
                <i class="ph ph-plus file-type-card__icon"></i>
                <span class="file-type-card__label">Add New File Type</span>
            </div>
        </div>
    @elseif($fileType === 'admin' && $adminCategory)
        {{-- Screen 4: Administrative File Form --}}
        <div class="admin-breadcrumb-bar">
            <button type="button" class="admin-back-btn" wire:click="goBack" title="Back">
                <i class="ph ph-caret-left"></i>
            </button>
            <span class="admin-breadcrumb-title">Add New File</span>
            <span class="admin-breadcrumb-sep">/</span>
            <span class="admin-breadcrumb-title">Administrative File</span>
            <span class="admin-breadcrumb-sep">/</span>
            <span class="admin-breadcrumb-active">{{ $adminCategory }}</span>
        </div>

        <form wire:submit="saveAdminRecord" class="add-file-form-stack">
            <div class="add-file-form-card">
                <h3 class="add-file-form-card__title">{{ $adminCategory }} Details</h3>

                <div class="row g-3">
                    <div class="col-md-6 form-field-group">
                        <label for="admin_title">Document Title</label>
                        <input wire:model="admin_title" type="text" id="admin_title" class="form-control-custom" placeholder="Enter title...">
                        @error('admin_title') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="admin_series_number">Reference / Series No.</label>
                        <input wire:model="admin_series_number" type="text" id="admin_series_number" class="form-control-custom" placeholder="e.g. MEM-2023-001">
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="admin_year">Fiscal Year</label>
                        <select wire:model="admin_year" id="admin_year" class="form-select-custom">
                            @for($y = date('Y'); $y >= 2015; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-6 form-field-group">
                        <label for="admin_recipient">Recipient / Target Office</label>
                        <input wire:model="admin_recipient" type="text" id="admin_recipient" class="form-control-custom" placeholder="e.g. DOST Regional Office XI">
                    </div>

                    <div class="col-12 form-field-group">
                        <label for="admin_description">Description / Remarks</label>
                        <textarea wire:model="admin_description" id="admin_description" class="form-control-custom" rows="3" placeholder="Enter document details..."></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions-bar">
                <button type="submit" class="btn-submit-custom">Save Administrative File</button>
            </div>
        </form>
    @endif
</div>

@script
<script>
    window.__stagedCategoryFiles = window.__stagedCategoryFiles || {};

    if (typeof window.pdfjsLib !== 'undefined') {
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    function formatFileSize(bytes) {
        if (!bytes || bytes <= 0) return '0 KB';
        if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(1) + ' MB';
        }
        return Math.max(1, Math.round(bytes / 1024)) + ' KB';
    }

    async function generatePdfThumbnail(file) {
        try {
            if (typeof window.pdfjsLib === 'undefined') return null;
            const arrayBuffer = await file.arrayBuffer();
            const pdf = await window.pdfjsLib.getDocument({ data: arrayBuffer }).promise;
            const page = await pdf.getPage(1);
            const viewport = page.getViewport({ scale: 0.8 });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            await page.render({ canvasContext: context, viewport: viewport }).promise;
            return canvas.toDataURL('image/jpeg', 0.85);
        } catch (e) {
            console.warn('PDF thumbnail render warning:', e);
            return null;
        }
    }

    function updateCategoryUI(catId) {
        const staged = window.__stagedCategoryFiles[catId] || [];
        const filesContainer = document.getElementById(`added_files_container_${catId}`);
        const filesList = document.getElementById(`added_files_list_${catId}`);
        const filesCount = document.getElementById(`source_files_count_${catId}`);
        const previewWrapper = document.getElementById(`preview_grid_wrapper_${catId}`);
        const previewContainer = document.getElementById(`preview_container_${catId}`);

        if (!filesContainer || !filesList || !previewWrapper || !previewContainer) return;

        if (staged.length === 0) {
            filesContainer.style.display = 'none';
            previewWrapper.style.display = 'none';
            filesList.innerHTML = '';
            previewContainer.innerHTML = '';
            return;
        }

        filesContainer.style.display = 'block';
        previewWrapper.style.display = 'block';
        if (filesCount) filesCount.textContent = staged.length;

        // Render source files list
        filesList.innerHTML = '';
        staged.forEach((item) => {
            const row = document.createElement('div');
            row.className = 'added-file-item';
            row.dataset.fileId = item.id;

            const infoDiv = document.createElement('div');
            infoDiv.className = 'file-info';

            const icon = document.createElement('i');
            icon.className = item.isPdf ? 'ph ph-file-pdf text-danger fs-5' : 'ph ph-image text-primary fs-5';

            const nameSpan = document.createElement('span');
            nameSpan.className = 'file-name text-truncate';
            nameSpan.title = item.name;
            nameSpan.textContent = item.name;

            const sizeSpan = document.createElement('span');
            sizeSpan.className = 'text-muted small ms-2';
            sizeSpan.textContent = `(${item.sizeFormatted})`;

            infoDiv.appendChild(icon);
            infoDiv.appendChild(nameSpan);
            infoDiv.appendChild(sizeSpan);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn-remove-file';
            removeBtn.title = 'Remove file';
            removeBtn.innerHTML = '<i class="ph ph-trash"></i>';
            removeBtn.onclick = function () {
                removeStagedFile(catId, item.id);
            };

            row.appendChild(infoDiv);
            row.appendChild(removeBtn);
            filesList.appendChild(row);
        });

        // Render preview cards
        previewContainer.innerHTML = '';
        staged.forEach((item, idx) => {
            const card = document.createElement('div');
            card.className = 'preview-card-item';
            card.dataset.fileId = item.id;

            const mediaContainer = document.createElement('div');
            mediaContainer.className = 'preview-media-container';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn-remove-page';
            removeBtn.title = 'Remove file';
            removeBtn.innerHTML = '<i class="ph ph-x"></i>';
            removeBtn.onclick = function () {
                removeStagedFile(catId, item.id);
            };
            mediaContainer.appendChild(removeBtn);

            if (item.previewUrl) {
                const img = document.createElement('img');
                img.src = item.previewUrl;
                img.alt = item.name;
                img.loading = 'lazy';
                mediaContainer.appendChild(img);
            } else if (item.isPdf) {
                const pdfPlaceholder = document.createElement('div');
                pdfPlaceholder.className = 'd-flex flex-column align-items-center justify-content-center h-100 p-2 text-center';
                pdfPlaceholder.innerHTML = `<i class="ph ph-file-pdf text-danger fs-1"></i><span class="small fw-semibold text-muted text-truncate w-100 mt-1" style="font-size: 0.7rem;">${item.name}</span>`;
                mediaContainer.appendChild(pdfPlaceholder);
            } else {
                const filePlaceholder = document.createElement('div');
                filePlaceholder.className = 'd-flex flex-column align-items-center justify-content-center h-100 p-2 text-center';
                filePlaceholder.innerHTML = `<i class="ph ph-file text-primary fs-1"></i><span class="small fw-semibold text-muted text-truncate w-100 mt-1" style="font-size: 0.7rem;">${item.name}</span>`;
                mediaContainer.appendChild(filePlaceholder);
            }

            const meta = document.createElement('div');
            meta.className = 'preview-meta';
            const badge = document.createElement('span');
            badge.className = 'page-badge';
            badge.textContent = `File ${idx + 1}`;
            meta.appendChild(badge);

            card.appendChild(mediaContainer);
            card.appendChild(meta);
            previewContainer.appendChild(card);
        });

        // Initialize Sortable if not already
        if (!previewContainer._sortable && typeof Sortable !== 'undefined') {
            previewContainer._sortable = new Sortable(previewContainer, {
                animation: 250,
                draggable: '.preview-card-item',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function () {
                    const cards = previewContainer.querySelectorAll('.preview-card-item');
                    const orderedIds = Array.from(cards).map(c => c.dataset.fileId);
                    const currentStaged = window.__stagedCategoryFiles[catId] || [];
                    const reordered = [];
                    orderedIds.forEach(id => {
                        const found = currentStaged.find(f => f.id === id);
                        if (found) reordered.push(found);
                    });
                    currentStaged.forEach(f => {
                        if (!reordered.includes(f)) reordered.push(f);
                    });
                    window.__stagedCategoryFiles[catId] = reordered;

                    const badges = previewContainer.querySelectorAll('.page-badge');
                    badges.forEach((b, i) => { b.textContent = `File ${i + 1}`; });
                }
            });
        }
    }

    function removeStagedFile(catId, fileId) {
        if (!window.__stagedCategoryFiles[catId]) return;
        const index = window.__stagedCategoryFiles[catId].findIndex(f => f.id === fileId);
        if (index !== -1) {
            const removed = window.__stagedCategoryFiles[catId].splice(index, 1)[0];
            if (removed && removed.previewUrl && removed.previewUrl.startsWith('blob:')) {
                URL.revokeObjectURL(removed.previewUrl);
            }
        }
        updateCategoryUI(catId);
    }

    async function handleFileSelection(catId, fileList) {
        if (!fileList || fileList.length === 0) return;

        window.__stagedCategoryFiles[catId] = window.__stagedCategoryFiles[catId] || [];
        const filesArray = Array.from(fileList);

        for (const file of filesArray) {
            const fileId = 'staged_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);
            const isPdf = (file.type && file.type.includes('pdf')) || file.name.toLowerCase().endsWith('.pdf');
            const isImage = file.type && file.type.startsWith('image/');
            let previewUrl = null;

            if (isImage) {
                previewUrl = URL.createObjectURL(file);
            }

            const item = {
                id: fileId,
                file: file,
                name: file.name,
                size: file.size,
                sizeFormatted: formatFileSize(file.size),
                mimeType: file.type || (isPdf ? 'application/pdf' : 'application/octet-stream'),
                isPdf: isPdf,
                isImage: isImage,
                previewUrl: previewUrl
            };

            window.__stagedCategoryFiles[catId].push(item);
        }

        // Render UI instantaneously (0ms lag)
        updateCategoryUI(catId);

        // Generate PDF thumbnails asynchronously in the background
        for (const item of window.__stagedCategoryFiles[catId]) {
            if (item.isPdf && !item.previewUrl) {
                generatePdfThumbnail(item.file).then(thumb => {
                    if (thumb) {
                        item.previewUrl = thumb;
                        const card = document.querySelector(`.preview-card-item[data-file-id="${item.id}"]`);
                        if (card) {
                            const media = card.querySelector('.preview-media-container');
                            if (media) {
                                const oldPlaceholder = media.querySelector('img, div');
                                const newImg = document.createElement('img');
                                newImg.src = thumb;
                                newImg.alt = item.name;
                                newImg.loading = 'lazy';
                                if (oldPlaceholder) oldPlaceholder.replaceWith(newImg);
                                else media.appendChild(newImg);
                            }
                        }
                    }
                });
            }
        }
    }

    function initCategoryUploaders() {
        document.querySelectorAll('input[type="file"][data-cat-id]').forEach(input => {
            const catId = input.dataset.catId;

            if (!input._hasUploadListener) {
                input._hasUploadListener = true;
                input.addEventListener('change', function () {
                    handleFileSelection(catId, this.files);
                    this.value = '';
                });
            }

            updateCategoryUI(catId);
        });

        const scholarForm = document.getElementById('scholarUploadForm');
        if (scholarForm && !scholarForm._hasSubmitIntercept) {
            scholarForm._hasSubmitIntercept = true;
            scholarForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const allFiles = [];
                const manifest = [];
                let uploadIdx = 0;

                const categoryBoxes = document.querySelectorAll('.scanned-file-box');
                categoryBoxes.forEach(box => {
                    const catInput = box.querySelector('input[type="file"][data-cat-id]');
                    if (!catInput) return;
                    const catId = catInput.dataset.catId;

                    const catSelect = box.querySelector('.scanned-file-box__category-select');
                    const catNameInput = box.querySelector('.scanned-file-box__editable-label');
                    let catName = 'General Documents';
                    if (catNameInput && catNameInput.value.trim() !== '') {
                        catName = catNameInput.value.trim();
                    } else if (catSelect && catSelect.value && catSelect.value !== 'custom') {
                        catName = catSelect.value;
                    }

                    const staged = window.__stagedCategoryFiles[catId] || [];
                    staged.forEach(item => {
                        allFiles.push(item.file);
                        manifest.push({
                            index: uploadIdx++,
                            cat_id: catId,
                            cat_name: catName,
                            name: item.name,
                            file_size: item.size,
                            mime_type: item.mimeType,
                            is_pdf: item.isPdf,
                            is_image: item.isImage
                        });
                    });
                });

                const submitBtn = document.getElementById('btnSubmitScholar');

                if (allFiles.length === 0) {
                    $wire.saveScholar();
                    return;
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving Scholar & Uploading Documents...';
                }

                $wire.uploadMultiple('pendingUploads', allFiles, () => {
                    $wire.saveScholarWithStagedFiles(manifest);
                }, (err) => {
                    console.error('Upload failed:', err);
                    alert('Upload failed. Please try again.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<span>Upload File</span>';
                    }
                });
            });
        }
    }

    document.addEventListener('livewire:navigated', () => {
        setTimeout(initCategoryUploaders, 100);
    });

    document.addEventListener('livewire:initialized', () => {
        setTimeout(initCategoryUploaders, 100);
    });

    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => {
            setTimeout(initCategoryUploaders, 100);
            const submitBtn = document.getElementById('btnSubmitScholar');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Upload File</span>';
            }
        });
    });
</script>
@endscript
