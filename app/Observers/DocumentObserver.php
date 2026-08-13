<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

/**
 * Document CRUD → audit_logs.record_* (same contract as ScholarObserver / Q08=C).
 * Skips writes when unauthenticated — never fabricates user_id.
 */
class DocumentObserver
{
    public function created(Document $document): void
    {
        $this->writeAudit('created', $document, before: null, after: $document->toArray());
    }

    public function updated(Document $document): void
    {
        if (! $document->wasChanged()) {
            return;
        }

        $this->writeAudit(
            'updated',
            $document,
            before: $document->getOriginal(),
            after: $document->getChanges(),
        );
    }

    public function deleted(Document $document): void
    {
        // SoftDeletes sets deleted_at before this event; use originals for pre-delete snapshot.
        $this->writeAudit('deleted', $document, before: $document->getOriginal(), after: null);
    }

    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    protected function writeAudit(string $action, Document $document, ?array $before, ?array $after): void
    {
        $userId = Auth::id();
        if (! $userId) {
            return;
        }

        AuditLog::query()->create([
            'user_id' => $userId,
            'action' => $action,
            'record_type' => Document::class,
            'record_id' => $document->id,
            'before_payload' => $before,
            'after_payload' => $after,
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);
    }
}
