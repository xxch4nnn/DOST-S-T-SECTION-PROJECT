<?php

namespace Tests\Feature;

use App\Livewire\AddFile;
use App\Models\ClearanceStatus;
use App\Models\Course;
use App\Models\FileType;
use App\Models\Region;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarshipType;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AddFileScholarUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_scholar_file_with_scanned_categories_and_uploads()
    {
        $user = User::factory()->create();

        $scholarship = Scholarship::firstOrCreate(['name' => 'RA 10612', 'is_available' => true]);
        $scholarshipType = ScholarshipType::firstOrCreate(['name' => 'DOST - SEI Undergraduate Scholarship', 'is_available' => true]);
        $school = School::firstOrCreate(['name' => 'University of Southeastern Philippines', 'is_available' => true]);
        $course = Course::firstOrCreate(['name' => 'BS Computer Science', 'abbreviation' => 'BSCS', 'is_available' => true]);
        $region = Region::firstOrCreate(['name' => 'Region XI', 'abbreviation' => 'R11', 'is_available' => true]);
        $status = ClearanceStatus::firstOrCreate(['name' => 'Not Cleared', 'is_available' => true]);

        FileType::firstOrCreate(['name' => 'Amendatory Agreement', 'year' => '2023', 'metadata_template' => [], 'file_group_id' => \App\Models\FileGroup::firstOrCreate(['name' => 'Default Group', 'slug' => 'default-group'])->id]);
        FileType::firstOrCreate(['name' => 'Report of Grades', 'year' => '2023', 'metadata_template' => [], 'file_group_id' => \App\Models\FileGroup::firstOrCreate(['name' => 'Default Group', 'slug' => 'default-group'])->id]);

        $test = Livewire::actingAs($user)
            ->test(AddFile::class)
            ->call('selectFileType', 'scholar')
            ->set('first_name', 'Maria')
            ->set('last_name', 'Santos')
            ->set('middle_name', 'Clara')
            ->set('spas_no', '2023-TEST-0099')
            ->set('contact_number', '09123456789')
            ->set('email_address', 'test@test.com')
            ->set('year_of_award', '2023')
            ->set('scholarship_id', $scholarship->id)
            ->set('scholarship_type_id', $scholarshipType->id)
            ->set('school_id', $school->id)
            ->set('course_id', $course->id)
            ->set('region_id', $region->id)
            ->set('clearance_status_id', $status->id)
            ->call('addFileToCategory', 'cat_1', [
                'id' => 'file_test_1',
                'name' => 'signed_agreement.pdf',
                'size' => 102400,
                'mime_type' => 'application/pdf',
                'is_pdf' => true,
                'is_image' => false,
                'data_url' => 'data:application/pdf;base64,'.base64_encode('PDF Content'),
            ])
            ->call('addScannedCategory');

        // Verify that adding category created second category
        $this->assertCount(2, $test->get('scannedCategories'));

        $secondCatId = $test->get('scannedCategories')[1]['id'];

        $test->call('addFileToCategory', $secondCatId, [
            'id' => 'file_test_2',
            'name' => 'grade_sheet.png',
            'size' => 204800,
            'mime_type' => 'image/png',
            'is_pdf' => false,
            'is_image' => true,
            'data_url' => 'data:image/png;base64,'.base64_encode('PNG Content'),
        ])
            ->call('saveScholar')
            ->assertHasNoErrors();

        // Verify scholar created in database
        $this->assertDatabaseHas('scholars', [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'spas_no' => '2023-TEST-0099',
        ]);

        $scholar = Scholar::where('spas_no', '2023-TEST-0099')->first();
        $this->assertNotNull($scholar);

        // Verify documents created and linked to scholar
        $this->assertEquals(2, $scholar->documents()->count());
        $this->assertDatabaseHas('document_versions', [
            'original_filename' => 'signed_agreement.pdf',
        ]);
        $this->assertDatabaseHas('document_versions', [
            'original_filename' => 'grade_sheet.png',
        ]);

        // Verify file was saved in storage
        $storedDocs = $scholar->documents()->with('currentVersion')->get();
        foreach ($storedDocs as $doc) {
            Storage::disk('local')->assertExists('documents/'.$doc->stored_filename);
            Storage::disk('local')->delete('documents/'.$doc->stored_filename);
        }
    }

    public function test_file_addition_removal_and_reordering()
    {
        $user = User::factory()->create();

        $test = Livewire::actingAs($user)
            ->test(AddFile::class)
            ->call('selectFileType', 'scholar')
            ->call('addFileToCategory', 'cat_1', [
                'id' => 'file_1',
                'name' => 'first.pdf',
                'size' => 1024,
                'mime_type' => 'application/pdf',
                'is_pdf' => true,
            ])
            ->call('addFileToCategory', 'cat_1', [
                'id' => 'file_2',
                'name' => 'second.pdf',
                'size' => 2048,
                'mime_type' => 'application/pdf',
                'is_pdf' => true,
            ]);

        $categories = $test->get('scannedCategories');
        $this->assertCount(2, $categories[0]['files']);
        $this->assertEquals('first.pdf', $categories[0]['files'][0]['name']);
        $this->assertEquals('second.pdf', $categories[0]['files'][1]['name']);

        // Test reordering
        $test->call('reorderFiles', 'cat_1', ['file_2', 'file_1']);
        $categoriesReordered = $test->get('scannedCategories');
        $this->assertEquals('second.pdf', $categoriesReordered[0]['files'][0]['name']);
        $this->assertEquals('first.pdf', $categoriesReordered[0]['files'][1]['name']);

        // Test removal by ID
        $test->call('removeFileById', 'cat_1', 'file_2');
        $categoriesAfterRemoval = $test->get('scannedCategories');
        $this->assertCount(1, $categoriesAfterRemoval[0]['files']);
        $this->assertEquals('first.pdf', $categoriesAfterRemoval[0]['files'][0]['name']);
    }

    public function test_process_pending_uploads_with_uploaded_files_and_thumbnails()
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $file1 = UploadedFile::fake()->create('contract.pdf', 300, 'application/pdf');
        $file2 = UploadedFile::fake()->image('avatar.png', 200, 200);

        $test = Livewire::actingAs($user)
            ->test(AddFile::class)
            ->call('selectFileType', 'scholar')
            ->set('pendingUploads', [$file1, $file2])
            ->call('processPendingUploads', 'cat_1', [
                'contract.pdf' => 'data:image/jpeg;base64,pdfthumb',
                'avatar.png' => 'data:image/png;base64,imgthumb',
            ]);

        $categories = $test->get('scannedCategories');
        $this->assertCount(2, $categories[0]['files']);
        $this->assertEquals('contract.pdf', $categories[0]['files'][0]['name']);
        $this->assertTrue($categories[0]['files'][0]['is_pdf']);
        $this->assertEquals('avatar.png', $categories[0]['files'][1]['name']);
        $this->assertTrue($categories[0]['files'][1]['is_image']);
        $this->assertNotNull($categories[0]['files'][0]['thumbnail_url']);
        $this->assertNotNull($categories[0]['files'][1]['thumbnail_url']);
    }

    public function test_save_scholar_with_staged_files_creates_scholar_and_documents()
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $scholarship = Scholarship::firstOrCreate(['name' => 'RA 7687', 'is_available' => true]);
        $scholarshipType = ScholarshipType::firstOrCreate(['name' => 'DOST - SEI Undergraduate Scholarship', 'is_available' => true]);
        $school = School::firstOrCreate(['name' => 'Ateneo de Davao University', 'is_available' => true]);
        $course = Course::firstOrCreate(['name' => 'BS Information Technology', 'abbreviation' => 'BSIT', 'is_available' => true]);
        $region = Region::firstOrCreate(['name' => 'Region XI', 'abbreviation' => 'R11', 'is_available' => true]);
        $status = ClearanceStatus::firstOrCreate(['name' => 'Not Cleared', 'is_available' => true]);

        FileType::firstOrCreate(['name' => 'Scholarship Agreement', 'year' => '2024', 'metadata_template' => [], 'file_group_id' => \App\Models\FileGroup::firstOrCreate(['name' => 'Default Group', 'slug' => 'default-group'])->id]);
        FileType::firstOrCreate(['name' => 'Certificate of Registration', 'year' => '2024', 'metadata_template' => [], 'file_group_id' => \App\Models\FileGroup::firstOrCreate(['name' => 'Default Group', 'slug' => 'default-group'])->id]);

        $file1 = UploadedFile::fake()->create('agreement.pdf', 500, 'application/pdf');
        $file2 = UploadedFile::fake()->image('cor.jpg', 300, 300);

        $manifest = [
            [
                'index' => 0,
                'cat_id' => 'cat_1',
                'cat_name' => 'Scholarship Agreement',
                'name' => 'agreement.pdf',
                'file_size' => 500 * 1024,
                'mime_type' => 'application/pdf',
                'is_pdf' => true,
                'is_image' => false,
            ],
            [
                'index' => 1,
                'cat_id' => 'cat_2',
                'cat_name' => 'Certificate of Registration',
                'name' => 'cor.jpg',
                'file_size' => 300 * 1024,
                'mime_type' => 'image/jpeg',
                'is_pdf' => false,
                'is_image' => true,
            ],
        ];

        $test = Livewire::actingAs($user)
            ->test(AddFile::class)
            ->call('selectFileType', 'scholar')
            ->set('first_name', 'Juan')
            ->set('last_name', 'Dela Cruz')
            ->set('middle_name', 'Protacio')
            ->set('spas_no', '2024-STAGED-001')
            ->set('contact_number', '09123456789')
            ->set('email_address', 'test@test.com')
            ->set('year_of_award', '2024')
            ->set('scholarship_id', $scholarship->id)
            ->set('scholarship_type_id', $scholarshipType->id)
            ->set('school_id', $school->id)
            ->set('course_id', $course->id)
            ->set('region_id', $region->id)
            ->set('clearance_status_id', $status->id)
            ->set('pendingUploads', [$file1, $file2])
            ->call('saveScholarWithStagedFiles', $manifest)
            ->assertHasNoErrors();

        // Verify scholar created in database
        $this->assertDatabaseHas('scholars', [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'spas_no' => '2024-STAGED-001',
        ]);

        $scholar = Scholar::where('spas_no', '2024-STAGED-001')->first();
        $this->assertNotNull($scholar);
        $test->assertRedirect(route('scholars.index', ['open_scholar' => $scholar->id]));

        $fileTypeAgreement = FileType::where('name', 'Scholarship Agreement')->first();
        $fileTypeCor = FileType::where('name', 'Certificate of Registration')->first();

        // Verify documents created and linked to scholar
        $this->assertEquals(2, $scholar->documents()->count());
        $this->assertDatabaseHas('document_versions', [
            'original_filename' => 'agreement.pdf',
            'file_type_id' => $fileTypeAgreement->id,
        ]);
        $this->assertDatabaseHas('document_versions', [
            'original_filename' => 'cor.jpg',
            'file_type_id' => $fileTypeCor->id,
        ]);
    }
}
