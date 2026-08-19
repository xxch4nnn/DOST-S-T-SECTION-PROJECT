<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['region_id', 'name']) ]
class Province extends Model
{
    public $timestamps = false;

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function municipality()
    {
        return $this->hasMany(Municipality::class, 'municipality_id');
    }
}
