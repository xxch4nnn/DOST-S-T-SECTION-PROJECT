<?php

namespace App\Observers;

use App\Models\Document;

class DocumentObserver
{
    // Ensures the FTS data gets updated immediately after a file is created or updated 
    public bool $afterCommit = true;

    public function creating(Document $file){
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
    public function updating(Document $file){
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
     */
    public function deleted(Document $file): void
    {
        //
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
