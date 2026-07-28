<x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">
            {{ __('Scholars Directory') }}
        </h2>
        <a href="{{ route('scholars.create') }}" class="btn btn-primary btn-sm">
            Add Scholar
        </a>
    </div>
</x-slot>

<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search Name</label>
                    <input wire:model.live.debounce.300ms="search" type="text" id="search" class="form-control" placeholder="Search first, middle, last name...">
                </div>
                <div class="col-md-3">
                    <label for="spas_no" class="form-label">SPAS No.</label>
                    <input wire:model.live.debounce.300ms="spas_no" type="text" id="spas_no" class="form-control" placeholder="e.g. 2023-0001">
                </div>
                <div class="col-md-3">
                    <label for="school" class="form-label">School</label>
                    <select wire:model.live="school_id" id="school" class="form-select">
                        <option value="">All Schools</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }} ({{ $school->campus ?? 'Main' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="course" class="form-label">Course</label>
                    <select wire:model.live="course_id" id="course" class="form-select">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->abbreviation }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>SPAS No.</th>
                        <th>Name</th>
                        <th>School &amp; Course</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($scholars as $scholar)
                        <tr>
                            <td class="fw-medium">{{ $scholar->spas_number ?? 'None' }}</td>
                            <td>
                                <div class="fw-semibold">{{ $scholar->last_name }}, {{ $scholar->first_name }} {{ $scholar->middle_name }} {{ $scholar->generational_suffix }}</div>
                                <div class="small text-muted">{{ $scholar->scholarshipProgram->name ?? 'null' }} - {{ $scholar->year_of_award ?? 'null' }}</div>
                            </td>
                            <td>
                                <div>{{ $scholar->school->name ?? 'null' }}</div>
                                <div class="small text-muted">{{ $scholar->course?->abbreviation ?? 'null' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ $scholar->clearanceStatus->name ?? 'Not Cleared' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('scholars.show', $scholar->id) }}" class="btn btn-link btn-sm">View</a>
                                <a href="{{ route('scholars.edit', $scholar->id) }}" class="btn btn-link btn-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No scholars found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $scholars->links() }}
        </div>
    </div>
</div>
