<x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">
            {{ $scholar->first_name }} {{ $scholar->last_name }}'s Record
        </h2>
        <a href="{{ route('scholars.index') }}" class="btn btn-link btn-sm">
            &larr; Back to Directory
        </a>
    </div>
</x-slot>

<div class="container py-4">
    <div class="row g-4">

        <!-- Scholar Details -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h3 class="h5 mb-0">Scholar Information</h3>
                        <a href="{{ route('scholars.edit', $scholar->id) }}" class="btn btn-outline-primary btn-sm">Edit Info</a>
                    </div>
                    <dl class="mb-0">
                        <div class="mb-3">
                            <dt class="small text-muted">SPAS No.</dt>
                            <dd class="mb-0">{{ $scholar->spas_number ?? 'N/A' }}</dd>
                        </div>
                        <div class="mb-3">
                            <dt class="small text-muted">Name</dt>
                            <dd class="mb-0 fw-semibold">{{ $scholar->first_name }} {{ $scholar->middle_name }} {{ $scholar->last_name }} {{ $scholar->generational_suffix }}</dd>
                        </div>
                        <div class="mb-3">
                            <dt class="small text-muted">Scholarship</dt>
                            <dd class="mb-0">{{ $scholar->scholarshipProgram?->name }} ({{ $scholar->year_of_award }})</dd>
                        </div>
                        <div class="mb-3">
                            <dt class="small text-muted">School &amp; Course</dt>
                            <dd class="mb-0">{{ $scholar->school?->name ?? 'null' }} - {{ $scholar->course?->abbreviation ?? 'null' }}</dd>
                        </div>
                        <div class="mb-3">
                            <dt class="small text-muted">Sex &amp; Birthdate</dt>
                            <dd class="mb-0">{{ $scholar->sex ?? 'N/A' }} / {{ $scholar->birthdate ? $scholar->birthdate->format('M d, Y') : 'N/A' }}</dd>
                        </div>
                        <div class="mb-3">
                            <dt class="small text-muted">Contact</dt>
                            <dd class="mb-0">{{ $scholar->contact_number ?? 'N/A' }} <br> {{ $scholar->email_address ?? 'N/A' }}</dd>
                        </div>
                        <div class="mb-3">
                            <dt class="small text-muted">Geographic Address</dt>
                            <dd class="mb-0 text-muted">
                                {{ $scholar->barangay ? $scholar->barangay . ', ' : '' }}
                                {{ $scholar->municipality ? $scholar->municipality . ', ' : '' }}
                                {{ $scholar->province ? $scholar->province : '' }}
                                <div class="small text-muted">Region: {{ $scholar->region?->abbreviation }}</div>
                            </dd>
                        </div>
                        <div class="mb-3">
                            <dt class="small text-muted">Clearance Status</dt>
                            <dd class="mb-0">
                                <span class="badge bg-success">
                                    {{ $scholar->clearanceStatus?->name ?? 'Active' }}
                                </span>
                                @if($scholar->clearance_date)
                                <div class="small text-muted mt-1">Cleared: {{ $scholar->clearance_date->format('M d, Y') }}</div>
                                @endif
                            </dd>
                        </div>
                        <div class="mb-0">
                            <dt class="small text-muted">Disposal Status</dt>
                            <dd class="mb-0">
                                @if($scholar->for_disposal)
                                <span class="badge bg-danger">Eligible for Disposal</span>
                                @else
                                <span class="badge bg-success">Retained</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="col-md-8">

            <!-- Upload Document -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h5 mb-3">Upload New Document</h3>

                    @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                    @endif

                    <form wire:submit="uploadDocument">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Document Type <span class="text-danger">*</span></label>
                                <select wire:model="file_type_id" class="form-select" required>
                                    <option value="">Select type...</option>
                                    @foreach($fileTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->year }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">File <span class="text-danger">*</span></label>
                                <input wire:model="file" type="file" class="form-control" required>
                                <div wire:loading wire:target="file" class="small text-primary mt-1">Uploading...</div>
                                @error('file') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Document List -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="h5 mb-0">Document History</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Filename</th>
                                <th>Size</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($documents as $doc)
                            <tr class="{{ $doc->trashed() ? 'table-danger opacity-50' : '' }}">
                                <td class="fw-medium">
                                    {{ $doc->fileType?->name }}
                                    @if($doc->trashed())
                                    <span class="badge bg-danger ms-1">Struck Off</span>
                                    @endif
                                </td>
                                <td class="text-muted">
                                    {{ Str::limit($doc->original_filename, 30) }}
                                </td>
                                <td class="text-muted">
                                    {{ $doc->file_size_kb }} KB
                                </td>
                                <td class="text-muted">
                                    {{ $doc->created_at->format('M d, Y') }}
                                </td>
                                <td class="text-end">
                                    @if(!$doc->trashed())
                                    <a href="{{ route('documents.download', $doc->uuid) }}" class="btn btn-link btn-sm" target="_blank">Download</a>
                                    @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin']))
                                    <button wire:click="strikeOff('{{ $doc->uuid }}')" wire:confirm="Are you sure you want to strike off this document? This soft-delete can be undone." class="btn btn-link btn-sm text-danger">Strike Off</button>
                                    @endif
                                    @else
                                    @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin']))
                                    <button wire:click="undoStrikeOff('{{ $doc->uuid }}')" class="btn btn-link btn-sm text-success">Undo Strike Off</button>
                                    @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No documents uploaded yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Duplicate Modal (Priority 2 Step 6) -->
@if($showDuplicateModal)
<div class="modal d-block" tabindex="-1" style="background-color: rgba(0,0,0,.5);">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h5 mb-0">Duplicate Document Detected</h4>
            </div>
            <div class="modal-body">
                <p class="mb-2">
                    An active document of type <strong>{{ $duplicateDocument->fileType?->name }}</strong> already exists for this record:
                </p>
                <p class="small text-muted mb-0">
                    Existing file: {{ $duplicateDocument->original_filename }} (Uploaded on {{ $duplicateDocument->created_at->format('M d, Y') }})
                </p>
            </div>
            <div class="modal-footer flex-column align-items-stretch gap-2">
                <button wire:click="resolveDuplicate('keep_history')" class="btn btn-primary">
                    Keep History (Archive current, upload new as active)
                </button>
                <button wire:click="resolveDuplicate('overwrite')" class="btn btn-warning">
                    Overwrite (Physically delete current, replace metadata)
                </button>
                <button wire:click="resolveDuplicate('cancel')" class="btn btn-outline-secondary">
                    Cancel (Keep current file untouched)
                </button>
            </div>
        </div>
    </div>
</div>
@endif