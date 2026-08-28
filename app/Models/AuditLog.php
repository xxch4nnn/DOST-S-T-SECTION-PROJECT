<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    // public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'record_type', 'record_id',
        'before_payload', 'after_payload', 'ip_address',
    ];

    protected $casts = [
        'before_payload' => 'array',
        'after_payload' => 'array',
    ];

    public function loggable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
