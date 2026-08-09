<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Download the specified document (current version).
     */
    public function download(Document $document)
    {
        $this->authorize('download', $document);

        $version = $document->currentVersion;
        if (! $version) {
            abort(404, 'File not found on server.');
        }

        $path = $version->file_path ?: ('documents/'.$version->stored_filename);

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'File not found on server.');
        }

        return response()->download(
            Storage::disk('local')->path($path),
            $version->original_filename
        );
    }
}
