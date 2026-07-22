<x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">
            {{ __('Administrative Records') }}
        </h2>
        <a href="{{ route('admin-records.create') }}" class="btn btn-primary btn-sm">
            Add Admin Record
        </a>
    </div>
</x-slot>

<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Title / Series / Recipient</label>
                    <input wire:model.live.debounce.300ms="search" type="text" id="search" class="form-control" placeholder="Search...">
                </div>
                <div class="col-md-4">
                    <label for="record_type" class="form-label">Record Type</label>
                    <select wire:model.live="record_type" id="record_type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($recordTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="year" class="form-label">Year</label>
                    <input wire:model.live.debounce.300ms="year" type="number" id="year" placeholder="e.g. 2023" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Type &amp; Series</th>
                        <th>Title</th>
                        <th>Recipient</th>
                        <th>Year/Quarter</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $record->record_type }}</div>
                                <div class="small text-muted">{{ $record->series_number ?? 'No Series' }}</div>
                            </td>
                            <td>{{ $record->title }}</td>
                            <td class="text-muted">{{ $record->recipient ?? 'N/A' }}</td>
                            <td class="text-muted">
                                {{ $record->year ?? 'N/A' }} {{ $record->quarter ? "($record->quarter)" : '' }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin-records.show', $record->id) }}" class="btn btn-link btn-sm">View</a>
                                <a href="{{ route('admin-records.edit', $record->id) }}" class="btn btn-link btn-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No administrative records found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $records->links() }}
        </div>
    </div>
</div>
