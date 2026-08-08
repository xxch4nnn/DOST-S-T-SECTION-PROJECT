<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    public $timestamps = false; 

    protected $fillable = [
        'document_id', 'version_number', 'file_name', 'file_path', 'file_size', 'file_type_id', 'uploaded_by'
    ];

    public function document(){
        return $this->belongsTo(Document::class);
    }

    public function fileType()
    {
        return $this->belongsTo(FileType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
