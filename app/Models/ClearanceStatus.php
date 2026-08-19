<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'is_available'])]
class ClearanceStatus extends Model
{
    public $timestamps = false;

    public function scholars()
    {
        return $this->hasMany(Scholar::class, 'clearance_status_id');
    }
}
