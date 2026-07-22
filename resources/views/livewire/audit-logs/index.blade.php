<div>
    <div class="mb-4">
        <h1 class="h4 fw-semibold">Audit Log</h1>
        <p class="text-muted small mb-0">Review recorded system actions, actors, and payload changes. Restricted to Admins.</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body border-bottom">
            <div class="row g-3">
                <div class="col-sm-6 col-lg-2">
                    <label class="form-label small">Action</label>
                    <input type="text" wire:model="action" class="form-control form-control-sm">
                </div>
                <div class="col-sm-6 col-lg-2">
                    <label class="form-label small">Record Type</label>
                    <input type="text" wire:model="recordType" class="form-control form-control-sm">
                </div>
                <div class="col-sm-6 col-lg-2">
                    <label class="form-label small">User</label>
                    <input type="text" wire:model="user" class="form-control form-control-sm">
                </div>
                <div class="col-sm-6 col-lg-2">
                    <label class="form-label small">From</label>
                    <input type="date" wire:model="from" class="form-control form-control-sm">
                </div>
                <div class="col-sm-6 col-lg-2">
                    <label class="form-label small">To</label>
                    <input type="date" wire:model="to" class="form-control form-control-sm">
                </div>
                <div class="col-sm-6 col-lg-2 d-flex align-items-end">
                    <button wire:click="clear" type="button" class="btn btn-sm btn-outline-secondary">Reset</button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th class="small">When</th>
                        <th class="small">User</th>
                        <th class="small">Action</th>
                        <th class="small">Record</th>
                        <th class="small">Before</th>
                        <th class="small">After</th>
                        <th class="small">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="small text-nowrap">{{ $log->created_at?->diffForHumans() }}</td>
                            <td class="small">
                                <div class="fw-medium">{{ $log->user?->name ?? 'System' }}</div>
                                <div class="text-muted">{{ $log->user?->email ?? '' }}</div>
                            </td>
                            <td class="small font-monospace">{{ $log->action }}</td>
                            <td class="small">
                                <div class="fw-medium">{{ class_basename($log->record_type) }}</div>
                                <div class="text-muted">#{{ $log->record_id }}</div>
                            </td>
                            <td class="small">
                                <pre class="mb-0 small" style="max-height: 6rem; overflow: auto; white-space: pre-wrap; word-break: break-word;">{{ json_encode($log->before_payload) }}</pre>
                            </td>
                            <td class="small">
                                <pre class="mb-0 small" style="max-height: 6rem; overflow: auto; white-space: pre-wrap; word-break: break-word;">{{ json_encode($log->after_payload) }}</pre>
                            </td>
                            <td class="small text-muted text-nowrap">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No audit entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $logs->links() }}
        </div>
    </div>
</div>
