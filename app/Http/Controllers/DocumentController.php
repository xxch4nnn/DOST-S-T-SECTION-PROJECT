<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Download the specified document.
     */
    // public function download(File $document)
    // {
    //     // Check file exists
    //     if (! Storage::disk('local')->exists('documents/'.$document->stored_filename)) {
    //         abort(404, 'File not found on server.');
    //     }

    //     $path = Storage::disk('local')->path('documents/'.$document->stored_filename);

    //     return response()->download($path, $document->original_filename);
    // }
}
