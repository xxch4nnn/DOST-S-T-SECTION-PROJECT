<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['municipality_id', 'name'])]
class Barangay extends Model
{
    public $timestamps = false;

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }
}
