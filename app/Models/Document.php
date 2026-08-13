<?php

namespace App\Models;

use App\Observers\DocumentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[ObservedBy(DocumentObserver::class)]
class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'documentable_type',
        'documentable_id',
        'status',
        'metadata',
        'date_issued',
    ];

    protected $casts = [
        'metadata' => 'array',
        'date_issued' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Document $document): void {
            if (empty($document->uuid)) {
                $document->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Create a thin document shell plus version 1 (file payload lives on the version).
     *
     * @param  array<string, mixed>  $shell
     * @param  array<string, mixed>  $version
     */
    public static function createWithInitialVersion(array $shell, array $version): self
    {
        return DB::transaction(function () use ($shell, $version) {
            $document = static::query()->create([
                'uuid' => $shell['uuid'] ?? (string) Str::uuid(),
                'documentable_type' => $shell['documentable_type'],
                'documentable_id' => $shell['documentable_id'],
                'status' => $shell['status'] ?? 'active',
                'metadata' => $shell['metadata'] ?? null,
                'date_issued' => $shell['date_issued'] ?? null,
            ]);

            $stored = $version['stored_filename'];
            $document->versions()->create([
                'file_type_id' => $version['file_type_id'],
                'file_path' => $version['file_path'] ?? ('documents/'.$stored),
                'original_filename' => $version['original_filename'],
                'stored_filename' => $stored,
                'mime_type' => $version['mime_type'] ?? 'application/octet-stream',
                'file_size_bytes' => $version['file_size_bytes']
                    ?? (max(1, (int) ($version['file_size_kb'] ?? 1)) * 1024),
                'version_number' => $version['version_number'] ?? 1,
                'uploaded_by' => $version['uploaded_by'] ?? auth()->id(),
            ]);

            return $document->load('currentVersion.fileType', 'currentVersion.uploader');
        });
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class, 'document_uuid', 'uuid');
    }

    public function currentVersion(): HasOne
    {
        return $this->hasOne(DocumentVersion::class, 'document_uuid', 'uuid')
            ->latestOfMany('version_number');
    }

    public function getOriginalFilenameAttribute(): ?string
    {
        return $this->relationLoaded('currentVersion')
            ? $this->currentVersion?->original_filename
            : $this->currentVersion()->value('original_filename');
    }

    public function getStoredFilenameAttribute(): ?string
    {
        return $this->relationLoaded('currentVersion')
            ? $this->currentVersion?->stored_filename
            : $this->currentVersion()->value('stored_filename');
    }

    public function getMimeTypeAttribute(): ?string
    {
        return $this->relationLoaded('currentVersion')
            ? $this->currentVersion?->mime_type
            : $this->currentVersion()->value('mime_type');
    }

    public function getFileSizeKbAttribute(): ?int
    {
        $bytes = $this->relationLoaded('currentVersion')
            ? $this->currentVersion?->file_size_bytes
            : $this->currentVersion()->value('file_size_bytes');

        return $bytes === null ? null : max(1, (int) ceil($bytes / 1024));
    }

    public function getFilePathAttribute(): ?string
    {
        return $this->relationLoaded('currentVersion')
            ? $this->currentVersion?->file_path
            : $this->currentVersion()->value('file_path');
    }

    public function getFileTypeIdAttribute(): ?int
    {
        $value = $this->relationLoaded('currentVersion')
            ? $this->currentVersion?->file_type_id
            : $this->currentVersion()->value('file_type_id');

        return $value === null ? null : (int) $value;
    }

    public function getUploadedByAttribute(): ?int
    {
        $value = $this->relationLoaded('currentVersion')
            ? $this->currentVersion?->uploaded_by
            : $this->currentVersion()->value('uploaded_by');

        return $value === null ? null : (int) $value;
    }

    public function getFileTypeAttribute(): ?FileType
    {
        $this->loadMissing('currentVersion.fileType');

        return $this->currentVersion?->fileType;
    }

    public function getUploaderAttribute(): ?User
    {
        $this->loadMissing('currentVersion.uploader');

        return $this->currentVersion?->uploader;
    }
}
