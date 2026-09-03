<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class DocumentObserver
{
    // Ensures the FTS data gets updated immediately after a file is created or updated
    public bool $afterCommit = true;

    public function creating(Document $file)
    {
        $file->metadata = $file->metadata ?? [];
    }

    /**
     * Handle the Document "created" event.
     */
    public function created(Document $file): void
    {

        // Create a new column given the file information
        // Add it to the audit log
    }

    // A function run before saving the entire query...
    public function updating(Document $file)
    {
        $file->metadata = $file->metadata ?? [];
    }

    /**
     * Handle the Document "updated" event.
     */
    public function updated(Document $file): void
    {
        // Make an audit log
    }

    /**
     * Handle the Document "deleted" event.
     * 
     * @param Document $document
     * @return void
     */
    public function deleted(Document $document){
        // Ensures the action made was a soft delete
        if (! $document->isForceDeleting()) { 
            AuditLog::create([
                'user_id'        => Auth::id(), // The user who clicked delete
                'action'         => 'deleted',
                'record_type'    => Document::class,
                'record_id'      => $document->uuid,
                'before_payload' => $document->toArray(),
                'after_payload'  => null,
                'ip_address'     => request()->ip() ?? '127.0.0.1',
            ]);
        }
    }

    /**
     * Handle the Document "restored" event.
     */
    public function restored(Document $file): void
    {
        //
    }

    /**
     * Handle the Document "force deleted" event.
     */
    public function forceDeleted(Document $file): void
    {
        //
    }
}
