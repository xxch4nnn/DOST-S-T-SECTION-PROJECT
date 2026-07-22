<?php

namespace App\Livewire\AuditLogs;

use App\Models\AuditLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Title('Audit Log')]

    public string $action = '';
    public string $recordType = '';
    public string $user = '';
    public ?string $from = null;
    public ?string $to = null;

    public function mount(): void
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
    }

    public function updatingAction(): void { $this->resetPage(); }
    public function updatingRecordType(): void { $this->resetPage(); }
    public function updatingUser(): void { $this->resetPage(); }
    public function updatingFrom(): void { $this->resetPage(); }
    public function updatingTo(): void { $this->resetPage(); }

    public function clear(): void
    {
        $this->reset(['action', 'recordType', 'user', 'from', 'to']);
        $this->resetPage();
    }

    public function render()
    {
        $query = AuditLog::query()
            ->with(['user:id,name,email'])
            ->latest();

        if ($this->action !== '') {
            $query->where('action', $this->action);
        }
        if ($this->recordType !== '') {
            $query->where('record_type', 'like', '%'.$this->recordType.'%');
        }
        if ($this->user !== '') {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%'.$this->user.'%')
                  ->orWhere('email', 'like', '%'.$this->user.'%');
            });
        }
        if ($this->from) {
            $query->whereDate('created_at', '>=', $this->from);
        }
        if ($this->to) {
            $query->whereDate('created_at', '<=', $this->to);
        }

        $logs = $query
            ->paginate(20)
            ->withQueryString();

        return view('livewire.audit-logs.index', [
            'logs' => $logs,
            'actions' => AuditLog::query()
                ->select('action')
                ->distinct()
                ->orderBy('action')
                ->pluck('action'),
        ]);
    }
}
