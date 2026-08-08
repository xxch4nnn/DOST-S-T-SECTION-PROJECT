<?php

namespace App\Models;

use App\Observers\DocumentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([DocumentObserver::class])]
class Document extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'uuid', 'documentable_type', 'documentable_id',
        'metadata', 'deleted_at'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function documentVersion()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function latestVersion()
    {
        return $this->hasOne(DocumentVersion::class)->latestOfMany('version_number');
    }
}
