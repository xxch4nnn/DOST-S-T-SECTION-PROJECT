<?php

namespace App\Http\Controllers;

use App\Models\Document;
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
    public function download(Document $document): BinaryFileResponse
    {
        $this->authorize('download', $document);

        [$absolutePath, $fileName] = $this->resolveCurrentVersionFile($document);

        return response()->download($absolutePath, $fileName);
    }

    /**
     * Stream the current version inline for the document viewer.
     */
    public function viewFile(Document $document): BinaryFileResponse|StreamedResponse
    {
        $this->authorize('view', $document);

        [$absolutePath, $fileName, $mimeType] = $this->resolveCurrentVersionFile($document, withMime: true);

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * @return array{0: string, 1: string, 2?: string}
     */
    protected function resolveCurrentVersionFile(Document $document, bool $withMime = false): array
    {
        $version = $document->currentVersion;
        if (! $version) {
            abort(404, 'File not found on server.');
        }

        $path = $version->file_path ?: ('documents/'.$version->stored_filename);

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'File not found on server.');
        }

        $absolutePath = Storage::disk('local')->path($path);
        $fileName = $version->original_filename ?: basename($absolutePath);

        if (! $withMime) {
            return [$absolutePath, $fileName];
        }

        $mimeType = $version->mime_type
            ?: (function_exists('mime_content_type') ? @mime_content_type($absolutePath) : null)
            ?: 'application/octet-stream';

        return [$absolutePath, $fileName, $mimeType];
    }
}
