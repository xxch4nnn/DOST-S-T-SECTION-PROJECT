<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id', 'stored_filename', 'original_filename',
        'file_size_kb', 'version_number', 'replaced_by_user_id',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function replacer()
    {
        return $this->belongsTo(User::class, 'replaced_by_user_id');
    }
}
