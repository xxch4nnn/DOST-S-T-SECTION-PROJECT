<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['file_group_id', 'name', 'metadata_template'])]
class FileType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'file_group_id',
        'name',
        'metadata_template',
    ];

    protected $casts = [
        'metadata_template' => 'array',
    ];

    public function fileGroup()
    {
        return $this->belongsTo(FileGroup::class);
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }
}
