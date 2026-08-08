<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'abbreviation', 'is_available'])]
class Course extends Model
{
    public $timestamps = false;
    public function scholars(){
        return $this->hasMany(Scholar::class, 'course_id');
    }
}
