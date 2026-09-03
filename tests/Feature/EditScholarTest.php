<?php

namespace Tests\Feature;

use App\Livewire\Scholars\Edit;
use App\Models\AdministrativeRecord;
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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class EditScholarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_edit_scholar_page_renders_with_prefilled_scholar_data()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $scholarship = Scholarship::create(['name' => 'RA 7687', 'is_available' => true]);
        $scholarshipType = ScholarshipType::create(['name' => 'Undergraduate', 'is_available' => true]);
        $school = School::create(['name' => 'University of the Philippines', 'is_available' => true]);
        $course = Course::create(['name' => 'BS Computer Science', 'abbreviation' => 'BSCS', 'is_available' => true]);
        $region = Region::create(['name' => 'Region XI', 'abbreviation' => 'R11', 'is_available' => true]);
        $status = ClearanceStatus::create(['name' => 'Cleared', 'is_available' => true]);

        $scholar = Scholar::create([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'spas_no' => '2024-001',
            'contact_number' => '09170000001',
            'year_of_award' => '2024',
            'scholarship_id' => $scholarship->id,
            'scholarship_type_id' => $scholarshipType->id,
            'school_id' => $school->id,
            'course_id' => $course->id,
            'region_id' => $region->id,
            'clearance_status_id' => $status->id,
        ]);

        $fileType = FileType::create(['name' => 'Scholarship Agreement']);
        $doc = Document::createWithInitialVersion([
            'documentable_type' => Scholar::class,
            'documentable_id' => $scholar->id,
            'status' => 'active',
        ], [
            'file_type_id' => $fileType->id,
            'original_filename' => 'agreement.pdf',
            'stored_filename' => 'doc_test.pdf',
            'file_path' => 'documents/'.'doc_test.pdf',
            'mime_type' => 'application/pdf',
            'file_size_kb' => 100,
            'uploaded_by' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(Edit::class, ['scholar' => $scholar])
            ->assertOk()
            ->assertSet('first_name', 'Juan')
            ->assertSet('last_name', 'Dela Cruz')
            ->assertSet('spas_no', '2024-001')
            ->assertSee('agreement.pdf');
    }

    public function test_save_scholar_updates_scholar_profile_and_redirects()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $scholarship = Scholarship::create(['name' => 'RA 7687', 'is_available' => true]);
        $scholarshipType = ScholarshipType::create(['name' => 'Undergraduate', 'is_available' => true]);
        $school = School::create(['name' => 'University of the Philippines', 'is_available' => true]);
        $course = Course::create(['name' => 'BS Computer Science', 'abbreviation' => 'BSCS', 'is_available' => true]);
        $region = Region::create(['name' => 'Region XI', 'abbreviation' => 'R11', 'is_available' => true]);
        $status = ClearanceStatus::create(['name' => 'Cleared', 'is_available' => true]);

        $scholar = Scholar::create([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'spas_no' => '2024-001',
            'contact_number' => '09170000002',
            'year_of_award' => '2024',
            'scholarship_id' => $scholarship->id,
            'scholarship_type_id' => $scholarshipType->id,
            'school_id' => $school->id,
            'course_id' => $course->id,
            'region_id' => $region->id,
            'clearance_status_id' => $status->id,
        ]);

        Livewire::actingAs($user)
            ->test(Edit::class, ['scholar' => $scholar])
            ->set('first_name', 'Maria')
            ->set('last_name', 'Santos')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('scholars.index', ['open_scholar' => $scholar->id]));

        $this->assertDatabaseHas('scholars', [
            'id' => $scholar->id,
            'first_name' => 'Maria',
            'last_name' => 'Santos',
        ]);
    }

    public function test_update_scholar_with_new_staged_file_and_deleted_existing_document()
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $scholarship = Scholarship::create(['name' => 'RA 7687', 'is_available' => true]);
        $scholarshipType = ScholarshipType::create(['name' => 'Undergraduate', 'is_available' => true]);
        $school = School::create(['name' => 'University of the Philippines', 'is_available' => true]);
        $course = Course::create(['name' => 'BS Computer Science', 'abbreviation' => 'BSCS', 'is_available' => true]);
        $region = Region::create(['name' => 'Region XI', 'abbreviation' => 'R11', 'is_available' => true]);
        $status = ClearanceStatus::create(['name' => 'Cleared', 'is_available' => true]);

        $scholar = Scholar::create([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'spas_no' => '2024-001',
            'contact_number' => '09170000003',
            'year_of_award' => '2024',
            'scholarship_id' => $scholarship->id,
            'scholarship_type_id' => $scholarshipType->id,
            'school_id' => $school->id,
            'course_id' => $course->id,
            'region_id' => $region->id,
            'clearance_status_id' => $status->id,
        ]);

        $fileType = FileType::create(['name' => 'Scholarship Agreement']);
        $oldDoc = Document::createWithInitialVersion([
            'documentable_type' => Scholar::class,
            'documentable_id' => $scholar->id,
            'status' => 'active',
        ], [
            'file_type_id' => $fileType->id,
            'original_filename' => 'old_agreement.pdf',
            'stored_filename' => 'doc_old.pdf',
            'file_path' => 'documents/'.'doc_old.pdf',
            'mime_type' => 'application/pdf',
            'file_size_kb' => 100,
            'uploaded_by' => $user->id,
        ]);

        $newUploadedFile = UploadedFile::fake()->create('new_cor.pdf', 200, 'application/pdf');

        $manifest = [
            [
                'index' => 0,
                'cat_id' => 'cat_1',
                'cat_name' => 'Certificate of Registration',
                'name' => 'new_cor.pdf',
                'file_size' => 200 * 1024,
                'mime_type' => 'application/pdf',
                'is_pdf' => true,
                'is_image' => false,
                'is_existing' => false,
            ],
        ];

        $test = Livewire::actingAs($user)
            ->test(Edit::class, ['scholar' => $scholar])
            ->call('deleteExistingDocument', $oldDoc->id)
            ->set('pendingUploads', [$newUploadedFile])
            ->call('saveScholarWithStagedFiles', $manifest)
            ->assertHasNoErrors()
            ->assertRedirect(route('scholars.index', ['open_scholar' => $scholar->id]));

        // Check old doc was deleted
        $this->assertSoftDeleted('documents', [
            'id' => $oldDoc->id,
        ]);

        // Check new doc was created
        $this->assertDatabaseHas('documents', [
            'documentable_id' => $scholar->id,
            'documentable_type' => Scholar::class,
        ]);
        $this->assertDatabaseHas('document_versions', [
            'original_filename' => 'new_cor.pdf',
        ]);
    }

    public function test_delete_existing_document_rejects_cross_morph_documents()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $scholarship = Scholarship::create(['name' => 'RA 7687', 'is_available' => true]);
        $scholarshipType = ScholarshipType::create(['name' => 'Undergraduate', 'is_available' => true]);
        $school = School::create(['name' => 'University of the Philippines', 'is_available' => true]);
        $course = Course::create(['name' => 'BS Computer Science', 'abbreviation' => 'BSCS', 'is_available' => true]);
        $region = Region::create(['name' => 'Region XI', 'abbreviation' => 'R11', 'is_available' => true]);
        $status = ClearanceStatus::create(['name' => 'Cleared', 'is_available' => true]);

        $scholar = Scholar::create([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'spas_no' => '2024-001',
            'contact_number' => '09170000004',
            'year_of_award' => '2024',
            'scholarship_id' => $scholarship->id,
            'scholarship_type_id' => $scholarshipType->id,
            'school_id' => $school->id,
            'course_id' => $course->id,
            'region_id' => $region->id,
            'clearance_status_id' => $status->id,
        ]);

        $adminRecord = AdministrativeRecord::create([
            'record_type' => 'Memorandum',
            'series_number' => 'Memo-2024-01',
            'title' => 'Admin Record Test',
            'created_by' => $user->id,
        ]);

        $fileType = FileType::create(['name' => 'Administrative Order']);
        $adminDoc = Document::createWithInitialVersion([
            'documentable_type' => AdministrativeRecord::class,
            'documentable_id' => $adminRecord->id,
            'status' => 'active',
        ], [
            'file_type_id' => $fileType->id,
            'original_filename' => 'admin_memo.pdf',
            'stored_filename' => 'doc_admin.pdf',
            'file_path' => 'documents/'.'doc_admin.pdf',
            'mime_type' => 'application/pdf',
            'file_size_kb' => 50,
            'uploaded_by' => $user->id,
        ]);

        $this->expectException(ModelNotFoundException::class);

        Livewire::actingAs($user)
            ->test(Edit::class, ['scholar' => $scholar])
            ->call('deleteExistingDocument', $adminDoc->id);
    }

    public function test_edit_scholar_throws_404_when_scholar_id_does_not_exist()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $this->expectException(ModelNotFoundException::class);

        Livewire::actingAs($user)
            ->test(Edit::class, ['scholar' => 99999]);
    }
}
