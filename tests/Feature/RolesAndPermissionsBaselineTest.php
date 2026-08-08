<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesAndPermissionsBaselineTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_three_baseline_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertEqualsCanonicalizing(
            ['Super Admin', 'Admin', 'Encoder'],
            Role::query()->orderBy('name')->pluck('name')->all()
        );
    }

    public function test_super_admin_has_all_baseline_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $expected = [
            'viewAuditLogs',
            'manageUsers',
            'uploadDocuments',
            'editDocumentMetadata',
            'strikeOffDocuments',
            'viewReports',
            'viewNotifications',
            'viewScholars',
            'createScholars',
            'editScholars',
            'deleteScholars',
            'viewAdminRecords',
            'createAdminRecords',
            'editAdminRecords',
            'deleteAdminRecords',
        ];

        $this->assertEqualsCanonicalizing(
            $expected,
            Permission::query()->pluck('name')->all()
        );

        $superAdmin = Role::findByName('Super Admin');
        foreach ($expected as $permission) {
            $this->assertTrue(
                $superAdmin->hasPermissionTo($permission),
                "Super Admin missing permission [{$permission}]"
            );
        }
    }

    public function test_encoder_lacks_manage_users_and_destructive_admin_gates(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $encoder = Role::findByName('Encoder');

        $this->assertTrue($encoder->hasPermissionTo('uploadDocuments'));
        $this->assertTrue($encoder->hasPermissionTo('editDocumentMetadata'));
        $this->assertTrue($encoder->hasPermissionTo('viewReports'));
        $this->assertTrue($encoder->hasPermissionTo('viewNotifications'));
        $this->assertTrue($encoder->hasPermissionTo('viewScholars'));
        $this->assertTrue($encoder->hasPermissionTo('viewAdminRecords'));
        $this->assertFalse($encoder->hasPermissionTo('manageUsers'));
        $this->assertFalse($encoder->hasPermissionTo('strikeOffDocuments'));
        $this->assertFalse($encoder->hasPermissionTo('viewAuditLogs'));
        $this->assertFalse($encoder->hasPermissionTo('createAdminRecords'));
        $this->assertFalse($encoder->hasPermissionTo('deleteScholars'));
        $this->assertFalse($encoder->hasPermissionTo('deleteAdminRecords'));
    }

    public function test_seeded_test_admin_user_is_super_admin(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole('Super Admin'));
        $this->assertTrue($user->can('manageUsers'));
        $this->assertTrue($user->can('viewAuditLogs'));
    }

    public function test_encoder_user_cannot_manage_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $encoderUser = User::factory()->create(['email' => 'encoder@example.com']);
        $encoderUser->assignRole('Encoder');

        $this->assertTrue($encoderUser->can('uploadDocuments'));
        $this->assertFalse($encoderUser->can('manageUsers'));
        $this->assertFalse($encoderUser->hasAnyRole(['Super Admin', 'Admin']));
    }
}
