<?php

namespace Tests\Feature;

use App\Models\AdministrativeRecord;
use App\Models\FileType;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoutePermissionGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_encoder_can_open_scholars_and_is_forbidden_from_audit_logs(): void
    {
        $encoder = User::factory()->create([
            'email' => 'encoder-gates@example.com',
            'email_verified_at' => now(),
        ]);
        $encoder->assignRole('Encoder');

        $this->actingAs($encoder)
            ->get(route('scholars.index'))
            ->assertOk();

        $this->actingAs($encoder)
            ->get(route('audit-logs.index'))
            ->assertForbidden();

        $this->actingAs($encoder)
            ->get(route('admin-records.create'))
            ->assertForbidden();
    }

    public function test_encoder_can_view_admin_records_index_but_not_edit(): void
    {
        $encoder = User::factory()->create([
            'email' => 'encoder-admin@example.com',
            'email_verified_at' => now(),
        ]);
        $encoder->assignRole('Encoder');

        $record = AdministrativeRecord::create([
            'record_type' => 'Memorandum',
            'series_number' => 'Memo-GATE-1',
            'title' => 'Gate test',
            'created_by' => $encoder->id,
        ]);

        $this->actingAs($encoder)
            ->get(route('admin-records.index'))
            ->assertOk();

        $this->actingAs($encoder)
            ->get(route('admin-records.edit', $record))
            ->assertForbidden();
    }

    public function test_super_admin_can_open_audit_logs(): void
    {
        $admin = User::where('email', 'test@example.com')->first()
            ?? User::factory()->create([
                'email' => 'sa-gates@example.com',
                'email_verified_at' => now(),
            ]);

        if (! $admin->hasRole('Super Admin')) {
            $admin->assignRole('Super Admin');
        }

        // Ensure verified for middleware
        if ($admin->email_verified_at === null) {
            $admin->forceFill(['email_verified_at' => now()])->save();
        }

        $this->actingAs($admin)
            ->get(route('audit-logs.index'))
            ->assertOk();
    }

    public function test_user_without_role_cannot_open_dashboard(): void
    {
        $plain = User::factory()->create([
            'email' => 'norole@example.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($plain)
            ->get(route('dashboard'))
            ->assertForbidden();
    }

    public function test_document_download_returns_403_without_permission(): void
    {
        Storage::disk('local')->put('documents/gate-test.pdf', 'pdf-bytes');

        $owner = User::factory()->create(['email_verified_at' => now()]);
        $owner->assignRole('Encoder');

        $fileType = FileType::firstOrCreate(
            ['name' => 'Gate Memo'],
            ['metadata_template' => null, 'file_group_id' => null]
        );

        $record = AdministrativeRecord::create([
            'record_type' => 'Memorandum',
            'series_number' => 'Memo-DL-1',
            'title' => 'Download gate',
            'created_by' => $owner->id,
        ]);

        $document = $record->documents()->create([
            'file_type_id' => $fileType->id,
            'original_filename' => 'gate-test.pdf',
            'stored_filename' => 'gate-test.pdf',
            'mime_type' => 'application/pdf',
            'file_size_kb' => 1,
            'status' => 'active',
            'uploaded_by' => $owner->id,
        ]);

        $denied = User::factory()->create([
            'email' => 'denied-dl@example.com',
            'email_verified_at' => now(),
        ]);
        // No roles / permissions

        $this->actingAs($denied)
            ->get(route('documents.download', $document))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('documents.download', $document))
            ->assertOk();
    }
}
