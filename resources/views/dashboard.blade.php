<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0 fw-semibold">
            {{ __('DOSTorage Dashboard') }}
        </h2>
    </x-slot>

    <div class="container py-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h1 class="h3 fw-bold">
                    Welcome to DOSTorage, {{ auth()->user()->name }}!
                </h1>
                <p class="text-muted mb-0">
                    Digitized records portal for the DOST Region XI Scholarship Section. Manage physical 201 files and administrative documents offline securely.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <a href="{{ route('scholars.index') }}" class="card shadow-sm h-100 text-decoration-none text-body">
                    <div class="card-body d-flex align-items-start gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                            <svg style="width: 2rem; height: 2rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="h5 fw-bold mb-1">Scholar 201 Registry</h2>
                            <p class="text-muted small mb-0">Search scholars, view files, and upload scanned documents.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6">
                <a href="{{ route('admin-records.index') }}" class="card shadow-sm h-100 text-decoration-none text-body">
                    <div class="card-body d-flex align-items-start gap-3">
                        <div class="bg-secondary bg-opacity-10 text-secondary rounded p-3">
                            <svg style="width: 2rem; height: 2rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="h5 fw-bold mb-1">Administrative Records</h2>
                            <p class="text-muted small mb-0">Manage official documents, memoranda, special orders, and reports.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
