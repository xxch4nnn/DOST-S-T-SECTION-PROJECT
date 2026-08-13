<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Scholar;
use Illuminate\Support\Facades\Auth;

/**
 * Ports ScholarObserver from db-integration (Q08=C) onto mother audit_logs.record_*.
 * No fts_search_data / folders in this slice. DocumentObserver is a separate class (#95).
 */
class ScholarObserver
{
    public function created(Scholar $scholar): void
    {
        $this->writeAudit('created', $scholar, before: null, after: $scholar->toArray());
    }

    public function updated(Scholar $scholar): void
    {
        if (! $scholar->wasChanged()) {
            return;
        }

        $this->writeAudit(
            'updated',
            $scholar,
            before: $scholar->getOriginal(),
            after: $scholar->getChanges(),
        );
    }

    public function deleted(Scholar $scholar): void
    {
        $this->writeAudit('deleted', $scholar, before: $scholar->toArray(), after: null);
    }

    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    protected function writeAudit(string $action, Scholar $scholar, ?array $before, ?array $after): void
    {
        $userId = Auth::id();
        if (! $userId) {
            return;
        }

        AuditLog::query()->create([
            'user_id' => $userId,
            'action' => $action,
            'record_type' => Scholar::class,
            'record_id' => $scholar->id,
            'before_payload' => $before,
            'after_payload' => $after,
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);
    }
}
