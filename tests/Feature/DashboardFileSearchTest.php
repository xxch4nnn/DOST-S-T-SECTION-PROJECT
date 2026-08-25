<?php

namespace Tests\Feature;

use App\Models\ClearanceStatus;
use App\Models\Course;
use App\Models\Document;
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
use Livewire\Volt\Volt;
use Tests\TestCase;

class DashboardFileSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_matches_scholars_by_document_version_filename(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('Encoder');

        $scholarship = Scholarship::create(['name' => 'RA 7687', 'is_available' => true]);
        $scholarshipType = ScholarshipType::create(['name' => 'Undergrad', 'is_available' => true]);
        $school = School::create(['name' => 'Search U', 'is_available' => true]);
        $course = Course::create(['name' => 'BS CS', 'abbreviation' => 'BSCS', 'is_available' => true]);
        $region = Region::create(['name' => 'XI', 'abbreviation' => 'R11', 'is_available' => true]);
        $status = ClearanceStatus::create(['name' => 'Active', 'is_available' => true]);
        $fileGroup = FileGroup::create(['name' => 'Scholarly Documents', 'slug' => 'scholarly_documents']);
        $fileType = FileType::create(['name' => 'Notice of Award', 'file_group_id' => $fileGroup->id]);

        $scholar = Scholar::create([
            'first_name' => 'Luna',
            'last_name' => 'Cruz',
            'year_of_award' => 2024,
            'scholarship_id' => $scholarship->id,
            'scholarship_type_id' => $scholarshipType->id,
            'school_id' => $school->id,
            'course_id' => $course->id,
            'region_id' => $region->id,
            'clearance_status_id' => $status->id,
            'spas_no' => '2024-SEARCH-1',
        ]);

        Document::createWithInitialVersion(
            [
                'documentable_type' => Scholar::class,
                'documentable_id' => $scholar->id,
                'status' => 'active',
            ],
            [
                'file_type_id' => $fileType->id,
                'original_filename' => 'unique_award_packet.pdf',
                'stored_filename' => 'unique_award_packet.pdf',
                'file_path' => 'documents/unique_award_packet.pdf',
                'mime_type' => 'application/pdf',
                'file_size_bytes' => 2048,
                'uploaded_by' => $user->id,
            ]
        );

        Volt::actingAs($user)
            ->test('dashboard.file-search')
            ->set('query', 'unique_award_packet')
            ->assertSee('Cruz')
            ->assertSee('Luna')
            ->assertDontSee('Maclang');
    }

    public function test_recent_searches_start_empty_and_clear_methods_work(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('Encoder');

        Volt::actingAs($user)
            ->test('dashboard.file-search')
            ->assertSet('recentSearches', [])
            ->assertDontSee('Fernandez')
            ->assertDontSee('2023-00855-9102')
            ->set('recentSearches', [
                [
                    'id' => 1,
                    'last_name' => 'Sample',
                    'first_name' => 'Test',
                    'spas_no' => '0000-00000-0000',
                    'program_type' => 'RA 10612',
                    'program_level' => 'Undergrad',
                    'status' => 'Not Cleared',
                ],
            ])
            ->call('clearRecentSearch', 1)
            ->assertSet('recentSearches', [])
            ->set('recentSearches', [
                [
                    'id' => 2,
                    'last_name' => 'Sample',
                    'first_name' => 'Two',
                    'spas_no' => '0000-00000-0001',
                    'program_type' => 'RA 10612',
                    'program_level' => 'Undergrad',
                    'status' => 'Not Cleared',
                ],
            ])
            ->call('clearAllRecentSearches')
            ->assertSet('recentSearches', []);
    }
}
