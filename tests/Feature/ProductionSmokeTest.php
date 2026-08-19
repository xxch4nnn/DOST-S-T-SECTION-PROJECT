<?php

namespace Tests\Feature;

use App\Livewire\Scholars\Show;
use App\Models\ClearanceStatus;
use App\Models\Course;
use App\Models\FileGroup;
use App\Models\FileType;
use App\Models\Region;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarshipType;
use App\Models\School;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Synthetic production smoke: health, auth, upload, download.
 * Run via: php artisan test --group=smoke
 */
#[Group('smoke')]
class ProductionSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_health_endpoint_returns_ok_json(): void
    {
        $this->get(route('health'))
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonStructure(['status', 'app', 'env', 'timestamp']);
    }

    public function test_login_page_and_authenticated_dashboard_smoke(): void
    {
        $this->get('/login')->assertOk();

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole('Encoder');

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_scholar_document_upload_and_download_smoke(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('Encoder');

        $scholarship = Scholarship::firstOrCreate(['name' => 'RA 7687', 'is_available' => true]);
        $scholarshipType = ScholarshipType::firstOrCreate(['name' => 'Undergrad', 'is_available' => true]);
        $school = School::firstOrCreate(['name' => 'Smoke U', 'campus' => 'Main', 'is_available' => true]);
        $course = Course::firstOrCreate(['name' => 'BS CS', 'abbreviation' => 'BSCS', 'is_available' => true]);
        $region = Region::firstOrCreate(['name' => 'NCR', 'abbreviation' => 'NCR', 'is_available' => true]);
        $status = ClearanceStatus::firstOrCreate(['name' => 'Active', 'is_available' => true]);
        $fileGroup = FileGroup::firstOrCreate(['name' => 'Smoke Group', 'slug' => 'smoke-group', 'is_available' => true]);
        $fileType = FileType::firstOrCreate(['name' => 'Smoke Award'],
            ['metadata_template' => [], 'file_group_id' => $fileGroup->id]);

        $scholar = Scholar::create([
            'first_name' => 'Smoke',
            'last_name' => 'Tester',
            'year_of_award' => 2024,
            'scholarship_id' => $scholarship->id,
            'scholarship_type_id' => $scholarshipType->id,
            'school_id' => $school->id,
            'course_id' => $course->id,
            'region_id' => $region->id,
            'clearance_status_id' => $status->id,
            'spas_no' => 'SMOKE-0001',
            'contact_number' => '09123456789',
            'email_address' => 'smoke01@example.com',
        ]);

        $file = UploadedFile::fake()->create('smoke.pdf', 50, 'application/pdf');

        Livewire::actingAs($user)
            ->test(Show::class, ['scholar' => $scholar])
            ->set('file', $file)
            ->set('file_type_id', $fileType->id)
            ->call('uploadDocument')
            ->assertHasNoErrors();

        $document = $scholar->fresh()->documents()->first();
        $this->assertNotNull($document);
        Storage::disk('local')->assertExists('documents/'.$document->stored_filename);

        $this->actingAs($user)
            ->get(route('documents.download', $document))
            ->assertOk();

        Storage::disk('local')->delete('documents/'.$document->stored_filename);
    }
}
