<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'documentable_type', 'documentable_id',
        'file_type_id', 'original_filename', 'stored_filename',
        'mime_type', 'file_size_kb', 'status', 'uploaded_by',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function fileType()
    {
        return $this->belongsTo(FileType::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }
}
