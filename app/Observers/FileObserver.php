<?php

namespace App\Observers;

use App\Models\File;

class FileObserver
{
    // Ensures the FTS data gets updated immediately after a file is created or updated 
    public bool $afterCommit = true;

    public function creating(File $file){
        if ($file->file_path) 
            $file->file_path = str_replace('/', '\\', $file->file_path);

        if (empty($file->mime_type))
            $file->mime_type = 'application/pdf';

        $file->metadata = $file->metadata ?? [];
    }

    /**
     * Handle the File "created" event.
     */
    public function created(File $file): void
    {
        
        // Create a new column given the file information
        // Add it to the audit log
    }

    // A function run before saving the entire query...
    public function updating(File $file){
        if ($file->file_path) 
            $file->file_path = str_replace('/', '\\', $file->file_path);

        if (empty($file->mime_type))
            $file->mime_type = 'application/pdf';

        $file->metadata = $file->metadata ?? [];
    }

    /**
     * Handle the File "updated" event.
     */
    public function updated(File $file): void
    {
        // Make an audit log 
    }

    /**
     * Handle the File "deleted" event.
     */
    public function deleted(File $file): void
    {
        //
    }

    /**
     * Handle the File "restored" event.
     */
    public function restored(File $file): void
    {
        //
    }

    /**
     * Handle the File "force deleted" event.
     */
    public function forceDeleted(File $file): void
    {
        //
    }
}
