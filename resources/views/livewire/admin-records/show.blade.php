<x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">
            {{ $record->record_type }}: {{ $record->series_number ?? 'No Series' }}
        </h2>
        <a href="{{ route('admin-records.index') }}" class="btn btn-link btn-sm">
            &larr; Back to Directory
        </a>
    </div>
</x-slot>

<div class="container py-4">
    <div class="row g-4">

        <!-- Record Details -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h3 class="h5 mb-0">Record Information</h3>
                        <a href="{{ route('admin-records.edit', $record->id) }}" class="btn btn-outline-primary btn-sm">Edit Info</a>
                    </div>
                    <dl class="mb-0">
                        <div class="mb-3">
                            <dt class="small text-muted">Record Type</dt>
                            <dd class="mb-0 fw-semibold">{{ $record->record_type }}</dd>
                        </div>
                        <div class="mb-3">
                            <dt class="small text-muted">Series Number</dt>
                            <dd class="mb-0">{{ $record->series_number ?? 'N/A' }}</dd>
                        </div>
                        <div class="mb-3">
                            <dt class="small text-muted">Title / Subject</dt>
                            <dd class="mb-0">{{ $record->title }}</dd>
                        </div>
                        <div class="mb-3">
                            <dt class="small text-muted">Recipient / Addressee</dt>
                            <dd class="mb-0">{{ $record->recipient ?? 'N/A' }}</dd>
                        </div>
                        <div class="mb-3">
                            <dt class="small text-muted">Year / Quarter</dt>
                            <dd class="mb-0">{{ $record->year ?? 'N/A' }} {{ $record->quarter ? "($record->quarter)" : '' }}</dd>
                        </div>
                        <div class="mb-3">
                            <dt class="small text-muted">Disposal Status</dt>
                            <dd class="mb-0">
                                @if($record->for_disposal)
                                    <span class="badge bg-danger">Eligible for Disposal</span>
                                @else
                                    <span class="badge bg-success">Retained</span>
                                @endif
                            </dd>
                        </div>
                        <div class="mb-0">
                            <dt class="small text-muted">Created By</dt>
                            <dd class="mb-0">{{ $record->creator?->name }}</dd>
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
                    <h3 class="h5 mb-3">Upload Document File</h3>

                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form wire:submit="uploadDocument">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">File Type / Category <span class="text-danger">*</span></label>
                                <select wire:model="file_type_id" class="form-select" required>
                                    <option value="">Select type...</option>
                                    @foreach($fileTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
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
                                            <a href="{{ route('documents.download', $doc->id) }}" class="btn btn-link btn-sm" target="_blank">Download</a>
                                            @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin']))
                                                <button wire:click="strikeOff({{ $doc->id }})" wire:confirm="Are you sure you want to strike off this document? This soft-delete can be undone." class="btn btn-link btn-sm text-danger">Strike Off</button>
                                            @endif
                                        @else
                                            @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin']))
                                                <button wire:click="undoStrikeOff({{ $doc->id }})" class="btn btn-link btn-sm text-success">Undo Strike Off</button>
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
