<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Notifications\Index as NotificationsIndex;
use App\Models\AuditLog;
use App\Models\Scholar;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function createAuthorizedUser(): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('Encoder');

        return $user;
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('notifications.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_notifications_page(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get(route('notifications.index'));
        $response->assertOk();
        $response->assertSeeLivewire(NotificationsIndex::class);
        $response->assertSee('Notifications');
        $response->assertSee('All');
        $response->assertSee('Unread');
        $response->assertSee('Mark all as read');
    }

    public function test_notifications_component_initializes_with_items_and_unread_count(): void
    {
        $user = $this->createAuthorizedUser();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'edited document metadata',
            'record_type' => Scholar::class,
            'record_id' => 1,
            'ip_address' => '127.0.0.1',
        ]);

        Livewire::actingAs($user)
            ->test(NotificationsIndex::class)
            ->assertSet('filter', 'all')
            ->assertCount('notifications', 1)
            ->assertSee($user->name)
            ->assertSee('edited document metadata for');
    }

    public function test_can_filter_notifications_by_unread(): void
    {
        $user = $this->createAuthorizedUser();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'created record',
            'record_type' => Scholar::class,
            'record_id' => 1,
            'ip_address' => '127.0.0.1',
        ]);

        $component = Livewire::actingAs($user)
            ->test(NotificationsIndex::class)
            ->call('setFilter', 'unread')
            ->assertSet('filter', 'unread');

        $filtered = $component->get('filteredNotifications');
        $this->assertNotEmpty($filtered);

        foreach ($filtered as $item) {
            $this->assertTrue((bool) $item['is_unread']);
        }
    }

    public function test_can_mark_individual_notification_as_read(): void
    {
        $user = $this->createAuthorizedUser();

        $log = AuditLog::create([
            'user_id' => $user->id,
            'action' => 'uploaded document',
            'record_type' => Scholar::class,
            'record_id' => 1,
            'ip_address' => '127.0.0.1',
        ]);

        $component = Livewire::actingAs($user)
            ->test(NotificationsIndex::class);

        $initialUnread = $component->get('unreadCount');
        $this->assertGreaterThan(0, $initialUnread);

        $component->call('markAsRead', 'log_'.$log->id);

        $notifications = $component->get('notifications');
        $notif1 = collect($notifications)->firstWhere('id', 'log_'.$log->id);

        $this->assertFalse((bool) $notif1['is_unread']);
        $this->assertEquals($initialUnread - 1, $component->get('unreadCount'));
    }

    public function test_can_mark_all_notifications_as_read_and_dispatches_toast(): void
    {
        $user = $this->createAuthorizedUser();

        Livewire::actingAs($user)
            ->test(NotificationsIndex::class)
            ->call('markAllAsRead')
            ->assertSet('unreadCount', 0)
            ->assertDispatched('notify', function (string $eventName, array $params) {
                $payload = $params[0] ?? $params;

                return ($payload['message'] ?? '') === 'All notifications marked as read'
                    && ($payload['type'] ?? '') === 'green';
            });
    }

    public function test_can_trigger_demo_corner_alert(): void
    {
        $user = $this->createAuthorizedUser();

        Livewire::actingAs($user)
            ->test(NotificationsIndex::class)
            ->call('triggerDemoAlert', 'purple', 'Test Purple Alert Banner')
            ->assertDispatched('notify', function (string $eventName, array $params) {
                $payload = $params[0] ?? $params;

                return ($payload['message'] ?? '') === 'Test Purple Alert Banner'
                    && ($payload['type'] ?? '') === 'purple';
            });
    }
}
