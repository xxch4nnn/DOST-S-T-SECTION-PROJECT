<?php

namespace App\Models;

use App\Models\Scholar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'campus', 'requirement_deadline', 'is_available'])]
class School extends Model
{
    public $timestamps = false;
    protected $casts = [
        'requirement_deadline' => 'date:Y-m-d'
    ];
    
    public function scholars(){
        return $this->hasMany(Scholar::class, "school_id");
    }
}
