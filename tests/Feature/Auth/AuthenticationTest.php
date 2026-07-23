<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\LoginFormCard;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.login');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $component = Livewire::test(LoginFormCard::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'login',
            'record_type' => User::class,
            'record_id' => $user->id,
        ]);

        $this->assertSame(1, AuditLog::query()->where('action', 'login')->count());
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $component = Livewire::test(LoginFormCard::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password');

        $component->call('login');

        $component
            ->assertHasErrors()
            ->assertNoRedirect();

        $this->assertGuest();
        $this->assertSame(0, AuditLog::query()->where('action', 'login')->count());
    }

    public function test_users_are_rate_limited_after_five_failed_login_attempts(): void
    {
        $user = User::factory()->create();

        RateLimiter::clear(strtolower($user->email).'|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            Livewire::test(LoginFormCard::class)
                ->set('form.email', $user->email)
                ->set('form.password', 'wrong-password')
                ->call('login')
                ->assertHasErrors(['form.email']);
        }

        $component = Livewire::test(LoginFormCard::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password')
            ->call('login');

        $component->assertHasErrors(['form.email']);
        $this->assertGuest();
        $this->assertStringContainsString(
            'Too many login attempts',
            collect($component->errors()->get('form.email'))->implode(' ')
        );
    }

    public function test_navigation_menu_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response
            ->assertOk()
            ->assertSeeVolt('layout.navigation');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('layout.navigation');

        $component->call('logout');

        $component
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
