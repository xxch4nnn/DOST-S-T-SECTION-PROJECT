<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'year'];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
