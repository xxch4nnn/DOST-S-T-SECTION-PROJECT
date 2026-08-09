<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\FileType;
use App\Models\Scholar;
use App\Models\User;
use App\Support\SamplePdfFixture;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Optional demo fixtures from database/sample_pdfs (relative paths only).
 * Skips quietly when PDFs or a scholar are missing.
 */
class DocumentFixtureSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'test@example.com')->first()
            ?? User::query()->first();
        $scholar = Scholar::query()->first();
        $fileType = FileType::query()->where('name', 'Certificate of Registration')->first()
            ?? FileType::query()->first();

        if (! $user || ! $scholar || ! $fileType) {
            return;
        }

        $samples = array_slice(SamplePdfFixture::all(), 0, 4);
        if ($samples === []) {
            return;
        }

        foreach ($samples as $sourcePath) {
            $extension = pathinfo($sourcePath, PATHINFO_EXTENSION) ?: 'pdf';
            $stored = Str::uuid()->toString().'.'.$extension;
            $relative = 'documents/'.$stored;
            Storage::disk('local')->put($relative, file_get_contents($sourcePath));

            $exists = Document::query()
                ->where('documentable_type', Scholar::class)
                ->where('documentable_id', $scholar->id)
                ->whereHas('currentVersion', fn ($q) => $q->where('original_filename', basename($sourcePath)))
                ->exists();

            if ($exists) {
                continue;
            }

            Document::createWithInitialVersion(
                [
                    'documentable_type' => Scholar::class,
                    'documentable_id' => $scholar->id,
                    'status' => 'active',
                    'metadata' => null,
                ],
                [
                    'file_type_id' => $fileType->id,
                    'original_filename' => basename($sourcePath),
                    'stored_filename' => $stored,
                    'file_path' => $relative,
                    'mime_type' => 'application/pdf',
                    'file_size_bytes' => filesize($sourcePath) ?: 1024,
                    'uploaded_by' => $user->id,
                ]
            );
        }
    }
}
