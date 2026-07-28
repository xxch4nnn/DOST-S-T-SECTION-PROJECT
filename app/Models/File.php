<?php

namespace App\Models;

use App\Models\FileType;
use App\Observers\FileObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[Fillable(['file_type_id', 'file_name', 'file_path', 'file_size', 'uploaded_at', 'updated_at', 'deleted_at', 'mime_type', 'metadata'])]
#[ObservedBy([FileObserver::class])]
class File extends Model
{
    public $timestamps=false;
    protected $casts = [
        'metadata' => 'array',
    ];
    protected $fillable = [
        'file_type_id', 
        'file_name', 
        'file_path', 
        'file_size', 
        'uploaded_at', 
        'updated_at', 
        'deleted_at', 
        'mime_type', 
        'metadata'
    ];
    public function fileType(){
        return $this->belongsTo(FileType::class, "file_type_id");
    }
}
