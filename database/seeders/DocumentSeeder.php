<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\File;
use App\Models\FileType;
use App\Models\Scholar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            // #[Fillable(['file_type_id', 'file_name', 'file_path', 'file_size', 'uploaded_at', 'updated_at', 'deleted_at', 'mime_type', 'metadata'])]
            [
                'file_path'=>'C:\Users\Waks\Downloads\USeP Acads\3rd Year 3rd Sem\OJT\DOST System\DOST-S-T-SECTION-PROJECT\database\sample_pdfs\Certificate_Of_Registration\MACLANG_COR 3rd Year 2nd Sem.pdf',
                'scholar_id'=>1                
            ],
            [
                'file_path'=>'C:\Users\Waks\Downloads\USeP Acads\3rd Year 3rd Sem\OJT\DOST System\DOST-S-T-SECTION-PROJECT\database\sample_pdfs\Certificate_Of_Registration\MACLANG_COR 3rd Year 3rd Sem.pdf',
                'scholar_id'=>1                
            ],
            [
                'file_path'=>'C:\Users\Waks\Downloads\USeP Acads\3rd Year 3rd Sem\OJT\DOST System\DOST-S-T-SECTION-PROJECT\database\sample_pdfs\Certificate_Of_Grades\MACLANG_COG_1st Year 1st Sem.pdf',
                'scholar_id'=>1                
            ],
            [
                'file_path'=>'C:\Users\Waks\Downloads\USeP Acads\3rd Year 3rd Sem\OJT\DOST System\DOST-S-T-SECTION-PROJECT\database\sample_pdfs\Certificate_Of_Grades\MACLANG_COG_3rd Year 2nd Sem.pdf',
                'scholar_id'=>1                
            ],
            [
                'file_path'=>'C:\Users\Waks\Downloads\USeP Acads\3rd Year 3rd Sem\OJT\DOST System\DOST-S-T-SECTION-PROJECT\database\sample_pdfs\Certificate_Of_Grades\Memo-20260508-02-Conduct-of-Special-Internal-Elections-for-Clubs-and-Organizations (1).pdf',
                'scholar_id'=>1                
            ],
        ];

        foreach($groups as $group){
            $sourcePath = $group['file_path'];
            if (!file_exists($sourcePath)) {
                continue;
            }

            $fileName = basename($sourcePath);
            $fileSize = filesize($sourcePath);
            $mimeType = 'application/pdf'; // All samples are PDFs

            // Determine file_type_id based on directory name
            $typeName = '';
            if (str_contains($sourcePath, 'Certificate_Of_Registration')) {
                $typeName = 'Certificate of Registration';
            } elseif (str_contains($sourcePath, 'Certificate_Of_Grades')) {
                $typeName = 'Certificate of Grades';
            }


            // Store the file in local storage under documents/ using a unique UUID (ADR-005)
            $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
            $uuid = (string) \Illuminate\Support\Str::uuid();
            $uuidName = $uuid . '.' . $extension;
            $destinationRelativePath = 'documents/' . str_replace(' ', '_', $typeName) . '/' . $uuidName;
            $destinationAbsolutePath = \Illuminate\Support\Facades\Storage::disk('local')->path($destinationRelativePath);

            // Ensure destination directory exists
            $destinationDir = dirname($destinationAbsolutePath);
            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            // Copy file to storage destination
            copy($sourcePath, $destinationAbsolutePath);

            $fileType = FileType::where('name', $typeName)->first();
            $fileTypeId = $fileType ? $fileType->id : 1;

            Document::createWithInitialVersion(
                [
                    'uuid' => $uuid,
                    'documentable_type' => Scholar::class,
                    'documentable_id' => (string) $group['scholar_id'],
                    'status' => 'active',
                    'metadata' => [],
                ],
                [
                    'file_type_id' => $fileTypeId,
                    'original_filename' => $fileName,
                    'stored_filename' => $uuidName,
                    'file_path' => $destinationRelativePath,
                    'mime_type' => $mimeType,
                    'file_size_bytes' => $fileSize,
                    'version_number' => 1,
                    'uploaded_by' => 3,
                ]
            );
        }
    }
}
