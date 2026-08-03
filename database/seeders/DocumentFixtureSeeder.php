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

            Document::query()->firstOrCreate(
                [
                    'documentable_type' => Scholar::class,
                    'documentable_id' => $scholar->id,
                    'original_filename' => basename($sourcePath),
                ],
                [
                    'file_type_id' => $fileType->id,
                    'stored_filename' => $stored,
                    'mime_type' => 'application/pdf',
                    'file_size_kb' => (int) max(1, (int) ceil(filesize($sourcePath) / 1024)),
                    'status' => 'active',
                    'metadata' => null,
                    'uploaded_by' => $user->id,
                ]
            );
        }
    }
}
