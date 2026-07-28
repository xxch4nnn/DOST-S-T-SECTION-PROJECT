<?php

namespace App\Models;

use App\Models\FileType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'slug'])]
class FileGroup extends Model
{
    public $timestamps = false;

    public function fileType(){
        return $this->hasMany(FileType::class, 'file_group_id');
    }
}
