<?php

namespace Tests\Feature;

use App\Models\OfflineQueueItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OfflineQueueScaffoldTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_enqueue_and_replay_offline_queue_item(): void
    {
        $user = User::factory()->create();

        $item = OfflineQueueItem::create([
            'user_id' => $user->id,
            'action' => 'document.metadata.update',
            'payload' => ['document_id' => 1, 'metadata' => ['year' => 2026]],
        ]);

        $this->assertSame(OfflineQueueItem::STATUS_PENDING, $item->status);
        $this->assertNotEmpty($item->idempotency_key);

        Artisan::call('offline:replay', ['--limit' => 10]);

        $item->refresh();
        $this->assertSame(OfflineQueueItem::STATUS_COMPLETED, $item->status);
        $this->assertNotNull($item->processed_at);
        $this->assertSame(1, $item->attempts);
    }
}
