<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVersion extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'document_uuid',
        'file_type_id',
        'file_path',
        'original_filename',
        'stored_filename',
        'mime_type',
        'file_size_bytes',
        'version_number',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
        'version_number' => 'integer',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_uuid', 'uuid');
    }

    public function fileType(): BelongsTo
    {
        return $this->belongsTo(FileType::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeKbAttribute(): int
    {
        return max(1, (int) ceil(($this->file_size_bytes ?? 0) / 1024));
    }
}
