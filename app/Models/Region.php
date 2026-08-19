<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'abbreviation', 'is_available'])]
class Region extends Model
{
    public $timestamps = false;

    public function scholars()
    {
        return $this->hasMany(Scholar::class, 'region_id');
    }

    public function province()
    {
        return $this->hasMany(Province::class, 'province_id');
    }
}
