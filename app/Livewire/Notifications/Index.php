<?php

declare(strict_types=1);

namespace App\Livewire\Notifications;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Notifications - DOSTorage')]
class Index extends Component
{
    public string $filter = 'all'; // 'all' or 'unread'

    /**
     * Array of notification items.
     *
     * @var array<int, array<string, mixed>>
     */
    public array $notifications = [];

    public function mount(): void
    {
        $this->loadNotifications();
    }

    /**
     * Load notifications from system audit logs.
     */
    public function loadNotifications(): void
    {
        $readIds = session()->get('read_notifications', []);

        $items = [];

        try {
            $recentAuditLogs = AuditLog::with('user')->latest()->take(30)->get();
            foreach ($recentAuditLogs as $log) {
                $actor = $log->user->name ?? 'System';
                $action = $log->action ?? 'updated';
                $target = $log->record_type ? class_basename($log->record_type).' #'.$log->record_id : 'system record';
                $id = 'log_'.$log->id;

                $items[] = [
                    'id' => $id,
                    'actor' => $actor,
                    'action_text' => $action.' for',
                    'target_name' => $target,
                    'is_unread' => ! in_array($id, $readIds, true),
                    'time_ago' => $log->created_at ? $log->created_at->diffForHumans(short: true) : 'just now',
                    'timestamp' => $log->created_at ? $log->created_at->timestamp : time(),
                    'type' => 'purple',
                ];
            }
        } catch (\Throwable) {
            // Fallback gracefully if database table is empty or unmigrated
        }

        $this->notifications = $items;
    }

    /**
     * Switch current list filter.
     */
    public function setFilter(string $filter): void
    {
        $this->filter = in_array($filter, ['all', 'unread'], true) ? $filter : 'all';
    }

    /**
     * Mark a single notification item as read.
     */
    public function markAsRead(string $id): void
    {
        $readIds = session()->get('read_notifications', []);
        if (! in_array($id, $readIds, true)) {
            $readIds[] = $id;
            session()->put('read_notifications', $readIds);
        }

        foreach ($this->notifications as &$item) {
            if ($item['id'] === $id) {
                $item['is_unread'] = false;
                break;
            }
        }
        unset($item);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        $allIds = array_column($this->notifications, 'id');
        $readIds = array_values(array_unique(array_merge(session()->get('read_notifications', []), $allIds)));
        session()->put('read_notifications', $readIds);

        foreach ($this->notifications as &$item) {
            $item['is_unread'] = false;
        }
        unset($item);

        // Dispatch a corner toast alert confirming action
        $this->dispatch('notify', [
            'message' => 'All notifications marked as read',
            'type' => 'green',
        ]);
    }

    /**
     * Trigger a demo corner toast alert banner (for testing Image 2 styles).
     */
    public function triggerDemoAlert(string $type, string $message): void
    {
        $this->dispatch('notify', [
            'message' => $message,
            'type' => $type,
        ]);
    }

    /**
     * Get computed count of unread notifications.
     */
    public function getUnreadCountProperty(): int
    {
        return count(array_filter($this->notifications, fn ($n) => ! empty($n['is_unread'])));
    }

    /**
     * Get filtered list of notifications for the view.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFilteredNotificationsProperty(): array
    {
        if ($this->filter === 'unread') {
            return array_values(array_filter($this->notifications, fn ($n) => ! empty($n['is_unread'])));
        }

        return $this->notifications;
    }

    public function render(): View
    {
        return view('livewire.notifications.index', [
            'unreadCount' => $this->unreadCount,
            'filteredNotifications' => $this->filteredNotifications,
        ]);
    }
}
