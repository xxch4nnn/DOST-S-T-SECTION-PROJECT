<x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">
            {{ __('Add New Scholar') }}
        </h2>
        <a href="{{ route('scholars.index') }}" class="btn btn-link btn-sm">
            &larr; Back to Directory
        </a>
    </div>
</x-slot>

<div class="add-file-page">
    {{-- Breadcrumb Bar --}}
    <div class="admin-breadcrumb-bar">
        <a href="{{ route('scholars.index') }}" wire:navigate class="admin-back-btn text-decoration-none" title="Back to Scholars">
            <i class="ph ph-caret-left"></i>
        </a>
        <span class="admin-breadcrumb-title">Scholars</span>
        <span class="admin-breadcrumb-sep">/</span>
        <span class="admin-breadcrumb-active">Add Scholar</span>
    </div>

    {{-- Hidden data element for preloading existing categories & documents into JS --}}
    <div id="existing-categories-data" style="display: none;"></div>

    <form wire:submit="save" class="add-file-form-stack" id="scholarEditForm">
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
                    <label for="spas_no">SPAS ID No.</label>
                    <input wire:model="spas_no" type="text" id="spas_no" class="form-control-custom" placeholder="e.g. U-2023-00855-2235">
                    @error('spas_no') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4 form-field-group">
                    <label for="scholarship_id">Scholarship Program <span class="text-danger">*</span></label>
                    <select wire:model="scholarship_id" id="scholarship_id" class="form-select-custom" required>
                        <option value="">Select Scholarship</option>
                        @foreach($scholarships as $sch)
                            <option value="{{ $sch->id }}">{{ $sch->name }}</option>
                        @endforeach
                    </select>
                    @error('scholarship_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4 form-field-group">
                    <label for="scholarship_type_id">Program Type <span class="text-danger">*</span></label>
                    <select wire:model="scholarship_type_id" id="scholarship_type_id" class="form-select-custom" required>
                        <option value="">Select Program Type</option>
                        @foreach($scholarshipTypes as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                    @error('scholarship_type_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                {{-- Row 2: 3 equal columns --}}
                <div class="col-md-4 form-field-group">
                    <label for="year_of_award">Year of Award <span class="text-danger">*</span></label>
                    <select wire:model="year_of_award" id="year_of_award" class="form-select-custom" required>
                        <option value="" selected disabled>Select Year of Award</option>
                        @for($y = date('Y'); $y >= 2000; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                    @error('year_of_award') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4 form-field-group">
                    <label for="school_id">University / School <span class="text-danger">*</span></label>
                    <select wire:model="school_id" id="school_id" class="form-select-custom" required>
                        <option value="" selected disabled>Select School</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }} {{ $school->campus ? "(".$school->campus." Campus)" : '' }} {{ $school->abbreviation ? "({$school->abbreviation})" : '' }}</option>
                        @endforeach
                    </select>
                    @error('school_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4 form-field-group">
                    <label for="course_id">Course / Degree Program</label>
                    <select wire:model="course_id" id="course_id" class="form-select-custom">
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Row 3: 2 equal columns --}}
                <div class="col-md-6 form-field-group">
                    <label for="clearance_status_id">Clearance Status <span class="text-danger">*</span></label>
                    <select wire:model="clearance_status_id" id="clearance_status_id" class="form-select-custom" required>
                        <option value="" selected disabled>Select Status</option>
                        @foreach($clearanceStatuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('clearance_status_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-field-group">
                    <label for="clearance_date" id="clearance_date_label">Clearance Date</label>
                    <input wire:model="clearance_date" type="date" id="clearance_date" class="form-control-custom">
                </div>
            </div>
        </div>

        {{-- Section 3: Demographic & Contact Information --}}
        <div class="add-file-form-card">
            <h3 class="add-file-form-card__title">Demographic & Contact Information</h3>

            <div class="row g-3">
                {{-- Row 1: 4 columns (Hierarchical Location Dropdowns) --}}
                <div class="col-md-3 form-field-group">
                    <label for="region_id">Region <span class="text-danger">*</span></label>
                    <select wire:model.live.debounce.300ms="region_id" id="region_id" class="form-select-custom" required>
                        <option value="" selected disabled>Select Region</option>
                        @foreach($regions as $reg)
                            <option value="{{ $reg->id }}">{{ $reg->abbreviation }} ({{ $reg->name }})</option>
                        @endforeach
                    </select>
                    @error('region_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3 form-field-group">
                    <label for="province">Province</label>
                    <select wire:model.live.debounce.300ms="province_id" id="province" class="form-select-custom">
                        <option value="" selected disabled>{{ empty($region_id) ? 'Select Region First' : 'Select Province' }}</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                        @endforeach
                    </select>
                    @error('province_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3 form-field-group">
                    <label for="municipality">Municipality / City</label>
                    <select wire:model.live.debounce.300ms="municipality_id" id="municipality" class="form-select-custom" {{ empty($province_id) ? 'disabled' : '' }}>
                        <option value="" selected disabled>{{ empty($province_id) ? 'Select Province First' : 'Select Municipality / City' }}</option>
                        @foreach($municipalities as $muni)
                            <option value="{{ $muni->id }}">{{ $muni->name }}</option>
                        @endforeach
                    </select>
                    @error('municipality_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3 form-field-group">
                    <label for="barangay">Address / Barangay</label>
                    <select wire:model.live.debounce.300ms="barangay_id" id="barangay" class="form-select-custom" {{ empty($municipality_id) ? 'disabled' : '' }}>
                        <option value="" selected disabled>{{ empty($municipality_id) ? 'Select Municipality/City First' : 'Select Barangay' }}</option>
                        @foreach($barangays as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                    @error('barangay_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                {{-- Row 2: 1 full-width column --}}
                <div class="col-12 form-field-group">
                    <label for="home_address">Block & Lot / Street</label>
                    <input wire:model="home_address" type="text" id="home_address" class="form-control-custom" placeholder="e.g. Block 1, Lot 2, Sobrecarey St.">
                    @error('home_address') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                {{-- Row 3: 4 columns --}}
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

                <div class="col-md-3 form-field-group">
                    <label for="contact_number">Contact Number <span class="text-danger">*</span></label>
                    <input wire:model="contact_number" type="text" id="contact_number" class="form-control-custom" placeholder="e.g., 09123456789" required>
                    @error('contact_number') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3 form-field-group">
                    <label for="email_address">Email Address</label>
                    <input wire:model="email_address" type="email" id="email_address" class="form-control-custom" placeholder="e.g., scholar@gmail.com">
                    @error('email_address') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Form Bottom Action Bar (Discard + Save Changes) --}}
        <div class="form-actions-bar d-flex align-items-center justify-content-end gap-3 mt-4">
            <a href="{{ route('scholars.index') }}" wire:navigate class="btn-discard-custom text-decoration-none">Discard</a>
            <button type="submit" class="btn-success-custom" id="btnSubmitScholar">
                <span id="btnSubmitScholarText">Create Scholar</span>
            </button>
        </div>
    </form>
</div>

@script
<script>
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => {
            const submitBtn = document.getElementById('btnSubmitScholar');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Create Scholar</span>';
            }
        });
    });

    document.getElementById("clearance_status_id").addEventListener("change", function(){
        if (this.value == 1){
            const clearanceDate = document.getElementById("clearance_date");
            const clearanceDateLabel = document.getElementById("clearance_date_label");
            clearanceDate.setAttribute("disabled", true);
            clearanceDate.removeAttribute("required");
            clearanceDate.value = "";
            clearanceDateLabel.innerHTML = "Clearance Date";
        } else {
            const clearanceDate = document.getElementById("clearance_date");
            const clearanceDateLabel = document.getElementById("clearance_date_label");
            clearanceDate.removeAttribute("disabled");
            clearanceDate.setAttribute("required", true);
            clearanceDateLabel.innerHTML = "Clearance Date <span class='text-danger'>*</span>";
        }
    })
</script>
@endscript