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
     * Download the specified document or file.
     */
    public function download(string|int $id)
    {
        session_write_close();

        $relativePath = null;
        $fileName = null;

        $doc = Document::find($id);
        if ($doc) {
            $version = $doc->documentVersion()->orderByDesc('version_number')->first();
            $relativePath = $version?->file_path;
            $fileName = $version?->file_name;
        } else {
            $relativePath = $id;
        }

        if ($relativePath && Storage::disk('local')->exists($relativePath)) {
            $absolutePath = Storage::disk('local')->path($relativePath);
        } elseif ($relativePath && file_exists($relativePath)) {
            $absolutePath = $relativePath;
        } else {
            abort(404, 'File not found on server.');
        }

        return response()->download($absolutePath, $fileName ?? basename($absolutePath));
    }

    /**
     * View/Stream the specified file inline.
     */
    public function viewFile(string|int $id)
    {
        session_write_close();

        $relativePath = null;
        $fileName = null;

        $doc = Document::find($id);
        if ($doc) {
            $version = $doc->documentVersion()->orderByDesc('version_number')->first();
            $relativePath = $version?->file_path;
            $fileName = $version?->file_name;
        } else {
            $relativePath = $id;
        }

        if ($relativePath && Storage::disk('local')->exists($relativePath)) {
            $absolutePath = Storage::disk('local')->path($relativePath);
        } elseif ($relativePath && file_exists($relativePath)) {
            $absolutePath = $relativePath;
        } else {
            abort(404, 'File not found on server.');
        }

        $mimeType = (function_exists('mime_content_type') ? @mime_content_type($absolutePath) : null) 
            ?? 'application/pdf';

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . ($fileName ?? basename($absolutePath)) . '"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
