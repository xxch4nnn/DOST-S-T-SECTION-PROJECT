<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\ClearanceStatus;
use App\Models\Course;
use App\Models\Document;
use App\Models\FileType;
use App\Models\Region;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarshipType;
use App\Models\School;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_audit_log_with_record_columns_on_document_create_update(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('Encoder');
        $this->actingAs($user);

        $scholarship = Scholarship::create(['name' => 'RA 7687', 'is_available' => true]);
        $scholarshipType = ScholarshipType::create(['name' => 'Undergrad', 'is_available' => true]);
        $school = School::create(['name' => 'Test U', 'is_available' => true]);
        $course = Course::create(['name' => 'BS CS', 'abbreviation' => 'BSCS', 'is_available' => true]);
        $region = Region::create(['name' => 'XI', 'abbreviation' => 'R11', 'is_available' => true]);
        $status = ClearanceStatus::create(['name' => 'Active', 'is_available' => true]);
        $fileType = FileType::create(['name' => 'Notice of Award']);

        $scholar = Scholar::create([
            'first_name' => 'Ana',
            'last_name' => 'Reyes',
            'year_of_award' => 2024,
            'scholarship_id' => $scholarship->id,
            'scholarship_type_id' => $scholarshipType->id,
            'school_id' => $school->id,
            'course_id' => $course->id,
            'region_id' => $region->id,
            'clearance_status_id' => $status->id,
            'spas_no' => '2024-DOC-OBS-1',
        ]);

        $document = Document::createWithInitialVersion(
            [
                'documentable_type' => Scholar::class,
                'documentable_id' => $scholar->id,
                'status' => 'active',
                'date_issued' => '2026-08-01',
            ],
            [
                'file_type_id' => $fileType->id,
                'original_filename' => 'award.pdf',
                'stored_filename' => 'award.pdf',
                'file_path' => 'documents/award.pdf',
                'mime_type' => 'application/pdf',
                'file_size_bytes' => 1024,
                'uploaded_by' => $user->id,
            ]
        );

        $this->assertSame('2026-08-01', $document->fresh()->date_issued?->format('Y-m-d'));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'created',
            'record_type' => Document::class,
            'record_id' => $document->id,
        ]);

        $document->update(['date_issued' => '2026-08-13']);

        $this->assertTrue(
            AuditLog::query()
                ->where('action', 'updated')
                ->where('record_type', Document::class)
                ->where('record_id', $document->id)
                ->exists()
        );

        $this->assertSame('2026-08-13', $document->fresh()->date_issued?->format('Y-m-d'));
    }

    public function test_skips_audit_when_unauthenticated(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('Encoder');

        $scholarship = Scholarship::create(['name' => 'RA 7687', 'is_available' => true]);
        $scholarshipType = ScholarshipType::create(['name' => 'Undergrad', 'is_available' => true]);
        $school = School::create(['name' => 'Test U', 'is_available' => true]);
        $course = Course::create(['name' => 'BS CS', 'abbreviation' => 'BSCS', 'is_available' => true]);
        $region = Region::create(['name' => 'XI', 'abbreviation' => 'R11', 'is_available' => true]);
        $status = ClearanceStatus::create(['name' => 'Active', 'is_available' => true]);
        $fileType = FileType::create(['name' => 'Notice of Award']);

        $scholar = Scholar::create([
            'first_name' => 'Ben',
            'last_name' => 'Cruz',
            'year_of_award' => 2024,
            'scholarship_id' => $scholarship->id,
            'scholarship_type_id' => $scholarshipType->id,
            'school_id' => $school->id,
            'course_id' => $course->id,
            'region_id' => $region->id,
            'clearance_status_id' => $status->id,
            'spas_no' => '2024-DOC-OBS-2',
        ]);

        $before = AuditLog::query()->count();

        $document = Document::createWithInitialVersion(
            [
                'documentable_type' => Scholar::class,
                'documentable_id' => $scholar->id,
                'status' => 'active',
            ],
            [
                'file_type_id' => $fileType->id,
                'original_filename' => 'silent.pdf',
                'stored_filename' => 'silent.pdf',
                'file_path' => 'documents/silent.pdf',
                'mime_type' => 'application/pdf',
                'file_size_bytes' => 512,
                'uploaded_by' => $user->id,
            ]
        );

        $this->assertNotNull($document->id);
        $this->assertSame($before, AuditLog::query()->where('record_type', Document::class)->count());
    }
}
