<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrativeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_type', 'series_number', 'title', 'recipient',
        'year', 'quarter', 'for_disposal', 'created_by'
    ];

    protected $casts = [
        'for_disposal' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
