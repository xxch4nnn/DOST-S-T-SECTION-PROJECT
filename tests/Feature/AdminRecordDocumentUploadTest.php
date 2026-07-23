<?php

namespace Tests\Feature;

use App\Livewire\AdminRecords\Show;
use App\Models\AdministrativeRecord;
use App\Models\FileType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class AdminRecordDocumentUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_allows_users_to_upload_admin_documents_with_uuid_hashing()
    {
        // Avoid Storage::fake('local') — it breaks Livewire temporary upload metadata (LW4 + Flysystem).
        $user = User::factory()->create();
        $fileType = FileType::firstOrCreate(['name' => 'Memorandum Circular', 'year' => '2023']);

        $record = AdministrativeRecord::create([
            'record_type' => 'Memorandum',
            'series_number' => 'Memo-2023-12',
            'title' => 'Guidelines on Scholarship Grants',
            'recipient' => 'All Regional Coordinators',
            'year' => 2023,
            'quarter' => 'Q3',
            'created_by' => $user->id,
        ]);

        $file = UploadedFile::fake()->create('admin_memo.pdf', 100, 'application/pdf');

        Livewire::actingAs($user)
            ->test(Show::class, ['record' => $record])
            ->set('file', $file)
            ->set('file_type_id', $fileType->id)
            ->call('uploadDocument')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('documents', [
            'original_filename' => 'admin_memo.pdf',
            'documentable_id' => $record->id,
            'documentable_type' => AdministrativeRecord::class,
        ]);

        $document = $record->fresh()->documents()->first();

        // Stored filename must be a UUID (ADR-005)
        $this->assertNotEquals('admin_memo.pdf', $document->stored_filename);
        $this->assertTrue(Str::isUuid(pathinfo($document->stored_filename, PATHINFO_FILENAME)));

        // File must be stored correctly
        Storage::disk('local')->assertExists('documents/'.$document->stored_filename);
        Storage::disk('local')->delete('documents/'.$document->stored_filename);
    }
}
