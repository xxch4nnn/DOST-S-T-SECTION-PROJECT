<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\ClearanceStatus;
use App\Models\Course;
use App\Models\Region;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarshipType;
use App\Models\School;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScholarObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_audit_log_with_record_columns_on_scholar_create_update(): void
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
            'spas_no' => '2024-OBS-1',
        'contact_number' => '09123456789', 'email_address' => 'test@example.com']);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'created',
            'record_type' => Scholar::class,
            'record_id' => $scholar->id,
        ]);

        $scholar->update(['first_name' => 'Anita']);

        $this->assertTrue(
            AuditLog::query()
                ->where('action', 'updated')
                ->where('record_type', Scholar::class)
                ->where('record_id', $scholar->id)
                ->exists()
        );
    }
}
