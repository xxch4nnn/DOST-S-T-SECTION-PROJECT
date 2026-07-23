<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action', 'record_type', 'record_id',
        'before_payload', 'after_payload', 'ip_address',
    ];

    protected $casts = [
        'before_payload' => 'array',
        'after_payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
