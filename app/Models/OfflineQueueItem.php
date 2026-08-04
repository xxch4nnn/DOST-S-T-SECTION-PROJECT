<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OfflineQueueItem extends Model
{
    use HasFactory;

    protected $table = 'offline_queue';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'idempotency_key',
        'user_id',
        'action',
        'payload',
        'status',
        'attempts',
        'last_error',
        'available_at',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'available_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $item): void {
            if (empty($item->idempotency_key)) {
                $item->idempotency_key = (string) Str::uuid();
            }
            if (empty($item->status)) {
                $item->status = self::STATUS_PENDING;
            }
            if ($item->available_at === null) {
                $item->available_at = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDue($query)
    {
        return $query
            ->where('status', self::STATUS_PENDING)
            ->where(function ($q) {
                $q->whereNull('available_at')->orWhere('available_at', '<=', now());
            });
    }
}
