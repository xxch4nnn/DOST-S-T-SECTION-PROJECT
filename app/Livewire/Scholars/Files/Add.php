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

class Add extends Component
{
    use WithFileUploads;

    #[Url]
    public $return_url = '/scholars';

    // Form fields
    public $file_type_id = '';

    public $file_name = '';

    public $mime_type = '';

    public $file_size = '';

    public array|null $metadata = [];

    public $metadataObj;

    public Scholar|null $scholar;

    public Document|null $document;

    public $compiledFile;

    public function mount(Scholar $scholar): void   
    {
        // We need to find a way to populate the areas here
        $this->document = null;
        $this->metadata = null;
        $version = -1;
        $this->file_type_id = -1;
        $this->file_name = '';
        $this->mime_type = null;
        $this->file_size = -1;
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
                            ->where('documentable_id', $this->scholar->id);
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
        
        Storage::disk('local')->put($relativePath, $pdfContent);
        $metadata = $this->metadata;
        $dateIssued = $metadata['date_issued'] ?? $this->date_issued;

        Document::createWithInitialVersion(
            [
                'uuid' => (string) Str::uuid(),
                'documentable_type' => Scholar::class,
                'documentable_id' => $this->scholar->id,
                'date_issued' => $metadata['date_issued'],
                'status' => 'active',
                'metadata' => $this->metadata,
            ],
            [
                'file_type_id' => $this->file_type_id,
                'original_filename' => $this->file_name,
                'stored_filename' => $storedFilename,
                'file_path' => $relativePath,
                'mime_type' => 'application/pdf',
                'file_size_bytes' => strlen($pdfContent),
                'version_number' => 1,
                'uploaded_by' => auth()->id(),
            ]
        );

        unset($pdfContent);

        session()->flash('success', 'Document updated and saved successfully!');

        return redirect($this->return_url ?? route('scholars.show', $this->scholar->id));
    }

    // Runs every time a component is updated
    public function render()
    {
        return view('livewire.scholars.files.add')->layout('layouts.app');
    }
}
