<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\File;
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

    public function viewFile(File $file)
    {
        $path = null;

        if (!empty($file->file_path) && file_exists($file->file_path)) {
            $path = $file->file_path;
        } elseif (!empty($file->file_path) && Storage::disk('local')->exists($file->file_path)) {
            $path = Storage::disk('local')->path($file->file_path);
        } elseif (!empty($file->file_path) && Storage::disk('public')->exists($file->file_path)) {
            $path = Storage::disk('public')->path($file->file_path);
        }

        if (! $path || ! file_exists($path)) {
            abort(404, 'File not found on server.');
        }

        $mimeType = $file->mime_type ?? (function_exists('mime_content_type') ? @mime_content_type($path) : null) ?? 'application/pdf';

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . ($file->file_name ?? basename($path)) . '"',
        ]);
    }
}
