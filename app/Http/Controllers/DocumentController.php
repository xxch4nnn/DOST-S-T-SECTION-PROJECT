<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Download the specified document.
     */
    public function download(Document $document)
    {
        $this->authorize('download', $document);

        if (! Storage::disk('local')->exists('documents/'.$document->stored_filename)) {
            abort(404, 'File not found on server.');
        }

        $path = Storage::disk('local')->path('documents/'.$document->stored_filename);

        return response()->download($path, $document->original_filename);
    }
}
