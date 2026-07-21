<?php

namespace App\Livewire\AdminRecords;

use App\Models\AdministrativeRecord;
use App\Models\Document;
use App\Models\FileType;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Show extends Component
{
    use WithFileUploads;

    public AdministrativeRecord $record;
    public $file;
    public $file_type_id = '';

    public $documents = [];
    public $duplicateDocument = null;
    public $showDuplicateModal = false;

    public function mount(AdministrativeRecord $record)
    {
        $this->record = $record->load(['creator']);
        $this->loadDocuments();
    }

    public function loadDocuments()
    {
        $this->documents = $this->record->documents()
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
        $duplicate = $this->record->documents()
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

        // Store flat UUID in local private disk (as per ADR-005)
        $this->file->storeAs('documents', $storedFilename, 'local');

        $document = $this->record->documents()->create([
            'file_type_id' => $this->file_type_id,
            'original_filename' => $this->file->getClientOriginalName(),
            'stored_filename' => $storedFilename,
            'mime_type' => $this->file->getMimeType(),
            'file_size_kb' => round($this->file->getSize() / 1024),
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

        if ($option === 'keep_history') {
            \DB::transaction(function () use ($storedFilename) {
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
                    'original_filename' => $this->file->getClientOriginalName(),
                    'mime_type' => $this->file->getMimeType(),
                    'file_size_kb' => round($this->file->getSize() / 1024),
                ]);

                $this->logAudit('overwrite', $this->duplicateDocument, $before);
            });
        } elseif ($option === 'overwrite') {
            \DB::transaction(function () use ($storedFilename) {
                // Delete old file physically
                Storage::disk('local')->delete('documents/' . $this->duplicateDocument->stored_filename);

                // Store new file
                $this->file->storeAs('documents', $storedFilename, 'local');

                // Update active document metadata
                $before = $this->duplicateDocument->toArray();
                $this->duplicateDocument->update([
                    'stored_filename' => $storedFilename,
                    'original_filename' => $this->file->getClientOriginalName(),
                    'mime_type' => $this->file->getMimeType(),
                    'file_size_kb' => round($this->file->getSize() / 1024),
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
        return view('livewire.admin-records.show', [
            'fileTypes' => FileType::orderBy('name')->get()
        ])->layout('layouts.app');
    }
}
