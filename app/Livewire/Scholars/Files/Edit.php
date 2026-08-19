<?php

namespace App\Livewire\Scholars\Files;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\FileType;
use App\Models\Scholar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    #[Url]
    public $return_url = '/scholars';

    // Form fields
    public $file_type_id = '';

    public $file_name = '';

    public $mime_type = '';

    public $file_size = '';

    public array $metadata = [];

    public $metadataObj;

    public Scholar $scholar;

    public Document $document;

    public $compiledFile;

    public function mount(?Scholar $scholar = null, ?Document $document = null): void
    {
        // Eager load the latest version and file type
        $this->document = $document;
        $this->document->load('currentVersion.fileType');
        $this->metadata = $this->document->metadata ?? [];
        $this->metadata['date_issued'] = $this->document->date_issued;

        $version = $this->document->currentVersion;
        $this->file_type_id = $version?->file_type_id;
        $this->file_name = $version?->original_filename ?? $version?->stored_filename;
        $this->mime_type = $version?->mime_type;
        $this->file_size = $version?->file_size_bytes;

        // dd($version, $this->metadata);
    }

    #[Computed]
    public function fileTypes()
    {
        return FileType::whereHas('fileGroup', function ($q) {
            $q->where('slug', 'scholarly_documents');
        })->orderBy('name', 'asc')->get();
    }

    public function saveCompiledPdf(string $base64Data, string $filename = '')
    {
        @ini_set('memory_limit', '512M');

        $this->validate([
            'file_type_id' => 'required|exists:file_types,id',
            'file_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $exists = DocumentVersion::whereHas('document', function ($query) {
                        $query->where('documentable_type', Scholar::class)
                            ->where('documentable_id', $this->scholar->id)
                            ->where('uuid', '!=', $this->document->uuid);
                    })->where('original_filename', $value)->exists();

                    if ($exists) {
                        $fail("The document name '{$value}' is already used by another file for this scholar.");
                    }
                },
            ],
        ]);

        if (str_contains($base64Data, ',')) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        }

        $pdfContent = base64_decode($base64Data);
        unset($base64Data);

        if (! $pdfContent || strlen($pdfContent) === 0) {
            session()->flash('error', 'Failed to decode compiled PDF binary payload.');

            return;
        }

        $storedFilename = (string) Str::uuid().'.pdf';
        $fileType = FileType::find($this->file_type_id);
        $typeFolder = $fileType ? Str::slug($fileType->name, '_') : '';
        $relativePath = 'documents/'.($typeFolder ? $typeFolder.'/' : '').$storedFilename;

        DB::transaction(function () use ($filename, $storedFilename, $relativePath, &$pdfContent) {
            // Save binary PDF directly to storage disk
            Storage::disk('local')->put($relativePath, $pdfContent);

            $metadata = $this->metadata;
            $dateIssued = $metadata['date_issued'] ?? $this->document->date_issued;
            unset($metadata['date_issued']);

            // Update document metadata & date_issued
            $this->document->update([
                'date_issued' => $dateIssued,
                'metadata' => $metadata,
            ]);

            // 2. Increment version number for history tracking
            $nextVersion = $this->document->versions()->count() + 1;

            // 3. Insert new DocumentVersion record with updated file_type_id & file_name
            $this->document->versions()->create([
                'file_type_id' => $this->file_type_id,
                'original_filename' => $this->file_name ?: $filename,
                'stored_filename' => $storedFilename,
                'file_path' => $relativePath,
                'mime_type' => 'application/pdf',
                'file_size_bytes' => strlen($pdfContent),
                'version_number' => $nextVersion,
                'uploaded_by' => auth()->id(),
            ]);

            $this->document->load('currentVersion.fileType');
        });

        unset($pdfContent);

        session()->flash('success', 'Document updated and saved successfully!');

        return redirect($this->return_url ?? route('scholars.show', $this->scholar->id));
    }

    // Runs every time a component is updated
    public function render()
    {
        return view('livewire.scholars.files.edit')->layout('layouts.app');
    }
}
