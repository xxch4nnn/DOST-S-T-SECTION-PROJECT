<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'is_available'])]
class ScholarshipType extends Model
{
    public $timestamps=false;
    public function scholars(){
        return $this->hasMany(Scholar::class, "scholarship_type_id");
    }
}
