<?php

namespace Tests\Feature;

use App\Livewire\Scholars\Show;
use App\Models\ClearanceStatus;
use App\Models\Course;
use App\Models\FileGroup;
use App\Models\FileType;
use App\Models\Region;
use App\Models\Scholar;
use App\Models\ScholarshipProgram;
use App\Models\ScholarshipProgramType;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class ScholarDocumentUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_allows_users_to_upload_documents_with_uuid_hashing()
    {
        // Avoid Storage::fake('local') — it breaks Livewire temporary upload metadata (LW4 + Flysystem).
        $user = User::factory()->create();

        $scholarship = ScholarshipProgram::firstOrCreate(['name' => 'RA 7687', 'is_available' => true]);
        $scholarshipType = ScholarshipProgramType::firstOrCreate(['name' => 'Undergrad', 'is_available' => true]);
        $school = School::firstOrCreate(['name' => 'Test University', 'campus' => 'Main', 'is_available' => true]);
        $course = Course::firstOrCreate(['name' => 'BS CS', 'abbreviation' => 'BSCS', 'is_available' => true]);
        $region = Region::firstOrCreate(['name' => 'NCR', 'abbreviation' => 'NCR', 'is_available' => true]);
        $status = ClearanceStatus::firstOrCreate(['name' => 'Active', 'is_available' => true]);
        
        $fileGroup = FileGroup::firstOrCreate([
            'name' => 'Scholarly Documents',
            'slug' => 'scholarly_documents'
        ]);
        $fileType = FileType::firstOrCreate([
            'name' => 'Notice of Award',
        ], [
            'file_group_id' => $fileGroup->id,
            'metadata_template' => [],
        ]);

        $scholar = Scholar::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'year_of_award' => 2023,
            'scholarship_program_id' => $scholarship->id,
            'scholarship_program_type_id' => $scholarshipType->id,
            'school_id' => $school->id,
            'course_id' => $course->id,
            'region_id' => $region->id,
            'clearance_status_id' => $status->id,
            'spas_number' => '2023-0001',
            'contact_number' => '09123456789',
        ]);

        $file = UploadedFile::fake()->create('test_document.pdf', 100, 'application/pdf');

        Livewire::actingAs($user)
            ->test(Show::class, ['scholar' => $scholar])
            ->set('file', $file)
            ->set('file_type_id', $fileType->id)
            ->call('uploadDocument')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('documents', [
            'original_filename' => 'test_document.pdf',
            'documentable_id' => $scholar->id,
            'documentable_type' => Scholar::class,
        ]);

        $document = clone $scholar->fresh()->documents()->first();

        // Security check: Stored filename must be a UUID
        $this->assertNotEquals('test_document.pdf', $document->stored_filename);
        $this->assertTrue(Str::isUuid(pathinfo($document->stored_filename, PATHINFO_FILENAME)));

        // File must be stored correctly
        Storage::disk('local')->assertExists('documents/'.$document->stored_filename);
        Storage::disk('local')->delete('documents/'.$document->stored_filename);
    }
}
