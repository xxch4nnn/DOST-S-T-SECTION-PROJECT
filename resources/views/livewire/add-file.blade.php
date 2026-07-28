<div class="add-file-page">
    @if(!$fileType)
        {{-- Screen 1: File Type Selection --}}
        <div class="add-file-page__header">
            <h1>{{ __('Add New File') }}</h1>
        </div>

        <div class="file-type-grid">
            {{-- Scholar File Card --}}
            <div class="file-type-card" wire:click="selectFileType('scholar')">
                <i class="ph ph-graduation-cap file-type-card__icon"></i>
                <h2 class="file-type-card__label">Scholar File</h2>
            </div>

            {{-- Administrative File Card --}}
            <div class="file-type-card" wire:click="selectFileType('admin')">
                <i class="ph ph-file-text file-type-card__icon"></i>
                <h2 class="file-type-card__label">Administrative File</h2>
            </div>

            {{-- Dashed + Add New File Type Card --}}
            <div class="file-type-card file-type-card--dashed">
                <i class="ph ph-plus file-type-card__icon"></i>
                <span class="file-type-card__label">Add New File Type</span>
            </div>
        </div>
    @elseif($fileType === 'scholar')
        {{-- Screen 2: Scholarship File Form --}}
        <div class="admin-breadcrumb-bar">
            <button type="button" class="admin-back-btn" wire:click="goBack" title="Back">
                <i class="ph ph-caret-left"></i>
            </button>
            <span class="admin-breadcrumb-title">Add New File</span>
            <span class="admin-breadcrumb-sep">/</span>
            <span class="admin-breadcrumb-active">Scholarship File</span>
        </div>

        <form wire:submit="saveScholar" class="add-file-form-stack">
            {{-- Section 1: Scholar's Name --}}
            <div class="add-file-form-card">
                <h3 class="add-file-form-card__title">Scholar's Name</h3>
                
                <div class="row g-3">
                    <div class="col-md-3 form-field-group">
                        <label for="last_name">Last Name</label>
                        <input wire:model="last_name" type="text" id="last_name" class="form-control-custom" placeholder="Placeholder">
                        @error('last_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="first_name">First Name</label>
                        <input wire:model="first_name" type="text" id="first_name" class="form-control-custom" placeholder="Placeholder">
                        @error('first_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="middle_name">Middle Name</label>
                        <input wire:model="middle_name" type="text" id="middle_name" class="form-control-custom" placeholder="Placeholder">
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="generational_suffix">Suffix <span class="optional-tag">Optional</span></label>
                        <input wire:model="generational_suffix" type="text" id="generational_suffix" class="form-control-custom" placeholder="Placeholder">
                    </div>
                </div>
            </div>

            {{-- Section 2: Scholarship Information --}}
            <div class="add-file-form-card">
                <h3 class="add-file-form-card__title">Scholarship Information</h3>

                <div class="row g-3">
                    <div class="col-md-3 form-field-group">
                        <label for="spas_no">SPAS-ID</label>
                        <input wire:model="spas_no" type="text" id="spas_no" class="form-control-custom" placeholder="Placeholder">
                        @error('spas_no') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="year_of_award">Year Award</label>
                        <select wire:model="year_of_award" id="year_of_award" class="form-select-custom">
                            @for($y = date('Y'); $y >= 2015; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="scholarship_id">Scholarship Program</label>
                        <select wire:model="scholarship_id" id="scholarship_id" class="form-select-custom">
                            <option value="">Dropdown</option>
                            @foreach($scholarships as $sch)
                                <option value="{{ $sch->id }}">{{ $sch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="scholarship_type_id">Scholarship Program Type</label>
                        <select wire:model="scholarship_type_id" id="scholarship_type_id" class="form-select-custom">
                            <option value="">Dropdown</option>
                            @foreach($scholarshipTypes as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="school_id">University</label>
                        <select wire:model="school_id" id="school_id" class="form-select-custom">
                            <option value="">Dropdown</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="course_id">Course</label>
                        <select wire:model="course_id" id="course_id" class="form-select-custom">
                            <option value="">Dropdown</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->abbreviation }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="clearance_status_id">Clearance Status</label>
                        <select wire:model="clearance_status_id" id="clearance_status_id" class="form-select-custom">
                            <option value="">Dropdown</option>
                            @foreach($clearanceStatuses as $cs)
                                <option value="{{ $cs->id }}">{{ $cs->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="clearance_date">Clearance Date</label>
                        <input wire:model="clearance_date" type="date" id="clearance_date" class="form-control-custom">
                    </div>
                </div>
            </div>

            {{-- Section 3: Personal Information --}}
            <div class="add-file-form-card">
                <h3 class="add-file-form-card__title">Personal Information</h3>

                <div class="row g-3">
                    <div class="col-md-3 form-field-group">
                        <label for="barangay">Address / Barangay</label>
                        <input wire:model="barangay" type="text" id="barangay" class="form-control-custom" placeholder="Placeholder">
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="municipality">Municipality</label>
                        <input wire:model="municipality" type="text" id="municipality" class="form-control-custom" placeholder="Dropdown">
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="province">Province</label>
                        <input wire:model="province" type="text" id="province" class="form-control-custom" placeholder="Dropdown">
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="region_id">Region</label>
                        <select wire:model="region_id" id="region_id" class="form-select-custom">
                            <option value="">Dropdown</option>
                            @foreach($regions as $reg)
                                <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="birthdate">Birthdate</label>
                        <input wire:model="birthdate" type="date" id="birthdate" class="form-control-custom">
                    </div>

                    <div class="col-md-3 form-field-group">
                        <label for="sex">Sex</label>
                        <select wire:model="sex" id="sex" class="form-select-custom">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>
            {{-- Section 4: Upload Scanned Files Extension (Screenshot Match) --}}
            <div class="upload-file-form-card">
                <h3 class="upload-file-form-card__title text-center mb-4">Upload Scanned Files</h3>

                <div class="upload-scanned-stack d-flex flex-column gap-3">
                    @foreach($scannedCategories as $catIndex => $cat)
                        <div class="scanned-file-box">
                            <div class="scanned-file-box__header d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-1">
                                    <input type="text" 
                                           wire:model.live="scannedCategories.{{ $catIndex }}.name" 
                                           class="scanned-file-box__editable-label" 
                                           title="Click to rename category">
                                    <i class="ph ph-pencil-simple text-muted small"></i>
                                </div>
                                @if(count($scannedCategories) > 1)
                                    <button type="button" wire:click="removeScannedCategory({{ $catIndex }})" class="btn btn-link text-danger p-0 border-0 shadow-none text-decoration-none me-2" title="Remove Category">
                                        <i class="ph ph-trash fs-6"></i>
                                    </button>
                                @endif
                            </div>

                            @if(empty($cat['files']))
                                {{-- Empty Dropzone State --}}
                                <div class="upload-dropzone">
                                    <div class="upload-dropzone__icon">
                                        <i class="ph ph-tray"></i>
                                    </div>
                                    <h6 class="upload-dropzone__title">Click or drag file to this area to upload</h6>
                                    <p class="upload-dropzone__subtitle">Support for a single or bulk upload. Maximum file size 2MB.</p>
                                </div>
                            @else
                                {{-- Staged File Preview Grid --}}
                                <div class="scanned-file-box__preview-grid d-flex gap-3 flex-wrap p-2">
                                    @foreach($cat['files'] as $file)
                                        <div class="staged-file-card">
                                            <img src="{{ $file['url'] ?? 'https://images.unsplash.com/photo-1568667256549-094345857637?q=80&w=300&auto=format&fit=crop' }}" alt="Scanned file" class="img-fluid rounded">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    {{-- Centered + Add Scanned File Category Button --}}
                    <div class="text-center mt-2">
                        <button type="button" wire:click="addScannedCategory" class="btn-add-category-custom">
                            <i class="ph ph-plus me-1"></i> Add Scanned File Category
                        </button>
                    </div>
                </div>
            </div>

            {{-- Form Bottom Action Bar (Discard + Upload File Green Button) --}}
            <div class="form-actions-bar d-flex align-items-center justify-content-end gap-3 mt-4">
                <button type="button" wire:click="discard" class="btn-discard-custom">Discard</button>
                <button type="submit" class="btn-success-custom">Upload File</button>
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
