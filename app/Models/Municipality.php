<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['province_id', 'name'])]
class Municipality extends Model
{
    public $timestamps = false;

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function barangay()
    {
        return $this->hasMany(Barangay::class, 'barangay_id');
    }
}
