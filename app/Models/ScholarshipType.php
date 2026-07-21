<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_available'];

    public function scholars()
    {
        return $this->hasMany(Scholar::class);
    }
}
