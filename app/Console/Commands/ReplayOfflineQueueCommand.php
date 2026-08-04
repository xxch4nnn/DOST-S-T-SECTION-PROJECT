<?php

namespace App\Console\Commands;

use App\Models\OfflineQueueItem;
use Illuminate\Console\Command;

/**
 * Scaffold replay for offline_queue outbox rows (V1 demo stub).
 * Real mutation handlers land in later slices.
 */
class ReplayOfflineQueueCommand extends Command
{
    protected $signature = 'offline:replay {--limit=50 : Max pending items to process}';

    protected $description = 'Replay due offline_queue mutations (scaffold)';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $items = OfflineQueueItem::query()->due()->orderBy('id')->limit($limit)->get();

        if ($items->isEmpty()) {
            $this->info('No due offline_queue items.');

            return self::SUCCESS;
        }

        foreach ($items as $item) {
            $item->update([
                'status' => OfflineQueueItem::STATUS_PROCESSING,
                'attempts' => $item->attempts + 1,
            ]);

            try {
                // V1 scaffold: mark complete without side effects.
                // Wire action handlers (upload, metadata edit, etc.) in later PRs.
                $item->update([
                    'status' => OfflineQueueItem::STATUS_COMPLETED,
                    'processed_at' => now(),
                    'last_error' => null,
                ]);
                $this->line("Completed #{$item->id} [{$item->action}]");
            } catch (\Throwable $e) {
                $item->update([
                    'status' => OfflineQueueItem::STATUS_FAILED,
                    'last_error' => $e->getMessage(),
                ]);
                $this->error("Failed #{$item->id}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
