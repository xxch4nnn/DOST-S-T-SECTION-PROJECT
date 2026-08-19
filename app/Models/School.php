<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'campus', 'requirement_deadline', 'is_available'])]
class School extends Model
{
    public $timestamps = false;

    protected $casts = [
        'requirement_deadline' => 'date:Y-m-d',
    ];

    public function scholars()
    {
        return $this->hasMany(Scholar::class, 'school_id');
    }
}
