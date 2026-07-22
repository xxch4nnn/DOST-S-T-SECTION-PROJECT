<?php

namespace App\Livewire\Scholars;

use App\Models\Scholar;
use App\Models\Document;
use App\Models\FileType;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Show extends Component
{
    use WithFileUploads;

    public Scholar $scholar;
    public $file;
    public $file_type_id = '';

    public $documents = [];
    public $duplicateDocument = null;
    public $showDuplicateModal = false;

    public function mount(Scholar $scholar)
    {
        $this->scholar = $scholar->load(['scholarship', 'school', 'course', 'clearanceStatus']);
        $this->loadDocuments();
    }

    public function loadDocuments()
    {
        $this->documents = $this->scholar->documents()
            ->withTrashed()
            ->with(['fileType', 'uploader'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function uploadDocument()
    {
        $this->validate([
            'file' => 'required|file|max:10240|mimes:pdf,png,jpg,jpeg', // 10MB max
            'file_type_id' => 'required|exists:file_types,id',
        ]);

        // Check for duplicate active document of same type
        $duplicate = $this->scholar->documents()
            ->where('file_type_id', $this->file_type_id)
            ->where('status', 'active')
            ->first();

        if ($duplicate) {
            $this->duplicateDocument = $duplicate;
            $this->showDuplicateModal = true;
            return;
        }

        $this->storeNewDocument();
    }

    public function storeNewDocument()
    {
        $uuid = Str::uuid()->toString();
        $extension = $this->file->getClientOriginalExtension();
        $storedFilename = $uuid . '.' . $extension;
        $originalFilename = $this->file->getClientOriginalName();
        $mimeType = $this->file->getMimeType();
        $fileSizeKb = round($this->file->getSize() / 1024);

        // Store flat UUID in local private disk (as per ADR-005)
        $this->file->storeAs('documents', $storedFilename, 'local');

        $document = $this->scholar->documents()->create([
            'file_type_id' => $this->file_type_id,
            'original_filename' => $originalFilename,
            'stored_filename' => $storedFilename,
            'mime_type' => $mimeType,
            'file_size_kb' => $fileSizeKb,
            'status' => 'active',
            'uploaded_by' => auth()->id(),
        ]);

        $this->logAudit('upload', $document);

        $this->reset(['file', 'file_type_id', 'duplicateDocument', 'showDuplicateModal']);
        $this->loadDocuments();
        session()->flash('message', 'Document uploaded successfully.');
    }

    public function resolveDuplicate($option)
    {
        if ($option === 'cancel') {
            $this->reset(['file', 'file_type_id', 'duplicateDocument', 'showDuplicateModal']);
            return;
        }

        $this->validate([
            'file' => 'required|file|max:10240|mimes:pdf,png,jpg,jpeg',
            'file_type_id' => 'required|exists:file_types,id',
        ]);

        $uuid = Str::uuid()->toString();
        $extension = $this->file->getClientOriginalExtension();
        $storedFilename = $uuid . '.' . $extension;
        $originalFilename = $this->file->getClientOriginalName();
        $mimeType = $this->file->getMimeType();
        $fileSizeKb = round($this->file->getSize() / 1024);

        if ($option === 'keep_history') {
            \DB::transaction(function () use ($storedFilename, $originalFilename, $mimeType, $fileSizeKb) {
                // Move current active document values to version table
                $this->duplicateDocument->versions()->create([
                    'stored_filename' => $this->duplicateDocument->stored_filename,
                    'original_filename' => $this->duplicateDocument->original_filename,
                    'file_size_kb' => $this->duplicateDocument->file_size_kb,
                    'version_number' => $this->duplicateDocument->versions()->count() + 1,
                    'replaced_by_user_id' => auth()->id(),
                ]);

                // Store new file physically
                $this->file->storeAs('documents', $storedFilename, 'local');

                // Update active document metadata
                $before = $this->duplicateDocument->toArray();
                $this->duplicateDocument->update([
                    'stored_filename' => $storedFilename,
                    'original_filename' => $originalFilename,
                    'mime_type' => $mimeType,
                    'file_size_kb' => $fileSizeKb,
                ]);

                $this->logAudit('overwrite', $this->duplicateDocument, $before);
            });
        } elseif ($option === 'overwrite') {
            \DB::transaction(function () use ($storedFilename, $originalFilename, $mimeType, $fileSizeKb) {
                // Delete old file physically
                Storage::disk('local')->delete('documents/' . $this->duplicateDocument->stored_filename);

                // Store new file
                $this->file->storeAs('documents', $storedFilename, 'local');

                // Update active document metadata
                $before = $this->duplicateDocument->toArray();
                $this->duplicateDocument->update([
                    'stored_filename' => $storedFilename,
                    'original_filename' => $originalFilename,
                    'mime_type' => $mimeType,
                    'file_size_kb' => $fileSizeKb,
                ]);

                $this->logAudit('overwrite', $this->duplicateDocument, $before);
            });
        }

        $this->reset(['file', 'file_type_id', 'duplicateDocument', 'showDuplicateModal']);
        $this->loadDocuments();
        session()->flash('message', 'Document replaced successfully.');
    }

    public function strikeOff($documentId)
    {
        if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $document = Document::findOrFail($documentId);
        $before = $document->toArray();
        
        $document->update(['status' => 'struck_off']);
        $document->delete(); // soft delete

        $this->logAudit('strike_off', $document, $before);

        $this->loadDocuments();
        session()->flash('message', 'Document struck off successfully.');
    }

    public function undoStrikeOff($documentId)
    {
        if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $document = Document::withTrashed()->findOrFail($documentId);
        $before = $document->toArray();

        $document->restore();
        $document->update(['status' => 'active']);

        $this->logAudit('undo_strike_off', $document, $before);

        $this->loadDocuments();
        session()->flash('message', 'Document restored successfully.');
    }

    protected function logAudit(string $action, $record, array $before = null)
    {
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'record_type' => get_class($record),
            'record_id' => $record->id,
            'before_payload' => $before,
            'after_payload' => $record->toArray(),
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);
    }

    public function render()
    {
        return view('livewire.scholars.show', [
            'fileTypes' => FileType::orderBy('name')->get()
        ])->layout('layouts.app');
    }
}
