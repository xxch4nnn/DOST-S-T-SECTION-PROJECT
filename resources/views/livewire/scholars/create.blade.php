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

<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-4">
                    <h3 class="h5 border-bottom pb-2">1. Personal Information</h3>
                    <div class="row g-3 mt-1">
                        <div class="col-md-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input wire:model="first_name" type="text" class="form-control" required>
                            @error('first_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Middle Name</label>
                            <input wire:model="middle_name" type="text" class="form-control">
                            @error('middle_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input wire:model="last_name" type="text" class="form-control" required>
                            @error('last_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Suffix (e.g. Jr, III)</label>
                            <input wire:model="generational_suffix" type="text" class="form-control">
                            @error('generational_suffix') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sex</label>
                            <select wire:model="sex" class="form-select">
                                <option value="">Select...</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            @error('sex') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Birthdate</label>
                            <input wire:model="birthdate" type="date" class="form-control">
                            @error('birthdate') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input wire:model="contact_number" type="text" placeholder="09171234567" class="form-control" required>
                            @error('contact_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Email Address</label>
                            <input wire:model="email_address" type="email" class="form-control">
                            @error('email_address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="h5 border-bottom pb-2">2. Scholarship &amp; Academic Information</h3>
                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <label class="form-label">SPAS No.</label>
                            <input wire:model="spas_no" type="text" class="form-control">
                            @error('spas_no') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Year of Award <span class="text-danger">*</span></label>
                            <input wire:model="year_of_award" type="number" class="form-control" required>
                            @error('year_of_award') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Scholarship <span class="text-danger">*</span></label>
                            <select wire:model="scholarship_id" class="form-select" required>
                                <option value="">Select...</option>
                                @foreach($scholarships as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('scholarship_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Scholarship Type <span class="text-danger">*</span></label>
                            <select wire:model="scholarship_type_id" class="form-select" required>
                                <option value="">Select...</option>
                                @foreach($scholarshipTypes as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                            @error('scholarship_type_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">School <span class="text-danger">*</span></label>
                            <select wire:model="school_id" class="form-select" required>
                                <option value="">Select...</option>
                                @foreach($schools as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->campus ?? 'Main' }})</option>
                                @endforeach
                            </select>
                            @error('school_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Program & Course</label>
                            <select wire:model="course_id" class="form-select">
                                <option value="">Select...</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->abbreviation }})</option>
                                @endforeach
                            </select>
                            @error('course_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Major / Specialization Details</label>
                            <input wire:model="program" type="text" class="form-control">
                            @error('program') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="h5 border-bottom pb-2">3. Address &amp; Geographic Information</h3>
                    <div class="row g-3 mt-1">
                        <div class="col-md">
                            <label class="form-label">Barangay</label>
                            <input wire:model="barangay" type="text" class="form-control">
                            @error('barangay') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md">
                            <label class="form-label">Municipality / City</label>
                            <input wire:model="municipality" type="text" class="form-control">
                            @error('municipality') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md">
                            <label class="form-label">District</label>
                            <input wire:model="district" type="text" class="form-control">
                            @error('district') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md">
                            <label class="form-label">Province</label>
                            <input wire:model="province" type="text" class="form-control">
                            @error('province') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md">
                            <label class="form-label">Region <span class="text-danger">*</span></label>
                            <select wire:model="region_id" class="form-select" required>
                                <option value="">Select...</option>
                                @foreach($regions as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->abbreviation }})</option>
                                @endforeach
                            </select>
                            @error('region_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="h5 border-bottom pb-2">4. Clearance &amp; Status</h3>
                    <div class="row g-3 mt-1 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Clearance Status <span class="text-danger">*</span></label>
                            <select wire:model="clearance_status_id" class="form-select" required>
                                @foreach($clearanceStatuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                            @error('clearance_status_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Clearance Date</label>
                            <input wire:model="clearance_date" type="date" class="form-control">
                            @error('clearance_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input wire:model="for_disposal" type="checkbox" class="form-check-input" id="for_disposal_create">
                                <label class="form-check-label text-danger" for="for_disposal_create">Flag for Disposal (Retention eligibility achieved)</label>
                            </div>
                            @error('for_disposal') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('scholars.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Scholar</button>
                </div>
            </form>
        </div>
    </div>
</div>
