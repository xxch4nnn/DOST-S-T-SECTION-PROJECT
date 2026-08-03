<?php

namespace Database\Seeders;

use App\Models\File;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
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
        ];

        foreach($groups as $group){
            $sourcePath = $group['file_path'];
            if (!file_exists($sourcePath)) {
                continue;
            }

            $fileName = basename($sourcePath);
            $fileSize = filesize($sourcePath);
            $mimeType = 'application/pdf'; // All samples are PDFs

            // Store the file in local storage under documents/ using a unique UUID (ADR-005)
            $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
            $uuidName = \Illuminate\Support\Str::uuid() . '.' . $extension;
            $destinationRelativePath = 'documents/' . $uuidName;
            $destinationAbsolutePath = \Illuminate\Support\Facades\Storage::disk('local')->path($destinationRelativePath);

            // Ensure destination directory exists
            $destinationDir = dirname($destinationAbsolutePath);
            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            // Copy file to storage destination
            copy($sourcePath, $destinationAbsolutePath);

            // Determine file_type_id based on directory name
            $typeName = '';
            if (str_contains($sourcePath, 'Certificate_Of_Registration')) {
                $typeName = 'Certificate of Registration';
            } elseif (str_contains($sourcePath, 'Certificate_Of_Grades')) {
                $typeName = 'Certificate of Grades';
            }

            $fileType = \App\Models\FileType::where('name', $typeName)->first();
            $fileTypeId = $fileType ? $fileType->id : 1;

            File::firstOrCreate([
                'file_name' => $fileName,
                'file_type_id' => $fileTypeId,
                'file_path' => $destinationRelativePath,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'metadata' => [
                    'scholar_id' => $group['scholar_id']
                ],
            ]);
        }
    }
}
