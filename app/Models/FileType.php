<?php

namespace App\Models;

use App\Models\FileGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

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

    public function file(){
        return $this->hasMany(File::class, 'file_type_id');
    }
}
