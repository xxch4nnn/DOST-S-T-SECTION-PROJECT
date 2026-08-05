<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\File;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    use AuthorizesRequests;

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

    public function viewFile(File $file)
    {
        $this->authorize('download', $file);

        if (! Storage::disk('local')->exists('documents/'.$file->file_name)) {
            abort(404, 'File not found on server.');
        }

        $mimeType = $file->mime_type ?? (function_exists('mime_content_type') ? @mime_content_type($file->file_path) : null) ?? 'application/pdf';

        return response()->file($file->file_path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . ($file->file_name ?? basename($file->file_path)) . '"',
        ]);
    }
}
