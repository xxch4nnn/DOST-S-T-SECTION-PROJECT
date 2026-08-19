<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\File;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Download the specified document (current version).
     */
    public function download(string|int $id)
    {
        session_write_close();

        $relativePath = null;
        $fileName = null;

        $doc = Document::where('uuid', $id)->first();
        if ($doc) {
            $version = $doc->currentVersion;
            $relativePath = $version?->file_path;
            $fileName = $version?->original_filename ?? $version?->stored_filename;
        } else {
            $relativePath = (string) $id;
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

        $doc = Document::where('uuid', $id)->first();
        if ($doc) {
            $version = $doc->currentVersion;
            $relativePath = $version?->file_path;
            $fileName = $version?->original_filename ?? $version?->stored_filename;
        } else {
            $relativePath = (string) $id;
        }

        if ($relativePath && Storage::disk('local')->exists($relativePath)) {
            $absolutePath = Storage::disk('local')->path($relativePath);
        } elseif ($relativePath && file_exists($relativePath)) {
            $absolutePath = $relativePath;
        } else {
            abort(404, 'File not found on server.');
        }

        $lastModified = file_exists($absolutePath) ? filemtime($absolutePath) : time();
        $versionKey = $version ? ($version->id.'-'.$version->version_number) : $id;
        $etag = '"'.md5($versionKey.'-'.$lastModified).'"';

        // Check if client sent If-None-Match header matching current ETag
        $ifNoneMatch = request()->header('If-None-Match');
        if ($ifNoneMatch && (trim($ifNoneMatch) === $etag || trim($ifNoneMatch) === '*')) {
            return response()->noContent(304, [
                'ETag' => $etag,
                'Cache-Control' => 'private, no-cache, revalidate',
            ]);
        }

        $mimeType = (function_exists('mime_content_type') ? @mime_content_type($absolutePath) : null)
            ?? 'application/pdf';

        return response()->file($absolutePath, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . ($fileName ?? basename($absolutePath)) . '"',
            'ETag'                => $etag,
            'Cache-Control'       => 'private, no-cache, revalidate',
        ]);
    }
}
