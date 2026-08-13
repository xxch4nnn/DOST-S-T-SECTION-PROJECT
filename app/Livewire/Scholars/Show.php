<?php

namespace App\Livewire\Scholars;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\FileType;
use App\Models\Scholar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

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
            ->with(['currentVersion.fileType', 'currentVersion.uploader'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function uploadDocument()
    {
        $this->validate([
            'file' => 'required|file|max:10240|mimes:pdf,png,jpg,jpeg', // 10MB max
            'file_type_id' => 'required|exists:file_types,id',
        ]);

        $duplicate = $this->scholar->documents()
            ->where('status', 'active')
            ->whereHas('currentVersion', fn ($q) => $q->where('file_type_id', $this->file_type_id))
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
        $storedFilename = $uuid.'.'.$extension;
        $originalFilename = $this->file->getClientOriginalName();
        $mimeType = $this->file->getMimeType();
        $fileSizeBytes = max(1, (int) $this->file->getSize());

        $this->file->storeAs('documents', $storedFilename, 'local');

        $document = Document::createWithInitialVersion(
            [
                'documentable_type' => Scholar::class,
                'documentable_id' => $this->scholar->id,
                'status' => 'active',
            ],
            [
                'file_type_id' => $this->file_type_id,
                'original_filename' => $originalFilename,
                'stored_filename' => $storedFilename,
                'file_path' => 'documents/'.$storedFilename,
                'mime_type' => $mimeType,
                'file_size_bytes' => $fileSizeBytes,
                'uploaded_by' => auth()->id(),
            ]
        );

        // DocumentObserver writes audit_logs on create (action=created); avoid duplicate upload row.

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
        $storedFilename = $uuid.'.'.$extension;
        $originalFilename = $this->file->getClientOriginalName();
        $mimeType = $this->file->getMimeType();
        $fileSizeBytes = max(1, (int) $this->file->getSize());

        if ($option === 'keep_history') {
            DB::transaction(function () use ($storedFilename, $originalFilename, $mimeType, $fileSizeBytes) {
                $before = $this->duplicateDocument->load('currentVersion')->toArray();

                $this->file->storeAs('documents', $storedFilename, 'local');

                $this->duplicateDocument->versions()->create([
                    'file_type_id' => $this->file_type_id,
                    'stored_filename' => $storedFilename,
                    'original_filename' => $originalFilename,
                    'file_path' => 'documents/'.$storedFilename,
                    'mime_type' => $mimeType,
                    'file_size_bytes' => $fileSizeBytes,
                    'version_number' => ($this->duplicateDocument->versions()->max('version_number') ?? 0) + 1,
                    'uploaded_by' => auth()->id(),
                ]);

                $this->logAudit('overwrite', $this->duplicateDocument->fresh('currentVersion'), $before);
            });
        } elseif ($option === 'overwrite') {
            DB::transaction(function () use ($storedFilename, $originalFilename, $mimeType, $fileSizeBytes) {
                $document = $this->duplicateDocument->load('currentVersion');
                $before = $document->toArray();
                $current = $document->currentVersion;

                if ($current?->file_path) {
                    Storage::disk('local')->delete($current->file_path);
                } elseif ($current?->stored_filename) {
                    Storage::disk('local')->delete('documents/'.$current->stored_filename);
                }

                $this->file->storeAs('documents', $storedFilename, 'local');

                if ($current) {
                    $current->update([
                        'file_type_id' => $this->file_type_id,
                        'stored_filename' => $storedFilename,
                        'original_filename' => $originalFilename,
                        'file_path' => 'documents/'.$storedFilename,
                        'mime_type' => $mimeType,
                        'file_size_bytes' => $fileSizeBytes,
                        'uploaded_by' => auth()->id(),
                    ]);
                }

                $this->logAudit('overwrite', $document->fresh('currentVersion'), $before);
            });
        }

        $this->reset(['file', 'file_type_id', 'duplicateDocument', 'showDuplicateModal']);
        $this->loadDocuments();
        session()->flash('message', 'Document replaced successfully.');
    }

    public function strikeOff($documentId)
    {
        if (! auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
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
        if (! auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
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

    protected function logAudit(string $action, $record, ?array $before = null)
    {
        AuditLog::create([
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
            'fileTypes' => FileType::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
