<x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">
            {{ __('Add Administrative Record') }}
        </h2>
        <a href="{{ route('admin-records.index') }}" class="btn btn-link btn-sm">
            &larr; Back to Directory
        </a>
    </div>
</x-slot>

<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <form wire:submit="save">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Record Type <span class="text-danger">*</span></label>
                        <select wire:model="record_type" class="form-select" required>
                            <option value="">Select type...</option>
                            @foreach($recordTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('record_type') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Series / Reference Number</label>
                        <input wire:model="series_number" type="text" placeholder="e.g. Memo-2023-001" class="form-control">
                        @error('series_number') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Title / Subject <span class="text-danger">*</span></label>
                        <input wire:model="title" type="text" placeholder="e.g. Guidelines on Scholarship Grant..." class="form-control" required>
                        @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Recipient / Addressee</label>
                        <input wire:model="recipient" type="text" class="form-control">
                        @error('recipient') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Year</label>
                        <input wire:model="year" type="number" class="form-control">
                        @error('year') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quarter</label>
                        <select wire:model="quarter" class="form-select">
                            <option value="">N/A</option>
                            <option value="Q1">Q1</option>
                            <option value="Q2">Q2</option>
                            <option value="Q3">Q3</option>
                            <option value="Q4">Q4</option>
                        </select>
                        @error('quarter') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input wire:model="for_disposal" type="checkbox" class="form-check-input" id="for_disposal_create">
                            <label class="form-check-label text-danger" for="for_disposal_create">Flag for Disposal (Retention eligibility achieved)</label>
                        </div>
                        @error('for_disposal') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                    <a href="{{ route('admin-records.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        Create Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
