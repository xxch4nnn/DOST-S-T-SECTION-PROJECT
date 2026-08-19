<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\Auth;

class DocumentVersionObserver
{
    // Ensures the audit log gets created after transaction commits
    public bool $afterCommit = true;

    /**
     * Handle the DocumentVersion "created" event.
     */
    public function created(DocumentVersion $document): void
    {
        $document->loadMissing('document');
        $parentDocument = $document->document;

        // Fetch previous version (if editing existing document)
        $previousVersion = DocumentVersion::where('document_uuid', $document->document_uuid)
            ->where('id', '!=', $document->id)
            ->orderByDesc('version_number')
            ->first();

        $docData = $parentDocument ? $parentDocument->attributesToArray() : [];

        $beforePayload = $previousVersion
            ? array_merge($docData, ['current_version' => $previousVersion->attributesToArray()])
            : null;

        $afterPayload = array_merge($docData, ['current_version' => $document->attributesToArray()]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 2,
            'action' => ($document->version_number ?? 1) > 1 ? 'updated' : 'created',
            'record_type' => Document::class,
            'record_id' => $document->document_uuid,
            'before_payload' => $beforePayload,
            'after_payload' => $afterPayload,
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);
    }

    /**
     * Handle the DocumentVersion "updated" event.
     */
    public function updated(DocumentVersion $document): void
    {
        $document->loadMissing('document');
        $parentDocument = $document->document;
        $docData = $parentDocument ? $parentDocument->attributesToArray() : [];

        $beforePayload = array_merge($docData, ['current_version' => $document->getOriginal()]);
        $afterPayload = array_merge($docData, ['current_version' => $document->attributesToArray()]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 2,
            'action' => 'updated',
            'record_type' => Document::class,
            'record_id' => $document->document_uuid,
            'before_payload' => $beforePayload,
            'after_payload' => $afterPayload,
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);
    }
}
