<?php

declare(strict_types=1);

namespace App\Livewire\Notifications;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
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
     * Load notifications merging system audit logs and initial mock events.
     */
    public function loadNotifications(): void
    {
        // Check if notifications are stored in session for user state persistence
        $cachedNotifications = session()->get('user_notifications');

        if ($cachedNotifications && is_array($cachedNotifications) && count($cachedNotifications) > 0) {
            $this->notifications = $cachedNotifications;

            return;
        }

        $items = [];

        // Try reading recent real audit logs
        try {
            $recentAuditLogs = AuditLog::with('user')->latest()->take(10)->get();
            foreach ($recentAuditLogs as $log) {
                $actor = $log->user->name ?? 'Admin Name';
                $action = $log->action ?? 'modified';
                $target = $log->record_type ? class_basename($log->record_type).' #'.$log->record_id : 'system record';

                $items[] = [
                    'id' => 'log_'.$log->id,
                    'actor' => $actor,
                    'action_text' => $action.' '.$target,
                    'target_name' => '',
                    'is_unread' => true,
                    'time_ago' => $log->created_at ? $log->created_at->diffForHumans(short: true) : '5m ago',
                    'timestamp' => $log->created_at ? $log->created_at->timestamp : time(),
                    'type' => 'purple',
                ];
            }
        } catch (\Throwable) {
            // Fallback gracefully if database table is empty/unseeded
        }

        // Add rich initial notifications matching the Figma mockup (Image 1)
        $sampleNotifications = [
            [
                'id' => 'notif_1',
                'actor' => 'Admin Name',
                'action_text' => 'edited document metadata for',
                'target_name' => 'Fernandez, Gianfranco Miguel D.',
                'is_unread' => true,
                'time_ago' => '5m ago',
                'timestamp' => Carbon::now()->subMinutes(5)->timestamp,
                'type' => 'purple',
            ],
            [
                'id' => 'notif_2',
                'actor' => 'Admin Name',
                'action_text' => 'edited document metadata for',
                'target_name' => 'Fernandez, Gianfranco Miguel D.',
                'is_unread' => true,
                'time_ago' => '5m ago',
                'timestamp' => Carbon::now()->subMinutes(5)->timestamp,
                'type' => 'cyan',
            ],
            [
                'id' => 'notif_3',
                'actor' => 'Admin Name',
                'action_text' => 'edited document metadata for',
                'target_name' => 'Fernandez, Gianfranco Miguel D.',
                'is_unread' => true,
                'time_ago' => '5m ago',
                'timestamp' => Carbon::now()->subMinutes(5)->timestamp,
                'type' => 'green',
            ],
            [
                'id' => 'notif_4',
                'actor' => 'Admin Name',
                'action_text' => 'edited document metadata for',
                'target_name' => 'Fernandez, Gianfranco Miguel D.',
                'is_unread' => true,
                'time_ago' => '5m ago',
                'timestamp' => Carbon::now()->subMinutes(5)->timestamp,
                'type' => 'red',
            ],
            [
                'id' => 'notif_5',
                'actor' => 'Admin Name',
                'action_text' => 'edited document metadata for',
                'target_name' => 'Fernandez, Gianfranco Miguel D.',
                'is_unread' => true,
                'time_ago' => '5m ago',
                'timestamp' => Carbon::now()->subMinutes(5)->timestamp,
                'type' => 'yellow',
            ],
            [
                'id' => 'notif_6',
                'actor' => 'Admin Name',
                'action_text' => 'edited document metadata for',
                'target_name' => 'Fernandez, Gianfranco Miguel D.',
                'is_unread' => false,
                'time_ago' => '5m ago',
                'timestamp' => Carbon::now()->subMinutes(5)->timestamp,
                'type' => 'gray',
            ],
            [
                'id' => 'notif_7',
                'actor' => 'Admin Name',
                'action_text' => 'edited document metadata for',
                'target_name' => 'Fernandez, Gianfranco Miguel D.',
                'is_unread' => false,
                'time_ago' => '5m ago',
                'timestamp' => Carbon::now()->subMinutes(5)->timestamp,
                'type' => 'purple',
            ],
            [
                'id' => 'notif_8',
                'actor' => 'Admin Name',
                'action_text' => 'edited document metadata for',
                'target_name' => 'Fernandez, Gianfranco Miguel D.',
                'is_unread' => false,
                'time_ago' => '5m ago',
                'timestamp' => Carbon::now()->subMinutes(5)->timestamp,
                'type' => 'green',
            ],
        ];

        // Combine logs with mock items if list is small
        if (count($items) < 5) {
            $this->notifications = array_merge($items, $sampleNotifications);
        } else {
            $this->notifications = $items;
        }

        $this->saveState();
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
        foreach ($this->notifications as &$item) {
            if ($item['id'] === $id) {
                $item['is_unread'] = false;
                break;
            }
        }
        unset($item);

        $this->saveState();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        foreach ($this->notifications as &$item) {
            $item['is_unread'] = false;
        }
        unset($item);

        $this->saveState();

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

    /**
     * Persist current notifications state in session.
     */
    private function saveState(): void
    {
        session()->put('user_notifications', $this->notifications);
    }

    public function render(): View
    {
        return view('livewire.notifications.index', [
            'unreadCount' => $this->unreadCount,
            'filteredNotifications' => $this->filteredNotifications,
        ]);
    }
}
