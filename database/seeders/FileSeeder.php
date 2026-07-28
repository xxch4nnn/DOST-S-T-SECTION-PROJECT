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
                'file_name'=>'',
                'file_path'=>'',
                'file_size'=>'',
                'uploaded_at'=>'',
                'updated_at'=>'',
                'mime_type'=>'',
                'metadata'=>''
            ]
        ];

        foreach($groups as $group){
            // Load PDF from sample_pdf folder
            // Prepare the file name & file size
            // Get the extension
            // Store the file locally and save the file path
            
            /**
             * $table->id();
             * $table->string('file_name', 200)->unique()->nullable(false);
             * $table->string('file_path', 500)->unique()->nullable(false);
             * $table->integer('file_size')->nullable(false);
             * $table->timestamp('uploaded_at')->useCurrent();
             * $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
             * $table->timestamp('deleted_at')->nullable(true);
             * $table->string('mime_type', 50)->nullable(false);
             * $table->json('metadata')->nullable(false);
             */

            File::firstOrCreate($group);
        }
    }
}
