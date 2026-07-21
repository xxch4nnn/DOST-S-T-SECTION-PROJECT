<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'campus', 'is_available'];

    public function scholars()
    {
        return $this->hasMany(Scholar::class);
    }
}
