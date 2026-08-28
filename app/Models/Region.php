<?php

namespace App\Models;

use App\Models\Scholar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'abbreviation', 'is_available'])]
class Region extends Model
{
    public $timestamps=false;
    public function scholars(){
        return $this->hasMany(Scholar::class, "region_id");
    }
}
